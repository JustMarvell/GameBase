@extends('layouts.admin')

@section('title', 'Game')

@section('content')
            @php
    $filtering = collect(['q', 'genre', 'platform', 'cover'])->contains(fn($key) => request()->filled($key));
            @endphp

                <div class="mb-6 flex items-center justify-between">
                    <p class="text-base-content/60">Kelola data game.</p>
                        <form method="GET" action="{{ route('admin.games.index') }}" class="card mb-6">
                        <div class="card-body grid gap-3 p-4 md:grid-cols-6">
                            {{-- keep the current sorting when searching --}}
                            @if (request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                            @if (request('dir')) <input type="hidden" name="dir" value="{{ request('dir') }}"> @endif

                            <label class="input items-center gap-2 md:col-span-2">
                                <x-icon name="search" class="size-4 text-base-content/50" />
                                <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari judul / developer..." class="grow">
                            </label>

                            <select name="genre" class="select">
                                <option value="">Semua genre</option>
                                @foreach ($genres as $genre)
                                    <option value="{{ $genre->id }}" @selected(request('genre') == $genre->id)>{{ $genre->name }}</option>
                                @endforeach
                            </select>

                            <select name="platform" class="select">
                                <option value="">Semua platform</option>
                                @foreach ($platforms as $platform)
                                    <option value="{{ $platform->id }}" @selected(request('platform') == $platform->id)>{{ $platform->name }}</option>
                                @endforeach
                            </select>

                            <select name="cover" class="select">
                                <option value="">Semua cover</option>
                                <option value="has" @selected(request('cover') === 'has')>Ada cover</option>
                                <option value="none" @selected(request('cover') === 'none')>Tanpa cover</option>
                            </select>

                            <div class="flex gap-2">
                                <a href="{{ route('admin.games.index') }}" class="btn btn-text">Reset</a>
                                <button type="submit" class="btn btn-primary">Cari</button>
                            </div>
                        </div>
                    </form>
                    <a href="{{ route('admin.games.create') }}" class="btn btn-primary">
                        <x-icon name="plus" class="size-5" /> Tambah Game
                    </a>
                </div>

                <div class="card">
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Cover</th>
                                    <th><x-sort-link column="title" label="Judul" /></th>
                                    <th>Genre</th>
                                    <th>Platform</th>
                                    <th><x-sort-link column="year" label="Tahun" /></th>
                                    <th><x-sort-link column="rating" label="Rating" /></th>
                                    <th class="text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($games as $game)
                                    <tr>
                                        <td>
                                            @if ($game->cover_url)
                                                <img src="{{ $game->cover_url }}" alt="{{ $game->title }}" class="h-14 w-10 rounded object-cover" referrerpolicy="no-referrer" style="object-position: {{ $game->cover_position }}">
                                            @else
                                                <div class="flex h-14 w-10 items-center justify-center rounded bg-base-200 text-base-content/40">
                                                    <x-icon name="photo" class="size-5" />
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="font-semibold">{{ $game->title }}</div>
                                            <div class="text-xs text-base-content/60">{{ $game->developer }}</div>
                                        </td>
                                        <td><span class="badge badge-soft badge-secondary">{{ $game->genre->name }}</span></td>
                                        <td>
                                            <div class="flex flex-wrap gap-1">
                                                @forelse ($game->platforms as $platform)
                                                    <span class="badge badge-soft badge-primary badge-sm">{{ $platform->name }}</span>
                                                @empty
                                                    -
                                                @endforelse
                                            </div>
                                        </td>
                                        <td>{{ $game->release_year }}</td>
                                        <td>
                                            {{ $game->reviews_avg_rating ? number_format($game->reviews_avg_rating, 1) : '-' }}
                                            <span class="text-xs text-base-content/50">({{ $game->reviews_count }})</span>
                                        </td>
                                        <td>
                                            <div class="flex justify-end gap-1">
                                                <a href="{{ route('admin.games.media.index', $game) }}" class="btn btn-soft btn-secondary btn-square btn-sm" aria-label="Galeri" title="Kelola galeri">
                                                    <x-icon name="photo" class="size-4" />
                                                </a>
                                                <a href="{{ route('admin.games.edit', $game) }}" class="btn btn-soft btn-primary btn-square btn-sm" aria-label="Edit" title="Edit data game">
                                                    <x-icon name="pencil" class="size-4" />
                                                </a>
                                                <button type="button" class="btn btn-soft btn-error btn-square btn-sm" aria-label="Hapus" title="Hapus"
                                                        @click="$dispatch('open-delete', @js(['action' => route('admin.games.destroy', $game), 'name' => $game->title]))">
                                                    <x-icon name="trash" class="size-4" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-8 text-center text-base-content/60">
                                            {{ $filtering ? 'Tidak ada game yang cocok dengan filter.' : 'Belum ada game.' }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4">{{ $games->links() }}</div>

                <x-confirm-delete />
@endsection