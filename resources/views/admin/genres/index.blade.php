@extends('layouts.admin')

@section('title', 'Genre')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <p class="text-base-content/60">Kelola genre game.</p>
        <a href="{{ route('admin.genres.create') }}" class="btn btn-primary">
            <x-icon name="plus" class="size-5" /> Tambah Genre
        </a>
    </div>

    <div class="card">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Deskripsi</th>
                        <th>Jumlah Game</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($genres as $genre)
                        <tr>
                            <td>{{ $genres->firstItem() + $loop->index }}</td>
                            <td class="font-semibold">{{ $genre->name }}</td>
                            <td>{{ $genre->description ? Str::limit($genre->description, 60) : '-' }}</td>
                            <td><span class="badge badge-soft badge-primary">{{ $genre->games_count }}</span></td>
                            <td>
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('admin.genres.edit', $genre) }}" class="btn btn-soft btn-primary btn-square btn-sm" aria-label="Edit">
                                        <x-icon name="pencil" class="size-4" />
                                    </a>
                                    <button type="button" class="btn btn-soft btn-error btn-square btn-sm text-error" aria-label="Hapus"
                                            @click="$dispatch('open-delete', @js(['action' => route('admin.genres.destroy', $genre), 'name' => $genre->name]))">
                                        <x-icon name="trash" class="size-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-base-content/60">Belum ada genre.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $genres->links() }}</div>

    <x-confirm-delete />
@endsection