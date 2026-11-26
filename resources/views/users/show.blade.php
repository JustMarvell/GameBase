@extends('layouts.app')

@section('title', $user->name)

@section('content')

    {{-- ===== HEADER PROFIL ===== --}}
    <div class="card overflow-hidden">
        <div class="hero-gradient h-24"></div>

        <div class="card-body pt-0">
            <div class="-mt-12 flex flex-wrap items-end justify-between gap-4">
                <div class="flex items-end gap-4">
                    <div class="rounded-full bg-base-100 p-1">
                        <x-avatar :user="$user" size="size-24" text="text-3xl" />
                    </div>
                    <div class="pb-1">
                        <h1 class="flex flex-wrap items-center gap-2 text-2xl font-extrabold">
                            {{ $user->name }}
                            <x-role-badge :user="$user" />
                        </h1>
                        <p class="text-sm text-base-content/60">Bergabung {{ $user->created_at->translatedFormat('F Y') }}</p>
                    </div>
                </div>

                @if ($isOwner)
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm">
                        <x-icon name="pencil" class="size-4" /> Edit Profil
                    </a>
                    @if ($isDeveloper)
                        <a href="{{ route('admin.games.index') }}" class="btn btn-soft btn-secondary btn-sm">Kelola Game</a>
                    @endif
                @endif
            </div>

            @if ($user->bio)
                <p class="mt-4 whitespace-pre-line text-base-content/80">{{ $user->bio }}</p>
            @elseif ($isOwner)
                <p class="mt-4 text-sm text-base-content/50">Belum ada bio. Tambahkan di "Edit Profil".</p>
            @endif

            @if ($isDeveloper && ($user->website || $devGenres->isNotEmpty()))
                <div class="mt-3 flex flex-wrap items-center gap-2 text-sm">
                    @if ($user->website)
                        <a href="{{ $user->website }}" target="_blank" rel="noopener nofollow" class="link link-primary inline-flex items-center gap-1">
                            <x-icon name="globe" class="size-4" /> {{ parse_url($user->website, PHP_URL_HOST) ?: $user->website }}
                        </a>
                    @endif
                    @foreach ($devGenres as $genre)
                        <a href="{{ route('genres.show', $genre) }}" class="badge badge-soft badge-secondary">{{ $genre->name }}</a>
                    @endforeach
                </div>
            @endif

            @if ($isDeveloper)
                <div class="mt-5 grid grid-cols-2 gap-3 text-center sm:grid-cols-4">
                    <div class="rounded-lg border border-base-content/10 bg-base-200/60 p-3">
                        <p class="text-2xl font-extrabold">{{ $devStats['games'] }}</p>
                        <p class="text-xs text-base-content/60">Game</p>
                    </div>
                    <div class="rounded-lg border border-base-content/10 bg-base-200/60 p-3">
                        <p class="text-2xl font-extrabold">{{ $devStats['reviews'] }}</p>
                        <p class="text-xs text-base-content/60">Ulasan diterima</p>
                    </div>
                    <div class="rounded-lg border border-base-content/10 bg-base-200/60 p-3">
                        <p class="text-2xl font-extrabold">{{ $devStats['rating'] ? number_format($devStats['rating'], 1) : '-' }}</p>
                        <p class="text-xs text-base-content/60">Rata-rata rating</p>
                    </div>
                    <div class="rounded-lg border border-base-content/10 bg-base-200/60 p-3">
                        <p class="text-2xl font-extrabold">{{ $devStats['saves'] }}</p>
                        <p class="text-xs text-base-content/60">Disimpan di library</p>
                    </div>
                </div>
            @else
                <div class="mt-5 grid grid-cols-3 gap-3 text-center">
                    <div class="rounded-lg border border-base-content/10 bg-base-200/60 p-3">
                        <p class="text-2xl font-extrabold">{{ $reviews->total() }}</p>
                        <p class="text-xs text-base-content/60">Ulasan</p>
                    </div>
                    <div class="rounded-lg border border-base-content/10 bg-base-200/60 p-3">
                        <p class="text-2xl font-extrabold">{{ $libraryTotal ?? '-' }}</p>
                        <p class="text-xs text-base-content/60">Game di library</p>
                    </div>
                    <div class="rounded-lg border border-base-content/10 bg-base-200/60 p-3">
                        <p class="text-2xl font-extrabold">{{ $averageRating ? number_format($averageRating, 1) : '-' }}</p>
                        <p class="text-xs text-base-content/60">Rata-rata nilai</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if ($isDeveloper)
        <section class="mt-8">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="border-l-4 border-primary pl-3 text-xl font-extrabold">Game oleh {{ $user->name }}</h2>
                @if ($isOwner)
                    <a href="{{ route('admin.games.create') }}" class="btn btn-primary btn-sm">
                        <x-icon name="plus" class="size-4" /> Tambah Game
                    </a>
                @endif
            </div>

            @if ($devGames->isEmpty())
                <p class="text-base-content/60">Belum ada game.</p>
            @else
                <div class="grid grid-cols-2 gap-4 md:grid-cols-4 xl:grid-cols-6">
                    @foreach ($devGames as $game)
                        <x-game-card :game="$game" />
                    @endforeach
                </div>
                <div class="mt-4">{{ $devGames->links() }}</div>
            @endif
        </section>
    @else
        {{-- ===== LIBRARY ===== --}}
        <section class="mt-8">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="border-l-4 border-primary pl-3 text-xl font-extrabold">Library</h2>
                @if ($isOwner && $libraryTotal > 12)
                    <a href="{{ route('library.index') }}" class="link link-primary text-sm font-semibold">Lihat semua</a>
                @endif
            </div>

            @if ($libraryGames === null)
                <div class="card">
                    <div class="card-body items-center py-8 text-center text-base-content/60">
                        <x-icon name="lock" class="size-8" />
                        Library pengguna ini bersifat privat.
                    </div>
                </div>
            @elseif ($libraryGames->isEmpty())
                <p class="text-base-content/60">Library masih kosong.</p>
            @else
                <div class="grid grid-cols-2 gap-4 md:grid-cols-4 xl:grid-cols-6">
                    @foreach ($libraryGames as $game)
                        <x-game-card :game="$game" />
                    @endforeach
                </div>
                @if (!$isOwner && $libraryTotal > 12)
                    <p class="mt-3 text-sm text-base-content/50">Menampilkan 12 game terbaru dari {{ $libraryTotal }}.</p>
                @endif
            @endif
        </section>
    @endif

    {{-- ===== ULASAN ===== --}}
    <section class="mt-10">
        <h2 class="mb-4 border-l-4 border-primary pl-3 text-xl font-extrabold">Ulasan</h2>

        <div class="space-y-3">
            @forelse ($reviews as $review)
                <div class="card">
                    <div class="card-body flex-row gap-4 p-4">
                        <a href="{{ route('games.show', $review->game) }}" class="w-14 shrink-0">
                            <div class="relative aspect-3/4 overflow-hidden rounded-lg bg-base-300">
                                <div class="absolute inset-0 flex items-center justify-center text-base-content/30">
                                    <x-icon name="photo" class="size-5" />
                                </div>
                                @if ($review->game->cover_url)
                                    <img src="{{ $review->game->cover_url }}" alt="" referrerpolicy="no-referrer"
                                         onerror="this.remove()" class="absolute inset-0 size-full object-cover" style="object-position: {{ $review->game->cover_position }}">
                                @endif
                            </div>
                        </a>

                        <div class="min-w-0 flex-1">
                            <a href="{{ route('games.show', $review->game) }}" class="font-bold hover:text-primary">{{ $review->game->title }}</a>
                            <span class="badge badge-soft badge-warning mt-1 gap-1 font-bold">
                                <x-icon name="star" class="size-3.5 fill-current" /> {{ $review->rating }}/10
                            </span>
                            @if ($review->body)
                                <p class="mt-1 text-sm text-base-content/80">{{ Str::limit($review->body, 200) }}</p>
                            @endif
                            <p class="mt-1 text-xs text-base-content/50">{{ $review->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-base-content/60">Belum menulis ulasan.</p>
            @endforelse
        </div>

        <div class="mt-4">{{ $reviews->links() }}</div>
    </section>
@endsection