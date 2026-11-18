<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Genre;
use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $data = $request->validate($this->rules());

        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('covers', 'public');
        }

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
        $data = $request->validate($this->rules());

        if ($request->hasFile('cover')) {
            if ($game->cover) {
                Storage::disk('public')->delete($game->cover);
            }
            $data['cover'] = $request->file('cover')->store('covers', 'public');
        }

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
            Storage::disk('public')->delete($item->path);
        }

        $game->delete(); // pivot rows and reviews are removed by cascade

        return redirect()->route('admin.games.index')
            ->with('success', 'Game berhasil dihapus.');
    }
}