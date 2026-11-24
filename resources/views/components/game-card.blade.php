@props(['game'])

<a href="{{ route('games.show', $game) }}"
    class="group card overflow-hidden hover:-translate-y-0.5 hover:glow">
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
        <div class="pointer-events-none absolute inset-x-0 bottom-0 h-1/3 bg-linear-to-t from-black/50 to-transparent"></div>
    
        <span class="badge badge-sm absolute left-2 top-2 border-0 bg-base-200/85 text-secondary backdrop-blur">{{ $game->genre->name }}</span>
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
            class="absolute bottom-2 right-2 flex items-center gap-1 rounded-md bg-base-200/85 px-2 py-1 text-xs font-bold text-white backdrop-blur">
            <x-icon name="star" class="size-3.5 fill-amber-400 text-amber-400" />
            {{ $game->reviews_avg_rating ? number_format($game->reviews_avg_rating, 1) : '-' }}
        </span>
    </div>

    <div class="card-body gap-0.5 p-4">
        <h3 class="line-clamp-1 font-display font-bold transition-colors group-hover:text-primary">{{ $game->title }}</h3>
        <p class="truncate text-sm text-base-content/60">{{ $game->release_year }} &middot; {{ $game->developer }}</p>
    </div>
</a>