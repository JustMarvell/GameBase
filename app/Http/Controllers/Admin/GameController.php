<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Genre;
use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Support\MediaLink;

class GameController extends Controller
{
    private function rules(): array
    {
        return [
            'title' => ['required', 'max:255'],
            'developer' => ['required', 'max:100'],
            'release_year' => ['required', 'integer', 'min:1950', 'max:' . (date('Y') + 1)],
            'genre_id' => ['required', 'exists:genres,id'],
            'platforms' => ['nullable', 'array'],
            'platforms.*' => ['exists:platforms,id'],
            'cover' => ['nullable', 'image', 'max:10240'],
            'cover_link' => [
                'nullable',
                'url:http,https',
                'max:2048',
                function ($attribute, $value, $fail) {
                    if ($value && MediaLink::youtubeId($value)) {
                        $fail('Cover harus berupa link gambar, bukan video.');
                    }
                }
            ],
            'description' => ['nullable', 'string'],
        ];
    }

    public function index()
    {
        $games = Game::with(['genre', 'platforms'])
            ->withAvg('reviews', 'rating')
            ->latest()
            ->paginate(10);

        return view('admin.games.index', compact('games'));
    }

    public function create()
    {
        return view('admin.games.create', [
            'genres' => Genre::orderBy('name')->get(),
            'platforms' => Platform::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->handleCover($request, $request->validate($this->rules()));

        $game = Game::create(collect($data)->except('platforms')->all());
        $game->platforms()->sync($request->input('platforms', []));

        return redirect()->route('admin.games.media.index', $game)
            ->with('success', 'Game berhasil ditambahkan. Silakan unggah galeri gambar/video.');
    }

    public function edit(Game $game)
    {
        $game->load('platforms');

        return view('admin.games.edit', [
            'game' => $game,
            'genres' => Genre::orderBy('name')->get(),
            'platforms' => Platform::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Game $game)
    {
        $data = $this->handleCover($request, $request->validate($this->rules()), $game);

        $game->update(collect($data)->except('platforms')->all());
        $game->platforms()->sync($request->input('platforms', []));

        return redirect()->route('admin.games.index')
            ->with('success', 'Game berhasil diperbarui.');
    }

    public function destroy(Game $game)
    {
        if ($game->cover) {
            Storage::disk('public')->delete($game->cover);
        }

        foreach ($game->media as $item) {
            if ($item->path) {
                Storage::disk('public')->delete($item->path);
            }
        }

        $game->delete(); // pivot rows and reviews are removed by cascade

        return redirect()->route('admin.games.index')
            ->with('success', 'Game berhasil dihapus.');
    }

    /** The selected tab (cover_mode) decides whether the file or the link is used. */
    private function handleCover(Request $request, array $data, ?Game $game = null): array
    {
        $mode = $request->input('cover_mode');

        if ($mode === 'link' && filled($data['cover_link'] ?? null)) {
            $this->deleteCoverFile($game);
            $data['cover'] = null;
            $data['cover_link'] = MediaLink::directImageUrl($data['cover_link']);
        } elseif ($mode === 'upload' && $request->hasFile('cover')) {
            $this->deleteCoverFile($game);
            $data['cover'] = $request->file('cover')->store('covers', 'public');
            $data['cover_link'] = null;
        } else {
            unset($data['cover'], $data['cover_link']); // keep the current cover
        }

        return $data;
    }

    private function deleteCoverFile(?Game $game): void
    {
        if ($game?->cover) {
            Storage::disk('public')->delete($game->cover);
        }
    }
}