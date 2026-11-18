@extends('layouts.admin')

@section('title', 'Game')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <p class="text-base-content/60">Kelola data game.</p>
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
                        <th>Judul</th>
                        <th>Genre</th>
                        <th>Platform</th>
                        <th>Tahun</th>
                        <th>Rating</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($games as $game)
                        <tr>
                            <td>
                                @if ($game->cover_url)
                                    <img src="{{ $game->cover_url }}" alt="{{ $game->title }}" class="h-14 w-10 rounded object-cover">
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
                            <td>{{ $game->reviews_avg_rating ? number_format($game->reviews_avg_rating, 1) : '-' }}</td>
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
                            <td colspan="7" class="py-8 text-center text-base-content/60">Belum ada game.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $games->links() }}</div>

    <x-confirm-delete />
@endsection