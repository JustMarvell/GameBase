@props(['user', 'size' => 'size-10', 'text' => 'text-sm'])

@if ($user->avatar_url)
    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
         class="{{ $size }} shrink-0 rounded-full object-cover ring-2 ring-base-content/10">
@else
    <span class="hero-gradient {{ $size }} {{ $text }} flex shrink-0 items-center justify-center rounded-full font-bold text-white">
        {{ strtoupper(mb_substr($user->name, 0, 1)) }}
    </span>
@endif