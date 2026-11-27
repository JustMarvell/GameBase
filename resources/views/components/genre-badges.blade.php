@props(['game', 'link' => false, 'size' => 'badge-sm'])

<div {{ $attributes->merge(['class' => 'flex flex-wrap gap-1']) }}>
    @foreach ($game->genres as $genre)
        @if ($link)
            <a href="{{ route('genres.show', $genre) }}" class="badge badge-soft badge-secondary {{ $size }}">{{ $genre->name }}</a>
        @else
            <span class="badge badge-soft badge-secondary {{ $size }}">{{ $genre->name }}</span>
        @endif
    @endforeach
</div>