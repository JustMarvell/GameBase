@props(['value' => 0])

<div x-data="{ rating: {{ (int) $value }}, hover: 0 }" class="mt-1 flex flex-wrap items-center gap-1">
    <input type="hidden" name="rating" :value="rating">

    @for ($n = 1; $n <= 10; $n++)
        <button type="button" @click="rating = {{ $n }}" @mouseenter="hover = {{ $n }}" @mouseleave="hover = 0"
            :class="(hover || rating) >= {{ $n }} ? 'text-amber-400' : 'text-base-content/20'" class="rounded transition hover:scale-110"
            aria-label="Beri nilai {{ $n }}">
            <x-icon name="star" class="size-6 fill-current" />
        </button>
    @endfor

    <span class="ml-2 text-sm font-bold" x-text="rating ? rating + '/10' : ''"></span>
</div>