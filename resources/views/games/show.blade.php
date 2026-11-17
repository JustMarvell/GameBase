@extends('layouts.app')

@section('title', $game->title)

@section('content')
    <div class="card">
        <div class="card-body gap-8 md:flex-row">
            <div class="w-full shrink-0 md:w-64">
                @if ($game->cover_url)
                    <img src="{{ $game->cover_url }}" alt="{{ $game->title }}" class="aspect-[3/4] w-full rounded-xl object-cover shadow">
                @else
                    <div class="flex aspect-[3/4] w-full items-center justify-center rounded-xl bg-base-200 text-base-content/30">
                        <x-icon name="photo" class="size-16" />
                    </div>
                @endif
            </div>

            <div class="flex-1">
                <a href="{{ route('genres.show', $game->genre) }}" class="badge badge-secondary">{{ $game->genre->name }}</a>
                <h1 class="mt-2 text-3xl font-extrabold">{{ $game->title }}</h1>

                <div class="mt-3 flex items-center gap-2">
                    <x-icon name="star" class="size-7 fill-amber-400 text-amber-400" />
                    <span class="text-3xl font-extrabold">
                        {{ $game->reviews_avg_rating ? number_format($game->reviews_avg_rating, 1) : '-' }}
                    </span>
                    <span class="text-base-content/60">/ 10 &middot; {{ $game->reviews->count() }} ulasan</span>
                </div>

                <dl class="mt-5 grid grid-cols-[auto_1fr] gap-x-6 gap-y-2 text-sm">
                    <dt class="text-base-content/60">Developer</dt>
                    <dd class="font-semibold">{{ $game->developer }}</dd>

                    <dt class="text-base-content/60">Tahun rilis</dt>
                    <dd class="font-semibold">{{ $game->release_year }}</dd>

                    <dt class="text-base-content/60">Platform</dt>
                    <dd class="flex flex-wrap gap-1">
                        @forelse ($game->platforms as $platform)
                            <span class="badge badge-soft badge-primary">{{ $platform->name }}</span>
                        @empty
                            -
                        @endforelse
                    </dd>
                </dl>

                @if ($game->description)
                    <p class="mt-6 whitespace-pre-line text-base-content/80">{{ $game->description }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- ===== ULASAN ===== --}}
    <section class="mt-8">
        <h2 class="mb-4 border-l-4 border-primary pl-3 text-xl font-extrabold">Ulasan</h2>

        {{-- Form ulasan ditambahkan di Step 7 --}}

        <div class="space-y-4">
            @forelse ($game->reviews as $review)
                <div class="card">
                    <div class="card-body gap-2 p-5">
                        <div class="flex items-center justify-between">
                            <span class="font-bold">{{ $review->user->name }}</span>
                            <span class="flex items-center gap-1 font-bold">
                                <x-icon name="star" class="size-5 fill-amber-400 text-amber-400" />
                                {{ $review->rating }}/10
                            </span>
                        </div>
                        @if ($review->body)
                            <p class="whitespace-pre-line text-base-content/80">{{ $review->body }}</p>
                        @endif
                        <p class="text-xs text-base-content/50">{{ $review->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <p class="text-base-content/60">Belum ada ulasan untuk game ini.</p>
            @endforelse
        </div>
    </section>

    {{-- ===== GAME SEJENIS ===== --}}
    @if ($related->isNotEmpty())
        <section class="mt-10">
            <h2 class="mb-4 border-l-4 border-primary pl-3 text-xl font-extrabold">Game {{ $game->genre->name }} Lainnya</h2>
            <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                @foreach ($related as $item)
                    <x-game-card :game="$item" />
                @endforeach
            </div>
        </section>
    @endif
@endsection