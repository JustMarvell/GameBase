@props(['game'])

<a href="{{ route('games.show', $game) }}"
    class="group card overflow-hidden transition duration-200 hover:-translate-y-1 hover:glow">
    <div class="relative aspect-3/4 overflow-hidden bg-base-300">
        <div class="absolute inset-0 flex items-center justify-center text-base-content/30">
            <x-icon name="photo" class="size-12" />
        </div>
    
        @if ($game->cover_url)
            <img src="{{ $game->cover_url }}" alt="{{ $game->title }}" loading="lazy"
                referrerpolicy="no-referrer" onerror="this.remove()"
                style="object-position: {{ $game->cover_position }}"
                class="absolute inset-0 size-full object-cover transition duration-300 group-hover:scale-105">
        @endif
    
        <span class="badge badge-secondary absolute left-2 top-2">{{ $game->genre->name }}</span>
        @auth
            @if (auth()->user()->hasInLibrary($game->id))
                <span
                    class="glow absolute right-2 top-2 flex size-7 items-center justify-center rounded-full bg-primary text-primary-content"
                    title="Ada di library Anda">
                    <x-icon name="bookmark" class="size-4 fill-current" />
                </span>
            @endif
        @endauth
    
        <span
            class="absolute bottom-2 right-2 flex items-center gap-1 rounded-lg bg-black/70 px-2 py-1 text-xs font-bold text-white backdrop-blur">
            <x-icon name="star" class="size-3.5 fill-amber-400 text-amber-400" />
            {{ $game->reviews_avg_rating ? number_format($game->reviews_avg_rating, 1) : '-' }}
        </span>
    </div>

    <div class="card-body gap-0.5 p-4">
        <h3 class="line-clamp-1 font-display font-bold">{{ $game->title }}</h3>
        <p class="truncate text-sm text-base-content/60">{{ $game->release_year }} &middot; {{ $game->developer }}</p>
    </div>
</a>