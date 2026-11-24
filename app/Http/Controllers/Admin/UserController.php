<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $column = ['name' => 'name', 'date' => 'created_at'][$request->sort] ?? 'created_at';
        $dir = $request->dir === 'asc' ? 'asc' : 'desc';

        $users = User::with('roles')
            ->when($request->q, fn($query, $q) => $query->where(
                fn($w) => $w->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%")
            ))
            ->when(in_array($request->role, ['user', 'developer', 'admin']), fn($query) => $query->role($request->role))
            ->orderBy($column, $dir)
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create($data); // password is hashed by the model cast
        $user->assignRole('developer');

        return redirect()->route('admin.users.index')
            ->with('success', "Akun developer {$user->name} berhasil dibuat.");
    }

    public function updateRole(Request $request, User $user)
    {
        $data = $request->validate(['role' => ['required', 'in:user,developer']]);

        abort_if($user->hasRole('admin'), 403);

        $user->syncRoles([$data['role']]);

        if ($data['role'] === 'user') {
            // A demoted developer no longer owns games; the games stay in the catalog
            Game::where('developer_id', $user->id)->update(['developer_id' => null]);
        }

        return back()->with('success', "Role {$user->name} diubah menjadi {$data['role']}.");
    }

    /** JSON for the developer suggestion box in the game form. */
    public function searchDevelopers(Request $request)
    {
        $q = trim((string) $request->q);

        return User::role('developer')
            ->where('name', 'like', "%{$q}%")
            ->orderByRaw('CASE WHEN name LIKE ? THEN 0 ELSE 1 END', ["{$q}%"])
            ->orderBy('name')
            ->limit(8)
            ->get(['id', 'name']);
    }
}