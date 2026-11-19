@extends('layouts.app')

@section('title', $game->title)

@section('content')
    @if ($game->media->isNotEmpty())
        <div class="mb-6">
            <x-game-carousel :media="$game->media" />
        </div>
    @endif
    <div class="card">
        <div class="card-body gap-8 md:flex-row">
        @if ($game->media->isEmpty())
            <div class="w-full shrink-0 md:w-64">
                @if ($game->cover_url)
                    <img src="{{ $game->cover_url }}" alt="{{ $game->title }}" class="aspect-3/4 w-full rounded-xl object-cover shadow" referrerpolicy="no-referrer" style="object-position: {{ $game->cover_position }}">
                @else
                    <div class="flex aspect-3/4 w-full items-center justify-center rounded-xl bg-base-200 text-base-content/30">
                        <x-icon name="photo" class="size-16" />
                    </div>
                @endif
            </div>
        @endif

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

                @php $inLibrary = auth()->check() && auth()->user()->hasInLibrary($game->id); @endphp

                <div class="mt-5 flex flex-wrap items-center gap-2">
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            <x-icon name="bookmark" class="size-5" /> Tambah ke Library
                        </a>
                        <span class="text-sm text-base-content/60">Masuk untuk menyimpan game ini.</span>
                    @else
                        @if ($inLibrary)
                            <span class="badge badge-soft badge-success badge-lg gap-1">
                                <x-icon name="bookmark" class="size-4 fill-current" /> Ada di library Anda
                            </span>
                            <form method="POST" action="{{ route('library.destroy', $game) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-soft btn-error btn-sm">Hapus dari Library</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('library.store', $game) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <x-icon name="bookmark" class="size-5" /> Tambah ke Library
                                </button>
                            </form>
                        @endif
                    @endguest
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
    <section id="ulasan" class="mt-8" x-data>
        <h2 class="mb-4 border-l-4 border-primary pl-3 text-xl font-extrabold">Ulasan</h2>

        @auth
            @if (!$myReview)
                <div class="card mb-6">
                    <div class="card-body p-5">
                        <h3 class="font-bold">Tulis ulasan</h3>
                        @include('games._review-form', ['action' => route('reviews.store', $game), 'review' => null])
                    </div>
                </div>
            @endif
        @else
            <div class="alert alert-soft alert-info mb-6">
                <a href="{{ route('login') }}" class="link font-semibold">Masuk</a>
                atau
                <a href="{{ route('register') }}" class="link font-semibold">daftar</a>
                untuk menulis ulasan.
            </div>
        @endauth

        <div class="space-y-4">
            @forelse ($game->reviews as $review)
                @php
    $own = auth()->check() && $review->user_id === auth()->id();
    $canDelete = $own || (auth()->check() && auth()->user()->hasRole('admin'));
                @endphp

                <div class="card {{ $own ? 'ring-2 ring-primary/40' : '' }}"
                     x-data="{ editing: {{ $own && $errors->any() ? 'true' : 'false' }} }">
                    <div class="card-body gap-2 p-5">
                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-2 font-bold">
                                <a href="{{ route('users.show', $review->user) }}" class="flex items-center gap-2 hover:text-primary">
                                    <x-avatar :user="$review->user" size="size-8" text="text-xs" />
                                    {{ $review->user->name }}
                                </a>
                                @if ($own)
                                    <span class="badge badge-soft badge-primary badge-sm">Ulasan Anda</span>
                                @endif
                            </span>
                            <span class="flex items-center gap-1 font-bold">
                                <x-icon name="star" class="size-5 fill-amber-400 text-amber-400" />
                                {{ $review->rating }}/10
                            </span>
                        </div>

                        <div x-show="!editing">
                            @if ($review->body)
                                <p class="whitespace-pre-line text-base-content/80">{{ $review->body }}</p>
                            @endif
                        </div>

                        @if ($own)
                            <div x-show="editing" x-cloak class="mt-2">
                                @include('games._review-form', ['action' => route('reviews.update', $review), 'review' => $review])
                            </div>
                        @endif

                        <div class="flex items-center justify-between">
                            <p class="text-xs text-base-content/50">{{ $review->created_at->diffForHumans() }}</p>

                            <div class="flex gap-1">
                                @if ($own)
                                    <button type="button" @click="editing = !editing"
                                            x-text="editing ? 'Batal' : 'Edit'" class="btn btn-text btn-sm"></button>
                                @endif
                                @if ($canDelete)
                                    <button type="button" class="btn btn-text btn-sm text-error"
                                            @click="$dispatch('open-delete', @js(['action' => route('reviews.destroy', $review), 'name' => 'ulasan dari ' . $review->user->name]))">
                                        Hapus
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-base-content/60">Belum ada ulasan untuk game ini.</p>
            @endforelse
        </div>

        <x-confirm-delete />
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