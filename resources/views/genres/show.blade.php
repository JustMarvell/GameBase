@extends('layouts.app')

@section('title', $genre->name)

@section('content')
    <div class="mb-6">
        <h1 class="border-l-4 border-primary pl-3 text-2xl font-extrabold">Genre: {{ $genre->name }}</h1>
        @if ($genre->description)
            <p class="mt-2 text-base-content/70">{{ $genre->description }}</p>
        @endif
    </div>

    @if ($games->isEmpty())
        <p class="py-12 text-center text-base-content/60">Belum ada game di genre ini.</p>
    @else
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4 xl:grid-cols-6">
            @foreach ($games as $game)
                <x-game-card :game="$game" />
            @endforeach
        </div>

        <div class="mt-6">{{ $games->links() }}</div>
    @endif
@endsection