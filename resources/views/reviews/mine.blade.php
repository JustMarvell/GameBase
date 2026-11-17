@extends('layouts.app')

@section('title', 'Ulasan Saya')

@section('content')
    <div x-data>
        <h1 class="mb-6 border-l-4 border-primary pl-3 text-2xl font-extrabold">Ulasan Saya</h1>

        @if ($reviews->isEmpty())
            <div class="card">
                <div class="card-body items-center py-12 text-center">
                    <p class="text-base-content/60">Anda belum menulis ulasan.</p>
                    <a href="{{ route('games.index') }}" class="btn btn-primary mt-3">Jelajahi Game</a>
                </div>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($reviews as $review)
                    <div class="card">
                        <div class="card-body gap-3 p-5 sm:flex-row sm:items-center">
                            <a href="{{ route('games.show', $review->game) }}" class="w-16 shrink-0">
                                @if ($review->game->cover_url)
                                    <img src="{{ $review->game->cover_url }}" alt="" class="aspect-3/4 w-16 rounded-lg object-cover">
                                @else
                                    <div class="flex aspect-3/4 w-16 items-center justify-center rounded-lg bg-base-200 text-base-content/30">
                                        <x-icon name="photo" class="size-6" />
                                    </div>
                                @endif
                            </a>

                            <div class="flex-1">
                                <a href="{{ route('games.show', $review->game) }}" class="font-bold hover:text-primary">{{ $review->game->title }}</a>
                                <p class="flex items-center gap-1 text-sm font-semibold">
                                    <x-icon name="star" class="size-4 fill-amber-400 text-amber-400" /> {{ $review->rating }}/10
                                </p>
                                @if ($review->body)
                                    <p class="mt-1 text-sm text-base-content/80">{{ Str::limit($review->body, 150) }}</p>
                                @endif
                                <p class="mt-1 text-xs text-base-content/50">{{ $review->created_at->diffForHumans() }}</p>
                            </div>

                            <div class="flex gap-1">
                                <a href="{{ route('games.show', $review->game) }}#ulasan" class="btn btn-text btn-sm">Lihat / Edit</a>
                                <button type="button" class="btn btn-text btn-sm text-error"
                                        @click="$dispatch('open-delete', @js(['action' => route('reviews.destroy', $review), 'name' => 'ulasan untuk ' . $review->game->title]))">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">{{ $reviews->links() }}</div>
        @endif

        <x-confirm-delete />
    </div>
@endsection