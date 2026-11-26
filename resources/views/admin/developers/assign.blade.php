@extends('layouts.admin')

@section('title', 'Tetapkan Game')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.developers.index') }}" class="btn btn-text btn-sm">
            <x-icon name="arrow-left" class="size-4" /> Daftar Developer
        </a>
    </div>

    {{-- 1. Pilih developer --}}
    <div class="card mb-6">
        <div class="card-body gap-2 p-5">
            <label for="developer" class="label-text font-semibold">1. Pilih developer</label>

            @if ($developers->isEmpty())
                <p class="text-sm text-base-content/60">
                    Belum ada developer.
                    <a href="{{ route('admin.developers.create') }}" class="link link-primary">Tambah developer</a> dulu.
                </p>
            @else
                <form method="GET" action="{{ route('admin.developers.assign') }}">
                    <select id="developer" name="developer" class="select max-w-md" onchange="this.form.submit()">
                        <option value="">-- Pilih developer --</option>
                        @foreach ($developers as $dev)
                            <option value="{{ $dev->slug }}" @selected($developer && $developer->id === $dev->id)>{{ $dev->name }}</option>
                        @endforeach
                    </select>
                </form>
            @endif
        </div>
    </div>

    @if ($developer)
        @php
            $tabs = [
                'unassigned' => 'Belum ditugaskan',
                'match' => 'Nama cocok',
                'mine' => 'Milik developer ini',
                'others' => 'Milik developer lain',
                'all' => 'Semua',
            ];
        @endphp

        {{-- 2. Pilih game --}}
        <div class="mb-3 flex items-center gap-3">
            <x-avatar :user="$developer" size="size-10" text="text-sm" />
            <div>
                <p class="font-bold">{{ $developer->name }}</p>
                <p class="text-xs text-base-content/60">{{ $counts['mine'] }} game saat ini</p>
            </div>
        </div>

        <p class="label-text mb-2 font-semibold">2. Pilih game</p>

        <div class="mb-4 flex flex-wrap gap-2">
            @foreach ($tabs as $key => $label)
                <a href="{{ request()->fullUrlWithQuery(['status' => $key, 'page' => null]) }}"
                   class="btn btn-sm {{ $status === $key ? 'btn-primary' : 'btn-soft btn-primary' }}">
                    {{ $label }}
                    <span class="badge badge-neutral badge-xs">{{ $counts[$key] }}</span>
                </a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('admin.developers.assign') }}" class="mb-4 flex flex-wrap items-center gap-2">
            <input type="hidden" name="developer" value="{{ $developer->slug }}">
            <input type="hidden" name="status" value="{{ $status }}">

            <label class="input grow items-center gap-2 sm:max-w-md">
                <x-icon name="search" class="size-4 text-base-content/50" />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari judul atau nama developer..." class="grow">
            </label>
            <button type="submit" class="btn btn-primary">Cari</button>
            @if (request()->filled('q'))
                <a href="{{ request()->fullUrlWithQuery(['q' => null, 'page' => null]) }}" class="btn btn-text">Reset</a>
            @endif
        </form>

        <form method="POST" action="{{ route('admin.developers.assign.store') }}" x-data="gameSelector()" class="card">
            @csrf
            <input type="hidden" name="developer" value="{{ $developer->slug }}">

            <div class="card-body gap-4 p-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm text-base-content/60">
                        <span class="font-semibold text-base-content" x-text="count"></span> game dipilih
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <button type="submit" name="action" value="assign" class="btn btn-primary btn-sm" :disabled="count === 0">
                            Tetapkan ke {{ $developer->name }}
                        </button>
                        <button type="submit" name="action" value="unassign" class="btn btn-soft btn-error btn-sm" :disabled="count === 0">
                            Lepas dari developer
                        </button>
                    </div>
                </div>

                @error('games') <p class="text-sm text-error">{{ $message }}</p> @enderror

                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="w-10">
                                    <input type="checkbox" class="checkbox checkbox-primary checkbox-sm" aria-label="Pilih semua di halaman ini"
                                           @change="toggleAll($event.target.checked)">
                                </th>
                                <th>Game</th>
                                <th>Genre</th>
                                <th>Tahun</th>
                                <th>Developer saat ini</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($games as $game)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="games[]" value="{{ $game->id }}" data-game
                                               class="checkbox checkbox-primary checkbox-sm" @change="recount()">
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="relative h-12 w-9 shrink-0 overflow-hidden rounded bg-base-300">
                                                @if ($game->cover_url)
                                                    <img src="{{ $game->cover_url }}" alt="" referrerpolicy="no-referrer" onerror="this.remove()"
                                                         style="object-position: {{ $game->cover_position }}"
                                                         class="absolute inset-0 size-full object-cover">
                                                @endif
                                            </div>
                                            <a href="{{ route('games.show', $game) }}" target="_blank" class="font-semibold hover:text-primary">{{ $game->title }}</a>
                                        </div>
                                    </td>
                                    <td><span class="badge badge-soft badge-secondary badge-sm">{{ $game->genre->name }}</span></td>
                                    <td>{{ $game->release_year }}</td>
                                    <td>
                                        @if ($game->developerAccount)
                                            <span class="badge badge-soft badge-sm {{ $game->developer_id == $developer->id ? 'badge-success' : 'badge-warning' }}">
                                                {{ $game->developerAccount->name }}
                                            </span>
                                        @else
                                            <span class="text-sm text-base-content/60">{{ $game->developer ?: '-' }}</span>
                                            <span class="badge badge-ghost badge-xs">tanpa akun</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-base-content/60">Tidak ada game di tab ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </form>

        <div class="mt-4">{{ $games->links() }}</div>
    @endif
@endsection