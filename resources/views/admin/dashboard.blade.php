@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
    <div class="card hero-gradient text-white shadow-lg">
        <div class="card-body">
            <p class="text-sm text-white/80">Selamat datang kembali,</p>
            <h2 class="text-3xl font-extrabold">{{ auth()->user()->name }} 👋</h2>
            <p class="mt-2 max-w-xl text-white/90">
                Temukan berbagai game disini
                Lihat dan berikan reviewmu!
            </p>
        </div>
    </div>
    @php
        $cards = [
            ['Total Game', $counts['games'], 'star', 'bg-primary/10 text-primary'],
            ['Total Genre', $counts['genres'], 'tag', 'bg-secondary/15 text-secondary'],
            ['Total Ulasan', $counts['reviews'], 'newspaper', 'bg-accent/20 text-accent'],
            ['Pengguna', $counts['users'], 'user', 'bg-success/15 text-success'],
        ];
    @endphp

    <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($cards as [$label, $value, $icon, $color])
            <div class="card">
                <div class="card-body">
                    <div class="flex size-11 items-center justify-center rounded-xl {{ $color }}">
                        <x-icon :name="$icon" class="size-6" />
                    </div>
                    <p class="mt-3 text-sm text-base-content/60">{{ $label }}</p>
                    <p class="text-3xl font-extrabold">{{ $value }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="card">
            <div class="card-body">
                <h3 class="font-bold">Ulasan Terbaru</h3>
                <ul class="mt-2 divide-y divide-base-content/10">
                    @forelse ($latestReviews as $review)
                        <li class="flex items-center justify-between gap-3 py-3 text-sm">
                            <div>
                                <p class="font-semibold">{{ $review->game->title }}</p>
                                <p class="text-base-content/60">{{ $review->user->name }} &middot; {{ $review->created_at->diffForHumans() }}</p>
                            </div>
                            <span class="badge badge-soft badge-warning">{{ $review->rating }}/10</span>
                        </li>
                    @empty
                        <li class="py-3 text-sm text-base-content/60">Belum ada ulasan.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h3 class="font-bold">Rating Tertinggi</h3>
                <ul class="mt-2 divide-y divide-base-content/10">
                    @forelse ($topGames as $game)
                        <li class="flex items-center justify-between gap-3 py-3 text-sm">
                            <a href="{{ route('games.show', $game) }}" target="_blank" class="font-semibold hover:text-primary">
                                {{ $loop->iteration }}. {{ $game->title }}
                            </a>
                            <span class="flex items-center gap-1 font-bold">
                                <x-icon name="star" class="size-4 fill-amber-400 text-amber-400" />
                                {{ number_format($game->reviews_avg_rating, 1) }}
                            </span>
                        </li>
                    @empty
                        <li class="py-3 text-sm text-base-content/60">Belum ada data.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection