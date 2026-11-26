import csv, re, time, html
import requests
from yt_dlp import YoutubeDL

GAME_LIMIT = 1            # how many games to scrape
OUTPUT = "games.csv"
RAWG_KEY = ""              # optional: free key from rawg.io/apidocs for full platforms
HEADERS = {"User-Agent": "Mozilla/5.0"}
DEV_CACHE = {}  # avoids looking up the same developer twice

def get_app_ids(limit):
    ids, start = [], 0
    while len(ids) < limit:
        url = "https://store.steampowered.com/search/results/"
        params = {"filter": "topsellers", "category1": 998,  # 998 = games only
                  "start": start, "count": 50, "infinite": 1}
        data = requests.get(url, params=params, headers=HEADERS).json()
        found = re.findall(r'data-ds-appid="(\d+)"', data["results_html"])
        if not found:
            break
        ids += [i for i in found if i not in ids]
        start += 50
    return ids[:limit]

def get_steam_details(appid):
    url = "https://store.steampowered.com/api/appdetails"
    res = requests.get(url, params={"appids": appid, "l": "english"}, headers=HEADERS).json()
    entry = res.get(str(appid), {})
    if not entry.get("success") or entry["data"].get("type") != "game":
        return None
    d = entry["data"]

    year = re.search(r"\d{4}", d.get("release_date", {}).get("date", ""))
    plats = d.get("platforms", {})

    return {
        "appid": appid,
        "title": d.get("name", ""),
        "developer": ", ".join(d.get("developers", [])),
        "release_year": year.group() if year else "",
        "cover_image": get_cover(appid, d.get("header_image", "")),
        "description": html.unescape(d.get("short_description", "")),
        "genre": ", ".join(g["description"] for g in d.get("genres", [])),
        "platforms": "PC" if any(plats.values()) else "",
        "images": " | ".join(s["path_full"] for s in d.get("screenshots", [])[:5]),
        "steam_video": steam_video(d),
    }
    
def get_cover(appid, fallback):
    # portrait cover (600x900); old games may not have one
    url = f"https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/{appid}/library_600x900.jpg"
    try:
        if requests.head(url, timeout=5).status_code == 200:
            return url
    except requests.RequestException:
        pass
    return fallback  # landscape header image

def get_developer_description(dev_name):
    if not dev_name:
        return ""
    if dev_name in DEV_CACHE:
        return DEV_CACHE[dev_name]

    desc = ""
    wiki_headers = {"User-Agent": "GameScraper/1.0 (student project)"}
    try:
        # 1. search Wikipedia for the developer's page
        search = requests.get("https://en.wikipedia.org/w/api.php", headers=wiki_headers, params={
            "action": "query", "list": "search", "format": "json", "srlimit": 1,
            "srsearch": f"{dev_name} video game developer",
        }).json()
        results = search["query"]["search"]

        if results:
            # 2. get that page's short summary
            page = results[0]["title"].replace(" ", "_")
            summary = requests.get(
                f"https://en.wikipedia.org/api/rest_v1/page/summary/{page}",
                headers=wiki_headers).json()
            extract = summary.get("extract", "")

            # 3. only keep it if it's actually about a game company
            if re.search(r"game|developer|studio|publisher", extract, re.I):
                desc = extract
    except (requests.RequestException, KeyError, ValueError):
        pass

    DEV_CACHE[dev_name] = desc
    return desc

def steam_video(d):
    movies = d.get("movies", [])
    if not movies:
        return ""
    m = movies[0]
    return m.get("mp4", {}).get("max") or m.get("hls_h264", "")

def find_youtube_trailer(title):
    opts = {"quiet": True, "extract_flat": True, "skip_download": True}
    try:
        with YoutubeDL(opts) as ydl:
            info = ydl.extract_info(f"ytsearch1:{title} official trailer", download=False)
        entries = info.get("entries") or []
        if entries:
            return f"https://www.youtube.com/watch?v={entries[0]['id']}"
    except Exception:
        pass
    return ""

def get_rawg_platforms(title):
    if not RAWG_KEY:
        return ""
    try:
        r = requests.get("https://api.rawg.io/api/games",
                         params={"key": RAWG_KEY, "search": title, "page_size": 1}).json()
        game = r["results"][0]
        return ", ".join(p["platform"]["name"] for p in game.get("platforms") or [])
    except (KeyError, IndexError, requests.RequestException):
        return ""
    
FIELDS = ["title", "developer", "developer_description", "release_year", "cover_image",
    "description", "genre", "platforms", "images", "video"]

def main():
    ids = get_app_ids(GAME_LIMIT)
    print(f"Found {len(ids)} games")

    with open(OUTPUT, "w", newline="", encoding="utf-8") as f:
        writer = csv.DictWriter(f, fieldnames=FIELDS)
        writer.writeheader()

        for i, appid in enumerate(ids, 1):
            game = get_steam_details(appid)
            if not game:
                continue

            game["platforms"] = get_rawg_platforms(game["title"]) or game["platforms"]
            game["video"] = find_youtube_trailer(game["title"]) or game["steam_video"]
            first_dev = game["developer"].split(",")[0].strip()
            game["developer_description"] = get_developer_description(first_dev)

            writer.writerow({k: game[k] for k in FIELDS})
            print(f"[{i}/{len(ids)}] {game['title']}")
            time.sleep(1.5)  # Steam allows ~200 requests / 5 min

if __name__ == "__main__":
    main()