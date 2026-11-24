@extends('layouts.app')

@section('title', 'Beranda')

@section('content')

    {{-- ===== SLIDER (Alpine.js) ===== --}}
    @if ($slides->isNotEmpty())
        <div x-data="{
                        i: 0, n: {{ $slides->count() }}, timer: null,
                        start() { this.timer = setInterval(() => this.i = (this.i + 1) % this.n, 5000) },
                        stop() { clearInterval(this.timer) },
                        next() { this.i = (this.i + 1) % this.n },
                        prev() { this.i = (this.i - 1 + this.n) % this.n },
                     }" x-init="start()" @mouseenter="stop()" @mouseleave="start()"
            class="relative h-72 overflow-hidden rounded-xl border border-base-content/10 bg-base-100 shadow-lg md:h-96">

            @foreach ($slides as $k => $game)
            <a href="{{ route('games.show', $game) }}" x-show="i === {{ $k }}" x-transition.opacity.duration.500ms
                @if ($k > 0) x-cloak @endif class="absolute inset-0 block">

                    @if ($game->cover_url)
                        <img src="{{ $game->cover_url }}" alt="" referrerpolicy="no-referrer" onerror="this.remove()"
                            class="absolute inset-0 size-full scale-110 object-cover opacity-50 blur-md">
                    @else
                        <div class="absolute inset-0 bg-linear-to-br from-secondary to-primary"></div>
                    @endif
                    <div class="absolute inset-0 bg-linear-to-t from-black/80 via-black/20 to-transparent"></div>

                    <div class="relative flex h-full items-end justify-between gap-6 p-6 md:p-10">
                        <div class="max-w-xl text-white">
                            <span class="badge border-0 bg-base-200/80 text-secondary backdrop-blur">{{ $game->genre->name }}</span>
                            @auth
                                @if (auth()->user()->hasInLibrary($game->id))
                                    <span class="badge badge-success ml-1 gap-1">
                                        <x-icon name="bookmark" class="size-3 fill-current" /> Di Library
                                    </span>
                                @endif
                            @endauth
                            <h2 class="mt-3 text-2xl font-extrabold md:text-4xl">{{ $game->title }}</h2>
                            <p class="mt-2 flex items-center gap-2 text-sm">
                                <x-icon name="star" class="size-5 fill-amber-400 text-amber-400" />
                                <span
                                    class="font-bold">{{ $game->reviews_avg_rating ? number_format($game->reviews_avg_rating, 1) : '-' }}</span>
                                <span class="text-white/70">/ 10 &middot; {{ $game->reviews_count }} ulasan</span>
                            </p>
                        </div>

                        @if ($game->cover_url)
                            <img src="{{ $game->cover_url }}" alt="{{ $game->title }}" referrerpolicy="no-referrer" onerror="this.remove()"
                                class="hidden h-56 rounded-lg object-cover shadow-2xl ring-1 ring-white/20 md:block">
                        @endif
                    </div>
                </a>
            @endforeach

            <button @click="prev()" class="btn btn-circle btn-sm absolute left-3 top-1/2 -translate-y-1/2 border-0 bg-base-200/70 text-base-content backdrop-blur hover:bg-primary hover:text-primary-content"
                aria-label="Sebelumnya">
                <x-icon name="chevron-left" class="size-5" />
            </button>
            <button @click="next()" class="btn btn-circle btn-sm absolute right-3 top-1/2 -translate-y-1/2 border-0 bg-base-200/70 text-base-content backdrop-blur hover:bg-primary hover:text-primary-content"
                aria-label="Berikutnya">
                <x-icon name="chevron-right" class="size-5" />
            </button>

            <div class="absolute right-4 top-4 flex gap-1.5">
                @foreach ($slides as $k => $game)
                    <button @click="i = {{ $k }}" aria-label="Slide {{ $k + 1 }}"
                        :class="i === {{ $k }} ? 'w-6 bg-primary' : 'w-2 bg-white/40'"
                        class="h-2 rounded-full transition-all"></button>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ===== KONTEN ===== --}}
    <div class="mt-10 grid gap-8 lg:grid-cols-4">
        <section class="lg:col-span-3">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="border-l-4 border-primary pl-3 text-xl font-extrabold">Game Terbaru</h2>
                <a href="{{ route('games.index') }}" class="link link-primary text-sm font-semibold">Lihat semua</a>
            </div>

            @if ($latest->isEmpty())
                <p class="text-base-content/60">Belum ada game.</p>
            @else
                <div class="grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-4">
                    @foreach ($latest as $game)
                        <x-game-card :game="$game" />
                    @endforeach
                </div>
            @endif
        </section>

        <aside>
            <div class="card sticky top-24 overflow-hidden">
                <div class="hero-gradient h-1"></div>

                <div class="card-body gap-3 p-5">
                    <h3 class="flex items-center gap-2 font-display font-bold">
                        <span class="flex size-7 items-center justify-center rounded-lg bg-primary/15 text-primary">
                            <x-icon name="tag" class="size-4" />
                        </span>
                        Genre
                    </h3>

                    <ul class="space-y-1">
                        @foreach ($genres as $genre)
                            <li>
                                <a href="{{ route('genres.show', $genre) }}"
                                    class="flex items-center justify-between rounded-lg px-3 py-2 text-sm font-semibold transition hover:bg-primary/10 hover:text-primary">
                                    {{ $genre->name }}
                                    <span class="badge badge-soft badge-secondary badge-sm">{{ $genre->games_count }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <a href="{{ route('games.index') }}"
                        class="link link-primary inline-flex items-center gap-1 text-sm font-semibold">
                        Lihat semua game <x-icon name="chevron-right" class="size-4" />
                    </a>
                </div>
            </div>
        </aside>
    </div>
@endsection