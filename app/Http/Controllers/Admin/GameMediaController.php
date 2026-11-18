<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GameMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Support\MediaLink;

class GameMediaController extends Controller
{
    private const IMAGE_LIMIT_MB = 10;
    private const VIDEO_LIMIT_MB = 100;

    public function index(Game $game)
    {
        $game->load('media');

        return view('admin.games.media', compact('game'));
    }

    public function store(Request $request, Game $game)
    {
        $request->validate([
            'files' => ['required', 'array', 'max:20'],
            'files.*' => [
                'file',
                'mimetypes:image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm,video/quicktime',
                'max:' . (self::VIDEO_LIMIT_MB * 1024),
            ],
        ], [
            'files.required' => 'Pilih minimal satu file.',
            'files.max' => 'Maksimal 20 file sekali unggah.',
            'files.*.uploaded' => 'Ada file yang gagal diunggah. Kemungkinan melebihi batas ukuran server.',
            'files.*.mimetypes' => 'Format file harus JPG, PNG, WEBP, GIF, MP4, WEBM, atau MOV.',
            'files.*.max' => 'Ukuran file terlalu besar.',
        ]);

        // Images max 10MB, videos max 100MB
        foreach ($request->file('files') as $file) {
            $isVideo = str_starts_with($file->getMimeType(), 'video/');
            $limit = $isVideo ? self::VIDEO_LIMIT_MB : self::IMAGE_LIMIT_MB;

            if ($file->getSize() > $limit * 1024 * 1024) {
                return back()->withErrors([
                    'files' => $file->getClientOriginalName() . " melebihi batas {$limit}MB untuk " . ($isVideo ? 'video' : 'gambar') . '.',
                ]);
            }
        }

        // New files go to the end of the order
        $position = (GameMedia::where('game_id', $game->id)->max('position') ?? -1) + 1;

        foreach ($request->file('files') as $file) {
            $game->media()->create([
                'type' => str_starts_with($file->getMimeType(), 'video/') ? 'video' : 'image',
                'path' => $file->store("games/{$game->id}", 'public'),
                'position' => $position++,
            ]);
        }

        return back()->with('success', count($request->file('files')) . ' file berhasil diunggah.');
    }

    public function reorder(Request $request, Game $game)
    {
        $order = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer'],
        ])['order'];

        foreach ($order as $position => $id) {
            GameMedia::where('game_id', $game->id)->whereKey($id)->update(['position' => $position]);
        }

        return response()->json(['ok' => true]);
    }

    public function destroy(GameMedia $media)
    {
        Storage::disk('public')->delete($media->path);
        if ($media->path) {
            Storage::disk('public')->delete($media->path);
        }
        $media->delete();

        return back()->with('success', 'Media berhasil dihapus.');
    }

    public function storeLink(Request $request, Game $game)
    {
        $data = $request->validate([
            'link' => ['required', 'url:http,https', 'max:2048'],
        ], [
            'link.required' => 'Tempel link terlebih dahulu.',
            'link.url' => 'Link tidak valid. Harus diawali http:// atau https://',
        ]);

        $youtubeId = MediaLink::youtubeId($data['link']);
        $position = (GameMedia::where('game_id', $game->id)->max('position') ?? -1) + 1;

        $game->media()->create([
            'type' => $youtubeId ? 'video' : 'image',
            'source' => 'link',
            'link_url' => $youtubeId
                ? "https://www.youtube.com/watch?v={$youtubeId}"
                : MediaLink::directImageUrl($data['link']),
            'position' => $position,
        ]);

        return back()->with('success', $youtubeId ? 'Video YouTube berhasil ditambahkan.' : 'Link gambar berhasil ditambahkan.');
    }
}