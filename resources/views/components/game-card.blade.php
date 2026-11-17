@props(['game'])

<a href="{{ route('games.show', $game) }}"
   class="card overflow-hidden transition hover:-translate-y-1 hover:shadow-lg">
    <div class="relative aspect-3/4 bg-base-200">
        @if ($game->cover_url)
            <img src="{{ $game->cover_url }}" alt="{{ $game->title }}" class="size-full object-cover" loading="lazy">
        @else
            <div class="flex size-full items-center justify-center text-base-content/30">
                <x-icon name="photo" class="size-12" />
            </div>
        @endif
        <span class="badge badge-secondary absolute left-2 top-2">{{ $game->genre->name }}</span>
    </div>

    <div class="card-body gap-1 p-4">
        <h3 class="line-clamp-1 font-bold">{{ $game->title }}</h3>
        <div class="flex items-center justify-between text-sm text-base-content/60">
            <span>{{ $game->release_year }}</span>
            <span class="flex items-center gap-1 font-semibold text-base-content">
                <x-icon name="star" class="size-4 fill-amber-400 text-amber-400" />
                {{ $game->reviews_avg_rating ? number_format($game->reviews_avg_rating, 1) : '-' }}
            </span>
        </div>
    </div>
</a>