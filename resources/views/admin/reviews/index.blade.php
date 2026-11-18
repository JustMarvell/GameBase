@extends('layouts.admin')

@section('title', 'Ulasan')

@section('content')
    <p class="mb-6 text-base-content/60">Moderasi ulasan dari pengguna.</p>

    <div class="card">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Game</th>
                        <th>Pengguna</th>
                        <th>Rating</th>
                        <th>Ulasan</th>
                        <th>Tanggal</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reviews as $review)
                        <tr>
                            <td class="font-semibold">
                                <a href="{{ route('games.show', $review->game) }}" target="_blank" class="link">{{ $review->game->title }}</a>
                            </td>
                            <td>{{ $review->user->name }}</td>
                            <td><span class="badge badge-soft badge-warning">{{ $review->rating }}/10</span></td>
                            <td>{{ $review->body ? Str::limit($review->body, 60) : '-' }}</td>
                            <td class="whitespace-nowrap text-sm">{{ $review->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="flex justify-end">
                                    <button type="button" class="btn btn-soft btn-error btn-square btn-sm text-error" aria-label="Hapus"
                                            @click="$dispatch('open-delete', @js(['action' => route('admin.reviews.destroy', $review), 'name' => 'ulasan dari ' . $review->user->name]))">
                                        <x-icon name="trash" class="size-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-base-content/60">Belum ada ulasan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $reviews->links() }}</div>

    <x-confirm-delete />
@endsection