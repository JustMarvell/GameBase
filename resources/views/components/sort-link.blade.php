@props(['column', 'label'])

@php
    $active = request('sort') === $column;
    $nextDir = $active && request('dir') === 'asc' ? 'desc' : 'asc';
@endphp

<a href="{{ request()->fullUrlWithQuery(['sort' => $column, 'dir' => $nextDir, 'page' => null]) }}"
    class="inline-flex items-center gap-1 hover:text-primary {{ $active ? 'text-primary' : '' }}">
    {{ $label }}
    @if ($active)
        <x-icon :name="request('dir') === 'asc' ? 'chevron-up' : 'chevron-down'" class="size-3.5" />
    @else
        <x-icon name="chevron-up-down" class="size-3.5 opacity-40" />
    @endif
</a>