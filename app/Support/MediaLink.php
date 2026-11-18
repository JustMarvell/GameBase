<?php

namespace App\Support;

class MediaLink
{
    /** Returns the 11-character YouTube video ID, or null if it's not a YouTube link. */
    public static function youtubeId(?string $url): ?string
    {
        $parts = parse_url(trim((string) $url));

        if (!$parts || empty($parts['host']) || !in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'])) {
            return null;
        }

        $host = strtolower(preg_replace('/^(www\.|m\.)/', '', $parts['host']));
        $segments = array_values(array_filter(explode('/', $parts['path'] ?? '')));
        $id = null;

        if ($host === 'youtu.be') {
            $id = $segments[0] ?? null;
        } elseif (in_array($host, ['youtube.com', 'youtube-nocookie.com'])) {
            if (($segments[0] ?? '') === 'watch') {
                parse_str($parts['query'] ?? '', $query);
                $id = $query['v'] ?? null;
            } elseif (in_array($segments[0] ?? '', ['shorts', 'embed', 'live', 'v'])) {
                $id = $segments[1] ?? null;
            }
        }

        return $id && preg_match('/^[A-Za-z0-9_-]{11}$/', $id) ? $id : null;
    }

    /** Turns a Google Drive share link into a direct image URL. Other links are returned unchanged. */
    public static function directImageUrl(string $url): string
    {
        $url = trim($url);

        if (
            preg_match('~drive\.google\.com/file/d/([A-Za-z0-9_-]+)~', $url, $m)
            || preg_match('~drive\.google\.com/(?:open|uc)\?(?:[^#]*&)?id=([A-Za-z0-9_-]+)~', $url, $m)
        ) {
            return "https://drive.google.com/thumbnail?id={$m[1]}&sz=w1600";
        }

        return $url;
    }
}