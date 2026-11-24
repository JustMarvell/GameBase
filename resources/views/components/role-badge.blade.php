@props(['user'])

@if ($user->hasRole('admin'))
    <span class="badge badge-soft badge-accent badge-sm gap-1" title="Administrator">
        <x-icon name="shield-check" class="size-3" /> Admin
    </span>
@elseif ($user->hasRole('developer'))
    <span class="badge badge-soft badge-secondary badge-sm gap-1" title="Developer game">
        <x-icon name="code" class="size-3" /> Developer
    </span>
@endif