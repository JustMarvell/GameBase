# Game Scrapper
This is a simple scrapper made to scrape game data from Steam, Wiki, and Youtube
The scrapper will scrape game title, description, screenshoot/media, genre, release date, etc.

## How To Use

Install the required library
```python
pip install yt-dlp request
```
You can set the file name, number of games, and other stuff at the top of the script
```python
GAME_LIMIT = 100        # how many games to scrape
OUTPUT = "games.csv"    # the result file name
```

Run the scrapper
```python
python scrapper.py
```

## Result

The script will output a `.csv` file with this column :
```
title
developer
developer_description
release_year
cover_image
description
genre
platforms
images
video
```

## Optional
RawG API can also be added to scrape the game platform list. API key can be added in the top of the script at :
```python
RAWG_KEY = ""              # optional: free key from rawg.io/apidocs for full platforms
```
currently is not used because can't login to the site. will try again later when login is working.