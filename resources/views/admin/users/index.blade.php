@extends('layouts.admin')

@section('title', 'Pengguna')

@section('content')
    @php
        $filtering = collect(['q', 'role'])->contains(fn($key) => request()->filled($key));
    @endphp

    <div class="mb-6 flex items-center justify-between">
        <p class="text-base-content/60">Kelola pengguna dan atur role developer.</p>
        <a href="{{ route('admin.developers.create') }}" class="btn btn-primary">
            <x-icon name="plus" class="size-5" /> Tambah Developer
        </a>
    </div>

    <form method="GET" action="{{ route('admin.users.index') }}" class="card mb-6">
        <div class="card-body grid gap-3 p-4 md:grid-cols-4">
            @if (request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
            @if (request('dir')) <input type="hidden" name="dir" value="{{ request('dir') }}"> @endif

            <label class="input items-center gap-2 md:col-span-2">
                <x-icon name="search" class="size-4 text-base-content/50" />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama atau email..." class="grow">
            </label>

            <select name="role" class="select">
                <option value="">Semua role</option>
                <option value="user" @selected(request('role') === 'user')>User</option>
                <option value="developer" @selected(request('role') === 'developer')>Developer</option>
                <option value="admin" @selected(request('role') === 'admin')>Admin</option>
            </select>

            <div class="flex gap-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-text">Reset</a>
                <button type="submit" class="btn btn-primary">Cari</button>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th><x-sort-link column="name" label="Pengguna" /></th>
                        <th>Role</th>
                        <th><x-sort-link column="date" label="Bergabung" /></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>
                                <a href="{{ route('users.show', $user) }}" target="_blank" class="flex items-center gap-3">
                                    <x-avatar :user="$user" size="size-9" text="text-sm" />
                                    <span>
                                        <span class="block font-semibold">{{ $user->name }}</span>
                                        <span class="block text-xs text-base-content/60">{{ $user->email }}</span>
                                    </span>
                                </a>
                            </td>
                            <td>
                                @if ($user->hasRole('admin'))
                                    <x-role-badge :user="$user" />
                                @else
                                    <form method="POST" action="{{ route('admin.users.role', $user) }}">
                                        @csrf
                                        @method('PUT')
                                        <select name="role" class="select select-sm w-36" onchange="this.form.requestSubmit()">
                                            <option value="user" @selected(!$user->hasRole('developer'))>User</option>
                                            <option value="developer" @selected($user->hasRole('developer'))>Developer</option>
                                        </select>
                                    </form>
                                @endif
                            </td>
                            <td class="whitespace-nowrap text-sm">{{ $user->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-base-content/60">
                                {{ $filtering ? 'Tidak ada pengguna yang cocok.' : 'Belum ada pengguna.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
@endsection