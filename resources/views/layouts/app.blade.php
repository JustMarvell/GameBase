<!DOCTYPE html>
<html lang="id" data-theme="gamebase">

<head>
    <title>@yield('title', 'Beranda') - {{ config('app.name') }}</title>
    @include('layouts._head')
</head>

<body class="techno-bg flex min-h-screen flex-col font-sans antialiased">

    <header x-data="{ menu: false }" class="sticky top-0 z-30 border-b border-base-content/10 bg-base-200/80 backdrop-blur-md">
        <nav class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="hero-gradient glow flex size-9 items-center justify-center rounded-xl text-white">
                    <x-icon name="gamepad" class="size-5" />
                </span>
                <span class="font-display text-xl font-bold tracking-tight">{{ config('app.name') }}</span>
            </a>

            @php
$navLink = fn($active) => 'relative py-5 transition-colors ' . ($active
    ? 'text-primary after:absolute after:inset-x-0 after:bottom-0 after:h-0.5 after:bg-primary'
    : 'text-base-content/70 hover:text-base-content');
            @endphp

            <div class="hidden items-center gap-6 text-sm font-semibold md:flex">
                <a href="{{ route('home') }}" class="{{ $navLink(request()->routeIs('home')) }}">Beranda</a>
                <a href="{{ route('games.index') }}" class="{{ $navLink(request()->routeIs('games.*')) }}">Game</a>
                @auth
                    <a href="{{ route('library.index') }}" class="{{ $navLink(request()->routeIs('library.*')) }}">Library Saya</a>
                @endauth
            </div>

            <form method="GET" action="{{ route('games.index') }}" class="hidden max-w-xs flex-1 md:block">
                <label class="input input-sm w-full items-center gap-2">
                    <x-icon name="search" class="size-4 text-base-content/50" />
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari game..." class="grow">
                </label>
            </form>

            <div class="flex items-center gap-2">
                @guest
                    <a href="{{ route('login') }}" class="btn btn-outline btn-secondary btn-sm">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Daftar</a>
                @endguest

                @auth
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="btn btn-soft btn-secondary btn-sm gap-2">
                            <x-avatar :user="auth()->user()" size="size-6" text="text-xs" /> {{ auth()->user()->name }}
                        </button>
                        <div x-show="open" x-cloak @click.outside="open = false" x-transition
                            class="card absolute right-0 mt-2 w-52 shadow-xl">
                            <ul class="menu w-full">
                                <li><a href="{{ route('users.show', auth()->user()) }}">Profil Saya</a></li>
                                <li><a href="{{ route('profile.edit') }}">Edit Profil</a></li>
                                <li><a href="{{ route('library.index') }}">Library Saya</a></li>
                                <li><a href="{{ route('reviews.mine') }}">Ulasan Saya</a></li>
                                @hasanyrole('admin|developer')
                                    <li>
                                        <a href="{{ route('admin.dashboard') }}">
                                            {{ auth()->user()->hasRole('admin') ? 'Dashboard Admin' : 'Panel Developer' }}
                                        </a>
                                    </li>
                                @endhasanyrole
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex w-full items-center gap-2 text-error">
                                            <x-icon name="logout" class="size-4" /> Keluar
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                @endauth

                <button @click="menu = !menu" class="btn btn-text btn-square md:hidden" aria-label="Menu">
                    <x-icon name="menu" class="size-6" />
                </button>
            </div>
        </nav>

        {{-- Menu HP --}}
        <div x-show="menu" x-cloak x-transition class="space-y-3 border-t border-base-content/10 px-4 py-3 text-sm font-semibold md:hidden">
            <form method="GET" action="{{ route('games.index') }}">
                <label class="input w-full items-center gap-2">
                    <x-icon name="search" class="size-4 text-base-content/50" />
                    <input type="search" name="q" placeholder="Cari game..." class="grow">
                </label>
            </form>
            <a href="{{ route('home') }}" class="block rounded-lg px-3 py-2 hover:bg-base-300/60">Beranda</a>
            <a href="{{ route('games.index') }}" class="block rounded-lg px-3 py-2 hover:bg-base-300/60">Game</a>
            @auth
                <a href="{{ route('library.index') }}" class="block rounded-lg px-3 py-2 hover:bg-base-300/60">Library Saya</a>
            @endauth
        </div>
    </header>

    <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-8">
        @if (session('success') || session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
                class="alert alert-soft mb-6 flex items-center gap-3 {{ session('success') ? 'alert-success' : 'alert-error' }}"
                role="alert">
                <span class="grow">{{ session('success') ?? session('error') }}</span>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="border-t border-base-content/10 bg-base-200">
        <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-8 text-sm md:flex-row md:items-center md:justify-between">
            <div class="flex items-center gap-2 font-display font-bold">
                <span class="hero-gradient flex size-7 items-center justify-center rounded-lg text-white">
                    <x-icon name="gamepad" class="size-4" />
                </span>
                {{ config('app.name') }}
            </div>
            <p class="text-base-content/60">Temukan, mainkan, dan ulas game favoritmu.</p>
            <p class="text-base-content/50">&copy; {{ date('Y') }} {{ config('app.name') }}. Made With °⋆♡︎.ೃ࿔:･ by VelloSaurus.</p>
        </div>
    </footer>

</body>

</html>