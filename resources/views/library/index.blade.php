@extends('layouts.app')

@section('title', 'Library Saya')

@section('content')
    <div class="mb-6 flex items-end justify-between gap-4">
        <h1 class="border-l-4 border-primary pl-3 text-2xl font-extrabold">Library Saya</h1>
        <span class="text-sm text-base-content/60">{{ $games->total() }} game</span>
    </div>

    @if ($games->isEmpty())
        <div class="card">
            <div class="card-body items-center py-12 text-center">
                <x-icon name="bookmark" class="size-10 text-base-content/30" />
                <p class="text-base-content/60">Library Anda masih kosong.</p>
                <a href="{{ route('games.index') }}" class="btn btn-primary mt-2">Jelajahi Game</a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4 xl:grid-cols-6">
            @foreach ($games as $game)
                <div class="flex flex-col gap-2">
                    <x-game-card :game="$game" />

                    <form method="POST" action="{{ route('library.destroy', $game) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-soft btn-error btn-sm btn-block">
                            <x-icon name="trash" class="size-4" /> Hapus
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $games->links() }}</div>
    @endif
@endsection