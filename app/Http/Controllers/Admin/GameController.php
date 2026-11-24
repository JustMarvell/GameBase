<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Genre;
use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Support\MediaLink;
use App\Models\User;

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
            'cover_focus_x' => ['nullable', 'integer', 'between:0,100'],
            'cover_focus_y' => ['nullable', 'integer', 'between:0,100'],
            'developer_id' => ['nullable', 'integer'],
        ];
    }

    public function index(Request $request)
    {
        // Only these columns can be sorted (prevents arbitrary column names in the URL)
        $sorts = [
            'title' => 'title',
            'year' => 'release_year',
            'rating' => 'reviews_avg_rating',
            'created' => 'created_at',
        ];
        $column = $sorts[$request->sort] ?? 'created_at';
        $dir = $request->dir === 'asc' ? 'asc' : 'desc';

        $games = Game::with(['genre', 'platforms'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->when($request->q, fn($query, $q) => $query->where(
                fn($w) => $w->where('title', 'like', "%{$q}%")
                    ->orWhere('developer', 'like', "%{$q}%")
            ))
            ->when($request->genre, fn($query, $id) => $query->where('genre_id', $id))
            ->when($request->platform, fn($query, $id) => $query->whereHas(
                'platforms',
                fn($p) => $p->where('platforms.id', $id)
            ))
            ->when($request->cover === 'none', fn($query) => $query
                ->whereNull('cover')->whereNull('cover_link'))
            ->when($request->cover === 'has', fn($query) => $query->where(
                fn($w) => $w->whereNotNull('cover')->orWhereNotNull('cover_link')
            ))
            ->when(!auth()->user()->hasRole('admin'), fn($query) => $query->where('developer_id', auth()->id()))
            ->orderBy($column, $dir)
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString(); // keeps filters when changing pages

        return view('admin.games.index', [
            'games' => $games,
            'genres' => Genre::orderBy('name')->get(),
            'platforms' => Platform::orderBy('name')->get(),
        ]);
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
        $data = $this->validated($request);

        $game = Game::create(collect($data)->except('platforms')->all());
        $game->platforms()->sync($request->input('platforms', []));

        return redirect()->route('admin.games.media.index', $game)
            ->with('success', 'Game berhasil ditambahkan. Silakan unggah galeri gambar/video.');
    }

    public function edit(Game $game)
    {
        $this->authorizeGame($game);
        $game->load('platforms');

        return view('admin.games.edit', [
            'game' => $game,
            'genres' => Genre::orderBy('name')->get(),
            'platforms' => Platform::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Game $game)
    {
        $this->authorizeGame($game);
        $data = $this->validated($request, $game);

        $game->update(collect($data)->except('platforms')->all());
        $game->platforms()->sync($request->input('platforms', []));

        return redirect()->route('admin.games.index')
            ->with('success', 'Game berhasil diperbarui.');
    }

    public function destroy(Game $game)
    {
        abort_unless(auth()->user()->hasRole('admin'), 403); // only admins delete games

        if ($game->cover) {
            Storage::disk('public')->delete($game->cover);
        }

        foreach ($game->media as $item) {
            if ($item->path) {
                Storage::disk('public')->delete($item->path);
            }
        }

        $game->delete();

        return back()->with('success', 'Game berhasil dihapus.');
    }

    /** The selected tab (cover_mode) decides whether the file or the link is used. */
    private function handleCover(Request $request, array $data, ?Game $game = null): array
    {
        $data['cover_focus_x'] = (int) ($data['cover_focus_x'] ?? 50);
        $data['cover_focus_y'] = (int) ($data['cover_focus_y'] ?? 50);
        
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

    private function authorizeGame(Game $game): void
    {
        abort_unless($game->isManagedBy(auth()->user()), 403);
    }

    /** Validates the request and decides the developer link (admin: chosen one, developer: themselves). */
    private function validated(Request $request, ?Game $game = null): array
    {
        $user = $request->user();
        $isAdmin = $user->hasRole('admin');

        $rules = $this->rules();
        if (!$isAdmin) {
            unset($rules['developer'], $rules['developer_id']);
        }

        $data = $this->handleCover($request, $request->validate($rules), $game);

        if (!$isAdmin) {
            $data['developer_id'] = $user->id;   // a developer always owns their games
            $data['developer'] = $user->name;
        } else {
            $developer = filled($data['developer_id'] ?? null)
                ? User::role('developer')->find($data['developer_id'])
                : null;

            $data['developer_id'] = $developer?->id;
            if ($developer) {
                $data['developer'] = $developer->name;
            }
        }

        return $data;
    }
}