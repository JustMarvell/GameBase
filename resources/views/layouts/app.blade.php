<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Beranda') - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex min-h-screen flex-col bg-base-200 font-sans antialiased">

    <header x-data="{ menu: false }" class="sticky top-0 z-30 bg-base-100 shadow-sm">
        <nav class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-lg font-extrabold text-primary">
                <x-icon name="star" class="size-6" /> {{ config('app.name') }}
            </a>

            <div class="hidden items-center gap-6 text-sm font-semibold md:flex">
                <a href="{{ route('home') }}" class="hover:text-primary">Beranda</a>
                <a href="{{ url('/games') }}" class="hover:text-primary">Game</a>
            </div>

            <div class="flex items-center gap-2">
                @guest
                    <a href="{{ route('login') }}" class="btn btn-text btn-sm">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Daftar</a>
                @endguest

                @auth
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="btn btn-text btn-sm gap-2">
                            <x-icon name="user" class="size-5" /> {{ auth()->user()->name }}
                        </button>
                        <div x-show="open" x-cloak @click.outside="open = false" x-transition
                            class="card absolute right-0 mt-2 w-52 shadow-xl ring-1 ring-base-content/10">
                            <ul class="menu w-full">
                                @role('admin')
                                <li><a href="{{ route('admin.dashboard') }}">Dashboard Admin</a></li>
                                @endrole
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
                @endauth

                <button @click="menu = !menu" class="btn btn-text btn-square md:hidden" aria-label="Menu">
                    <x-icon name="menu" class="size-6" />
                </button>
            </div>
        </nav>

        {{-- Menu HP --}}
        <div x-show="menu" x-cloak x-transition
            class="space-y-2 border-t border-base-content/10 px-4 py-3 text-sm font-semibold md:hidden">
            <a href="{{ route('home') }}" class="block">Beranda</a>
            <a href="{{ url('/games') }}" class="block">Game</a>
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

    <footer class="bg-slate-900 py-6 text-center text-sm text-slate-400">
        © {{ date('Y') }} {{ config('app.name') }}. Dibuat dengan Laravel.
    </footer>

</body>

</html>