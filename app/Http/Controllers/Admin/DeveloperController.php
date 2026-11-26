<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use App\Support\AccountDeleter;

class DeveloperController extends Controller
{
    public function index(Request $request)
    {
        $sorts = [
            'name' => 'users.name',
            'games' => 'developed_games_count',
            'reviews' => 'reviews_total',
            'rating' => 'rating_avg',
            'date' => 'users.created_at',
        ];
        $column = $sorts[$request->sort] ?? 'users.created_at';
        $dir = $request->dir === 'asc' ? 'asc' : 'desc';

        // Reviews written on this developer's games
        $reviewsOfDeveloper = fn () => DB::table('reviews')
            ->join('games', 'games.id', '=', 'reviews.game_id')
            ->whereColumn('games.developer_id', 'users.id');

        $developers = User::role('developer')
            ->select('users.*')
            ->withCount('developedGames')
            ->selectSub($reviewsOfDeveloper()->selectRaw('count(*)'), 'reviews_total')
            ->selectSub($reviewsOfDeveloper()->selectRaw('avg(reviews.rating)'), 'rating_avg')
            ->when($request->q, fn ($query, $q) => $query->where(
                fn ($w) => $w->where('users.name', 'like', "%{$q}%")
                             ->orWhere('users.email', 'like', "%{$q}%")
            ))
            ->orderBy($column, $dir)
            ->orderByDesc('users.id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.developers.index', compact('developers'));
    }

    public function create()
    {
        return view('admin.developers.create');
    }

    public function store(Request $request)
    {
        $data = $this->handleAvatar($request, $request->validate($this->rules()));

        $developer = User::create($data); // password is hashed by the model cast
        $developer->assignRole('developer');

        return redirect()->route('admin.developers.index')
            ->with('success', "Developer {$developer->name} berhasil ditambahkan.");
    }

    public function edit(User $developer)
    {
        $this->ensureDeveloper($developer);

        return view('admin.developers.edit', compact('developer'));
    }

    public function update(Request $request, User $developer)
    {
        $this->ensureDeveloper($developer);

        $data = $this->handleAvatar($request, $request->validate($this->rules($developer)), $developer);

        if (blank($data['password'] ?? null)) {
            unset($data['password']); // keep the current password
        }

        $developer->update($data);

        // Keep the developer name on their games in sync
        if ($developer->wasChanged('name')) {
            Game::where('developer_id', $developer->id)->update(['developer' => $developer->name]);
        }

        return redirect()->route('admin.developers.index')
            ->with('success', "Developer {$developer->name} berhasil diperbarui.");
    }

    /** JSON for the developer suggestion box in the game form. */
    public function search(Request $request)
    {
        $q = trim((string) $request->q);

        return User::role('developer')
            ->where('name', 'like', "%{$q}%")
            ->orderByRaw('CASE WHEN name LIKE ? THEN 0 ELSE 1 END', ["{$q}%"])
            ->orderBy('name')
            ->limit(8)
            ->get(['id', 'name']);
    }

    private function rules(?User $developer = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($developer?->id)],
            'bio' => ['nullable', 'string', 'max:300'],
            'website' => ['nullable', 'url:http,https', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'password' => [$developer ? 'nullable' : 'required', 'confirmed', Password::defaults()],
        ];
    }

    private function handleAvatar(Request $request, array $data, ?User $developer = null): array
    {
        unset($data['avatar']); // an empty file input must not wipe the current photo

        if ($request->hasFile('avatar')) {
            $this->deleteAvatar($developer);
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        } elseif ($request->boolean('remove_avatar')) {
            $this->deleteAvatar($developer);
            $data['avatar'] = null;
        }

        return $data;
    }

    private function deleteAvatar(?User $developer): void
    {
        if ($developer?->avatar) {
            Storage::disk('public')->delete($developer->avatar);
        }
    }

    private function ensureDeveloper(User $user): void
    {
        abort_unless($user->hasRole('developer'), 404);
    }

    public function assign(Request $request)
    {
        $developers = User::role('developer')->orderBy('name')->get();

        $developer = $request->filled('developer')
            ? $developers->firstWhere('slug', $request->developer)
            : null;

        $games = null;
        $counts = [];
        $status = 'unassigned';

        if ($developer) {
            $counts = [
                'unassigned' => Game::whereNull('developer_id')->count(),
                'match' => Game::whereNull('developer_id')->where('developer', $developer->name)->count(),
                'mine' => Game::where('developer_id', $developer->id)->count(),
                'others' => Game::whereNotNull('developer_id')->where('developer_id', '!=', $developer->id)->count(),
                'all' => Game::count(),
            ];

            // If some unlinked games already carry this developer's name, show those first
            $status = in_array($request->status, ['unassigned', 'match', 'mine', 'others', 'all'])
                ? $request->status
                : ($counts['match'] > 0 ? 'match' : 'unassigned');

            $games = Game::with(['genre', 'developerAccount'])
                ->when($request->q, fn($query, $q) => $query->where(
                    fn($w) => $w->where('title', 'like', "%{$q}%")
                        ->orWhere('developer', 'like', "%{$q}%")
                ))
                ->when($status === 'unassigned', fn($query) => $query->whereNull('developer_id'))
                ->when($status === 'match', fn($query) => $query
                    ->whereNull('developer_id')->where('developer', $developer->name))
                ->when($status === 'mine', fn($query) => $query->where('developer_id', $developer->id))
                ->when($status === 'others', fn($query) => $query
                    ->whereNotNull('developer_id')->where('developer_id', '!=', $developer->id))
                ->latest()
                ->paginate(20)
                ->withQueryString();
        }

        return view('admin.developers.assign', compact('developers', 'developer', 'status', 'counts', 'games'));
    }

    public function storeAssignment(Request $request)
    {
        $data = $request->validate([
            'developer' => ['required', 'string'],
            'action' => ['required', 'in:assign,unassign'],
            'games' => ['required', 'array', 'min:1'],
            'games.*' => ['integer', 'exists:games,id'],
        ], [
            'games.required' => 'Pilih minimal satu game.',
            'games.min' => 'Pilih minimal satu game.',
        ]);

        $developer = User::role('developer')->where('slug', $data['developer'])->firstOrFail();

        if ($data['action'] === 'assign') {
            $fromOthers = Game::whereIn('id', $data['games'])
                ->whereNotNull('developer_id')
                ->where('developer_id', '!=', $developer->id)
                ->count();

            $total = Game::whereIn('id', $data['games'])->update([
                'developer_id' => $developer->id,
                'developer' => $developer->name,
            ]);

            $message = "{$total} game ditetapkan ke {$developer->name}.";
            if ($fromOthers > 0) {
                $message .= " ({$fromOthers} dialihkan dari developer lain)";
            }

            return back()->with('success', $message);
        }

        // Unassign: only games that currently belong to this developer
        $total = Game::whereIn('id', $data['games'])
            ->where('developer_id', $developer->id)
            ->update(['developer_id' => null]);

        return back()->with('success', "{$total} game dilepas dari {$developer->name}.");
    }

    public function destroy(User $developer)
    {
        $this->ensureDeveloper($developer);

        $name = $developer->name;
        $gameCount = $developer->developedGames()->count();

        AccountDeleter::delete($developer);

        return back()->with('success', "Developer {$name} dihapus. {$gameCount} game tetap ada di katalog tanpa akun developer.");
    }
}