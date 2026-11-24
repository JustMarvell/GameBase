@extends('layouts.app')

@section('title', $genre->name)

@section('content')
    <div class="mb-6 border-b border-base-content/10 pb-4">
        <p class="text-xs font-semibold uppercase tracking-wider text-secondary">Genre</p>
        <h1 class="mt-1 text-3xl font-extrabold">{{ $genre->name }}</h1>
        @if ($genre->description)
            <p class="mt-2 max-w-2xl text-base-content/70">{{ $genre->description }}</p>
        @endif
        <p class="mt-2 text-sm text-base-content/50">{{ $games->total() }} game</p>
    </div>

    @if ($games->isEmpty())
        <div class="card">
            <div class="card-body items-center py-12 text-center text-base-content/60">
                Belum ada game di genre ini.
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