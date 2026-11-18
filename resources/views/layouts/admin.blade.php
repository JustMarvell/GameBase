<!DOCTYPE html>
<html lang="id" data-theme="gamebase">

<head>
    <title>@yield('title', 'Dashboard') - Admin {{ config('app.name') }}</title>
    @include('layouts._head')
</head>

<body class="bg-base-200 font-sans antialiased">
    {{-- sidebarOpen: status sidebar di layar kecil (HP) --}}
    <div x-data="{ sidebarOpen: false }" class="flex min-h-screen">
        {{-- Latar gelap saat sidebar terbuka di HP --}}
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" x-transition.opacity
            class="fixed inset-0 z-30 bg-black/50 lg:hidden"></div>
        {{-- ================= SIDEBAR ================= --}}
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-slate-900 text-slate-300 transition-transform duration-200 lg:static lg:translate-x-0">
            <div class="flex h-16 items-center gap-3 border-b border-white/10 px-6">
                <div class="hero-gradient flex size-9 items-center justify-center rounded-lg text-white">
                    <x-icon name="gamepad" class="size-5" />
                </div>
                    <span class="text-lg font-extrabold text-white">{{ config('app.name') }}</span>
                </div>
            @php
$menus = [
    ['admin.dashboard', 'Dashboard', 'dashboard'],
    ['admin.games.index', 'Game', 'star'],
    ['admin.genres.index', 'Genre', 'tag'],
    ['admin.platforms.index', 'Platform', 'globe'],
    ['admin.reviews.index', 'Ulasan', 'newspaper'],
];
            @endphp
            {{-- Komponen "menu" FlyonUI --}}
            <ul class="menu w-full flex-1 gap-1 px-3 py-6">
                <li class="menu-title text-xs tracking-wider text-slate-500 uppercase">Menu</li>
                @foreach ($menus as [$route, $label, $icon])
                            @php $active = request()->routeIs(str_replace('.index', '.*', $route)); @endphp
                            <li>
                                <a href="{{ route($route) }}" class="{{ $active ? 'bg-secondary text-secondary-content shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                    <x-icon :name="$icon" class="size-5" />
                                    {{ $label }}
                                </a>
                            </li>
                @endforeach
                <li class="menu-title mt-4 text-xs tracking-wider text-slate-500 uppercase">Website</li>
                <li>
                    <a href="{{ url('/') }}" target="_blank" class="text-slate-300 hover:bg-white/10 hover:text-white">
                        <x-icon name="globe" class="size-5" />
                        Lihat Website
                    </a>
                </li>
            </ul>
        </aside>
        {{-- ================= KONTEN ================= --}}
        <div class="flex min-w-0 flex-1 flex-col">
            {{-- Topbar (komponen "navbar" FlyonUI) --}}
            <header class="navbar sticky top-0 z-20 h-16 border-b border-base-content/10 bg-base-100 px-4 lg:px-8">
                <div class="navbar-start items-center gap-3">
                    <button @click="sidebarOpen = true" class="btn btn-text btn-square lg:hidden"
                        aria-label="Buka menu">
                        <x-icon name="menu" class="size-6" />
                    </button>
                    <h1 class="text-lg font-bold">@yield('title', 'Dashboard')</h1>
                </div>
                {{-- Dropdown akun (dibuka/tutup dengan Alpine.js) --}}
                <div class="navbar-end">
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="btn btn-text gap-2 px-2">
                            <span class="avatar avatar-placeholder">
                                <span class="bg-secondary/15 text-secondary size-8 rounded-full text-sm font-bold">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                            </span>
                            <span class="hidden text-sm font-semibold sm:block">{{ auth()->user()->name }}</span>
                            <x-icon name="chevron-down" class="size-4 opacity-60" />
                        </button>
                        <div x-show="open" x-cloak @click.outside="open = false" x-transition class="card absolute right-0 mt-2 w-52 shadow-xl ring-1 ring-base-content/10">
                            <ul class="menu w-full">
                                <li class="menu-title text-xs">{{ auth()->user()->email }}</li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="text-error flex w-full items-center gap-2">
                                            <x-icon name="logout" class="size-4" /> Keluar
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </header>
            <main class="flex-1 p-4 lg:p-8">
                {{-- Notifikasi (komponen "alert" FlyonUI) yang hilang sendiri setelah 4 detik --}}
                @if (session('success') || session('error'))
                                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition class="alert alert-soft mb-6 flex items-center gap-3 {{ session('success') ? 'alert-success' : 'alert-error' }}" role="alert">
                                        <x-icon :name="session('success') ? 'check-circle' : 'warning'" class="size-5" />
                                        <span class="grow">{{ session('success') ?? session('error') }}</span>
                                        <button @click="show = false" aria-label="Tutup"><x-icon name="x-mark" class="size-4" /></button>
                                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</body>

</html>