<?php

namespace App\Services;

use App\Models\Game;
use App\Models\Genre;
use App\Models\Platform;
use App\Models\User;
use App\Support\MediaLink;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class GameCsvImporter
{
    public const MAX_ROWS = 1000;

    private const REQUIRED = ['title', 'developer', 'release_year', 'genre'];
    private const BIO_LIMIT = 1000;
    private const MAX_IMAGES = 10;
    private const MAX_GENRES = 8;
    private const FALLBACK_GENRE = 'Lainnya';

    /** Genre names (lowercase) to skip, e.g. ['free to play']. Empty by default. */
    private const IGNORED_GENRES = [];

    /** Spellings that map to the platforms you already have. */
    private const PLATFORM_ALIASES = [
        'pc' => 'PC',
        'windows' => 'PC',
        'win' => 'PC',
        'steam' => 'PC',
        'ps5' => 'PlayStation 5',
        'playstation5' => 'PlayStation 5',
        'xbox' => 'Xbox Series X|S',
        'xboxseriesx' => 'Xbox Series X|S',
        'xboxseriesxs' => 'Xbox Series X|S',
        'switch' => 'Nintendo Switch',
        'nintendoswitch' => 'Nintendo Switch',
        'android' => 'Android',
        'ios' => 'iOS',
        'iphone' => 'iOS',
    ];

    private array $genreMap = [];
    private array $platformMap = [];
    private array $developerMap = [];
    private array $titleMap = [];
    private array $report = [];
    private string $passwordMode = 'pattern';
    private bool $showPasswords = true;

    /**
     * Reads the CSV and imports it inside one database transaction.
     * $commit = false is a dry run: everything is rolled back, but the report is the same.
     */
    public function run(string $path, string $passwordMode, bool $commit): array
    {
        $this->passwordMode = $passwordMode === 'random' ? 'random' : 'pattern';
        $this->showPasswords = $commit || $this->passwordMode === 'pattern';

        $this->report = [
            'fatal' => null,
            'committed' => $commit,
            'summary' => [
                'rows' => 0,
                'created' => 0,
                'skipped' => 0,
                'assigned' => 0,
                'errors' => 0,
                'developers_created' => 0,
                'developers_reused' => 0,
                'bios_filled' => 0,
                'genres_created' => 0,
                'platforms_created' => 0,
            ],
            'rows' => [],
            'developers' => [],
            'new_genres' => [],
            'new_platforms' => [],
        ];

        [$header, $records] = $this->readCsv($path);

        $missing = array_diff(self::REQUIRED, $header);
        if ($missing) {
            $this->report['fatal'] = 'Kolom wajib tidak ditemukan: ' . implode(', ', $missing) . '.';

            return $this->report;
        }
        if (count($records) === 0) {
            $this->report['fatal'] = 'File tidak berisi data.';

            return $this->report;
        }
        if (count($records) > self::MAX_ROWS) {
            $this->report['fatal'] = 'Maksimal ' . self::MAX_ROWS . ' baris per file. File ini berisi ' . count($records) . ' baris.';

            return $this->report;
        }

        $this->report['summary']['rows'] = count($records);

        DB::beginTransaction();

        try {
            $this->preload();
            $valid = $this->validateRows($records);

            // Pass 1: developers. Pass 2: games.
            $developers = $this->importDevelopers($valid);
            $this->importGames($valid, $developers);

            $commit ? DB::commit() : DB::rollBack();
        } catch (Throwable $e) {
            DB::rollBack();

            throw $e;
        }

        usort($this->report['rows'], fn($a, $b) => $a['no'] <=> $b['no']);

        return $this->report;
    }

    // ------------------------------------------------------------------ reading

    private function readCsv(string $path): array
    {
        $handle = fopen($path, 'r');
        if (!$handle) {
            return [[], []];
        }

        $first = fgets($handle);
        if ($first === false) {
            fclose($handle);

            return [[], []];
        }

        $first = preg_replace('/^\xEF\xBB\xBF/', '', $first); // UTF-8 BOM
        $delimiter = substr_count($first, ';') > substr_count($first, ',') ? ';' : ',';

        $header = array_map(
            fn($h) => Str::lower(trim($this->utf8((string) $h))),
            str_getcsv($first, $delimiter, '"', '\\')
        );

        $records = [];
        $number = 0;

        while (($data = fgetcsv($handle, 0, $delimiter, '"', '\\')) !== false) {
            $number++;

            if (count(array_filter($data, fn($v) => trim((string) $v) !== '')) === 0) {
                continue; // empty line
            }

            $row = [];
            foreach ($header as $i => $name) {
                $row[$name] = trim($this->utf8((string) ($data[$i] ?? '')));
            }

            $records[] = ['no' => $number, 'data' => $row];
        }

        fclose($handle);

        return [$header, $records];
    }

    private function utf8(string $value): string
    {
        return mb_check_encoding($value, 'UTF-8') ? $value : mb_convert_encoding($value, 'UTF-8', 'Windows-1252');
    }

    private function preload(): void
    {
        foreach (Genre::all() as $genre) {
            $this->genreMap[$this->key($genre->name)] = $genre;
        }

        foreach (Platform::all() as $platform) {
            $this->platformMap[$this->platformKey($platform->name)] = $platform;
        }

        foreach (User::role('developer')->get() as $developer) {
            $this->developerMap[$this->key($this->clean($developer->name))] = $developer;
        }

        foreach (Game::select('id', 'title', 'developer_id')->get() as $game) {
            $this->titleMap[$this->key($this->clean($game->title))] = [
                'id' => $game->id,
                'developer_id' => $game->developer_id,
            ];
        }
    }

    // --------------------------------------------------------------- validation

    private function validateRows(array $records): array
    {
        $valid = [];
        $seen = [];
        $maxYear = (int) date('Y') + 1;

        foreach ($records as $record) {
            $d = $record['data'];
            $title = $this->clean($d['title'] ?? '');
            $developer = $this->clean($d['developer'] ?? '');
            $year = $d['release_year'] ?? '';
            $notes = [];
            $errors = [];

            if ($title === '') {
                $errors[] = 'Judul kosong.';
            } elseif (mb_strlen($title) > 255) {
                $errors[] = 'Judul lebih dari 255 karakter.';
            }

            if ($developer === '') {
                $errors[] = 'Developer kosong.';
            } elseif (mb_strlen($developer) > 100) {
                $errors[] = 'Nama developer lebih dari 100 karakter.';
            }

            if (!ctype_digit($year) || (int) $year < 1950 || (int) $year > $maxYear) {
                $errors[] = 'Tahun rilis tidak valid ("' . $year . '").';
            }

            if ($errors) {
                $this->addRow($record['no'], $title !== '' ? $title : '(tanpa judul)', 'error', $errors);
                $this->report['summary']['errors']++;

                continue;
            }

            $key = $this->key($title);
            if (isset($seen[$key])) {
                $this->addRow($record['no'], $title, 'skipped', ['Judul ganda di dalam file, dilewati.']);
                $this->report['summary']['skipped']++;

                continue;
            }
            $seen[$key] = true;

            // Genres
            $genres = $this->unique($this->splitList($d['genre'] ?? '', '/[,;|]/'));
            $genres = array_values(array_filter(
                array_map(fn($g) => mb_substr($g, 0, 100), $genres),
                fn($g) => !in_array(mb_strtolower($g), self::IGNORED_GENRES, true)
            ));
            $genres = array_slice($genres, 0, self::MAX_GENRES);
            if (!$genres) {
                $genres = [self::FALLBACK_GENRE];
                $notes[] = 'Genre kosong, memakai "' . self::FALLBACK_GENRE . '".';
            }

            // Platforms
            $platforms = $this->unique($this->splitList($d['platforms'] ?? '', '/[,;|]/'));

            // Cover (portrait image link)
            $cover = null;
            $coverRaw = trim($d['cover_image'] ?? '');
            if ($coverRaw !== '') {
                if (!$this->isHttp($coverRaw)) {
                    $notes[] = 'Link cover tidak valid, diabaikan.';
                } elseif (MediaLink::youtubeId($coverRaw)) {
                    $notes[] = 'Cover berupa link video, diabaikan.';
                } else {
                    $cover = MediaLink::directImageUrl($coverRaw);
                }
            }

            // Gallery images
            $images = [];
            $badImages = 0;
            foreach ($this->splitList($d['images'] ?? '', '/\|/') as $url) {
                if (!$this->isHttp($url) || MediaLink::youtubeId($url)) {
                    $badImages++;

                    continue;
                }
                $images[] = MediaLink::directImageUrl($url);
            }
            $images = array_slice(array_values(array_unique($images)), 0, self::MAX_IMAGES);
            if ($badImages) {
                $notes[] = "{$badImages} link gambar tidak valid, diabaikan.";
            }

            // YouTube video
            $video = null;
            $videoRaw = trim($d['video'] ?? '');
            if ($videoRaw !== '') {
                $youtubeId = MediaLink::youtubeId($videoRaw);
                if ($youtubeId) {
                    $video = "https://www.youtube.com/watch?v={$youtubeId}";
                } else {
                    $notes[] = 'Video bukan link YouTube, diabaikan.';
                }
            }

            $valid[] = [
                'no' => $record['no'],
                'key' => $key,
                'title' => $title,
                'developer' => $developer,
                'bio' => trim($d['developer_description'] ?? ''),
                'year' => (int) $year,
                'genres' => $genres,
                'platforms' => $platforms,
                'cover' => $cover,
                'images' => $images,
                'video' => $video,
                'description' => ($d['description'] ?? '') !== '' ? $d['description'] : null,
                'notes' => $notes,
            ];
        }

        return $valid;
    }

    // -------------------------------------------------------------- pass 1: developers

    private function importDevelopers(array $rows): array
    {
        $wanted = [];

        foreach ($rows as $row) {
            $existing = $this->titleMap[$row['key']] ?? null;

            // A game that already has a developer account needs nothing from this file
            if ($existing && $existing['developer_id'] !== null) {
                continue;
            }

            $dk = $this->key($row['developer']);
            $wanted[$dk] ??= ['name' => $row['developer'], 'bio' => ''];

            if ($wanted[$dk]['bio'] === '' && $row['bio'] !== '') {
                $wanted[$dk]['bio'] = $row['bio'];
            }
        }

        $resolved = [];
        foreach ($wanted as $dk => $info) {
            $resolved[$dk] = $this->resolveDeveloper($dk, $info);
        }

        return $resolved;
    }

    private function resolveDeveloper(string $key, array $info): User
    {
        $bio = $this->limitBio($info['bio']);

        if ($existing = $this->developerMap[$key] ?? null) {
            if (blank($existing->bio) && $bio !== '') {
                $existing->update(['bio' => $bio]);
                $this->report['summary']['bios_filled']++;
            }

            $this->report['summary']['developers_reused']++;
            $this->report['developers'][] = [
                'name' => $existing->name,
                'email' => $existing->email,
                'password' => null,
                'status' => 'existing',
            ];

            return $existing;
        }

        // Email and password come from the developer name: symbols removed, spaces become "_"
        $base = Str::slug(preg_replace('/[^\p{L}\p{N}\s_-]+/u', '', $info['name']), '_') ?: 'developer';

        $email = "{$base}@dev.test";
        $n = 2;
        while (User::where('email', $email)->exists()) {
            $email = "{$base}_{$n}@dev.test";
            $n++;
        }

        $password = $this->passwordMode === 'random' ? Str::random(16) : "{$base}_password";

        $developer = User::create([
            'name' => $info['name'],
            'email' => $email,
            'password' => $password, // hashed by the model cast
            'bio' => $bio !== '' ? $bio : null,
        ]);
        $developer->assignRole('developer');

        $this->developerMap[$key] = $developer;
        $this->report['summary']['developers_created']++;
        $this->report['developers'][] = [
            'name' => $developer->name,
            'email' => $email,
            'password' => $this->showPasswords ? $password : null,
            'status' => 'created',
        ];

        return $developer;
    }

    // ----------------------------------------------------------------- pass 2: games

    private function importGames(array $rows, array $developers): void
    {
        foreach ($rows as $row) {
            $developer = $developers[$this->key($row['developer'])] ?? null;
            $existing = $this->titleMap[$row['key']] ?? null;

            if ($existing) {
                if ($existing['developer_id'] === null && $developer) {
                    Game::whereKey($existing['id'])->update([
                        'developer_id' => $developer->id,
                        'developer' => $developer->name,
                    ]);
                    $this->titleMap[$row['key']]['developer_id'] = $developer->id;

                    $this->addRow(
                        $row['no'],
                        $row['title'],
                        'assigned',
                        array_merge($row['notes'], ["Judul sudah ada. Developer {$developer->name} ditetapkan."])
                    );
                    $this->report['summary']['assigned']++;
                } else {
                    $this->addRow(
                        $row['no'],
                        $row['title'],
                        'skipped',
                        array_merge($row['notes'], ['Judul sudah ada, dilewati.'])
                    );
                    $this->report['summary']['skipped']++;
                }

                continue;
            }

            $game = Game::create([
                'title' => $row['title'],
                'developer' => $developer->name,
                'developer_id' => $developer->id,
                'release_year' => $row['year'],
                'cover_link' => $row['cover'],
                'description' => $row['description'],
            ]);

            $game->genres()->sync(array_map(fn($name) => $this->resolveGenre($name)->id, $row['genres']));
            $game->platforms()->sync(array_map(fn($name) => $this->resolvePlatform($name)->id, $row['platforms']));

            $position = 0;
            foreach ($row['images'] as $url) {
                $game->media()->create(['type' => 'image', 'source' => 'link', 'link_url' => $url, 'position' => $position++]);
            }
            if ($row['video']) {
                $game->media()->create(['type' => 'video', 'source' => 'link', 'link_url' => $row['video'], 'position' => $position++]);
            }

            $this->titleMap[$row['key']] = ['id' => $game->id, 'developer_id' => $developer->id];

            $this->addRow($row['no'], $row['title'], 'created', $row['notes']);
            $this->report['summary']['created']++;
        }
    }

    private function resolveGenre(string $name): Genre
    {
        $key = $this->key($name);

        if (!isset($this->genreMap[$key])) {
            $genre = Genre::firstOrCreate(['name' => $name]);
            $this->genreMap[$key] = $genre;

            if ($genre->wasRecentlyCreated) {
                $this->report['summary']['genres_created']++;
                $this->report['new_genres'][] = $genre->name;
            }
        }

        return $this->genreMap[$key];
    }

    private function resolvePlatform(string $name): Platform
    {
        $name = self::PLATFORM_ALIASES[$this->platformKey($name)] ?? $name;
        $key = $this->platformKey($name);

        if (!isset($this->platformMap[$key])) {
            $platform = Platform::firstOrCreate(['name' => mb_substr($name, 0, 100)]);
            $this->platformMap[$key] = $platform;

            if ($platform->wasRecentlyCreated) {
                $this->report['summary']['platforms_created']++;
                $this->report['new_platforms'][] = $platform->name;
            }
        }

        return $this->platformMap[$key];
    }

    // ----------------------------------------------------------------------- helpers

    private function addRow(int $no, string $title, string $status, array $notes = []): void
    {
        $this->report['rows'][] = ['no' => $no, 'title' => $title, 'status' => $status, 'notes' => $notes];
    }

    /** Removes trademark symbols and extra spaces. */
    private function clean(string $value): string
    {
        return trim(preg_replace('/\s+/u', ' ', preg_replace('/[™®©℠]/u', '', $value)));
    }

    /** Lowercase, single spaces: used to compare names. */
    private function key(string $value): string
    {
        return mb_strtolower(trim(preg_replace('/\s+/u', ' ', $value)));
    }

    /** Letters and digits only, so "Xbox Series X|S" and "xbox series xs" match. */
    private function platformKey(string $value): string
    {
        return preg_replace('/[^a-z0-9]/', '', mb_strtolower($value)) ?: $this->key($value);
    }

    private function splitList(string $value, string $pattern): array
    {
        return array_values(array_filter(
            array_map('trim', preg_split($pattern, $value) ?: []),
            fn($v) => $v !== ''
        ));
    }

    private function unique(array $items): array
    {
        $seen = [];
        $out = [];

        foreach ($items as $item) {
            $k = mb_strtolower($item);
            if (!isset($seen[$k])) {
                $seen[$k] = true;
                $out[] = $item;
            }
        }

        return $out;
    }

    private function isHttp(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false
            && in_array(strtolower((string) parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true);
    }

    private function limitBio(string $bio): string
    {
        $bio = trim($bio);

        return mb_strlen($bio) > self::BIO_LIMIT
            ? rtrim(mb_substr($bio, 0, self::BIO_LIMIT - 3)) . '...'
            : $bio;
    }
}