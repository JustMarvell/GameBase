@extends('layouts.admin')

@section('title', 'Platform')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <p class="text-base-content/60">Kelola platform game.</p>
        <a href="{{ route('admin.platforms.create') }}" class="btn btn-primary">
            <x-icon name="plus" class="size-5" /> Tambah Platform
        </a>
    </div>

    <div class="card">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Jumlah Game</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($platforms as $platform)
                        <tr>
                            <td>{{ $platforms->firstItem() + $loop->index }}</td>
                            <td class="font-semibold">{{ $platform->name }}</td>
                            <td><span class="badge badge-soft badge-primary">{{ $platform->games_count }}</span></td>
                            <td>
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('admin.platforms.edit', $platform) }}" class="btn btn-text btn-square btn-sm" aria-label="Edit">
                                        <x-icon name="pencil" class="size-4" />
                                    </a>
                                    <button type="button" class="btn btn-text btn-square btn-sm text-error" aria-label="Hapus"
                                            @click="$dispatch('open-delete', @js(['action' => route('admin.platforms.destroy', $platform), 'name' => $platform->name]))">
                                        <x-icon name="trash" class="size-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-base-content/60">Belum ada platform.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $platforms->links() }}</div>

    <x-confirm-delete />
@endsection