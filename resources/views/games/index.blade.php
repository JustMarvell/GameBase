@extends('layouts.app')

@section('title', 'Semua Game')

@section('content')
    <div class="mb-4 flex items-end justify-between gap-4">
        <h1 class="border-l-4 border-primary pl-3 text-2xl font-extrabold">Semua Game</h1>
        <span class="text-sm text-base-content/60">{{ $games->total() }} game</span>
    </div>

    <form method="GET" action="{{ route('games.index') }}" class="card mb-6">
        <div class="card-body grid gap-3 p-4 md:grid-cols-5">
            <label class="input items-center gap-2 md:col-span-2">
                <x-icon name="search" class="size-4 text-base-content/50" />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari judul / developer..." class="grow">
            </label>

            <select name="genre" class="select">
                <option value="">Semua genre</option>
                @foreach ($genres as $genre)
                    <option value="{{ $genre->id }}" @selected(request('genre') == $genre->id)>{{ $genre->name }}</option>
                @endforeach
            </select>

            <select name="platform" class="select">
                <option value="">Semua platform</option>
                @foreach ($platforms as $platform)
                    <option value="{{ $platform->id }}" @selected(request('platform') == $platform->id)>{{ $platform->name }}</option>
                @endforeach
            </select>

            <select name="sort" class="select">
                <option value="">Terbaru</option>
                <option value="rating" @selected(request('sort') === 'rating')>Rating tertinggi</option>
                <option value="year" @selected(request('sort') === 'year')>Tahun rilis</option>
                <option value="title" @selected(request('sort') === 'title')>Judul A-Z</option>
            </select>

            <div class="flex gap-2 md:col-span-5 md:justify-end">
                <a href="{{ route('games.index') }}" class="btn btn-text">Reset</a>
                <button type="submit" class="btn btn-primary">
                    <x-icon name="search" class="size-4" /> Cari
                </button>
            </div>
        </div>
    </form>

    @if ($games->isEmpty())
        <div class="card">
            <div class="card-body items-center gap-2 py-12 text-center text-base-content/60">
                <x-icon name="search" class="size-8 text-base-content/30" />
                Tidak ada game yang cocok.
            </div>
        </div>
    @else
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4 xl:grid-cols-6">
            @foreach ($games as $game)
                <x-game-card :game="$game" />
            @endforeach
        </div>

        <div class="mt-6">{{ $games->links() }}</div>
    @endif
@endsection