<!DOCTYPE html>
<html lang="id" data-theme="gamebase">

<head>
    <title>Login - {{ config('app.name') }}</title>
    @include('layouts._head')
</head>

<body class="techno-bg min-h-screen font-sans antialiased">
    <div class="flex min-h-screen items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            {{-- Logo & judul --}}
            <div class="mb-8 text-center text-white">
                <div class="mx-auto mb-4 flex size-14 items-center justify-center rounded-2xl bg-white/15 ring-1 ring-white/30">
                    <x-icon name="gamepad" class="size-8" />
                </div>
                <h1 class="text-2xl font-extrabold tracking-tight">{{ config('app.name') }}</h1>
                <p class="mt-1 text-sm text-white/80">Masuk ke akun Anda</p>
            </div>
            {{-- Kartu form login (komponen "card" FlyonUI) --}}
            <div class="card shadow-2xl">
                <div class="card-body gap-5 p-8">
                    {{-- Pesan error dari Fortify (komponen "alert" FlyonUI) --}}
                    @if ($errors->any())
                        <div class="alert alert-soft alert-error flex items-start gap-3 text-sm" role="alert">
                            <x-icon name="warning" class="size-5 shrink-0" />
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf
                        {{-- Email: "input" FlyonUI sebagai pembungkus ikon + input --}}
                        <div>
                            <label for="email" class="label-text font-semibold">Email</label>
                            <div class="input items-center gap-2">
                                <x-icon name="envelope" class="my-auto size-5 shrink-0 text-base-
content/50" />
                                <input id="email" type="email" name="email" value="{{
    old('email') }}" required autofocus class="grow" placeholder="nama@email.com">
                            </div>
                        </div>
                        {{-- Password dengan tombol lihat/sembunyikan (Alpine.js) --}}
                        <div x-data="{ show: false }">
                            <label for="password" class="label-text font-
semibold">Password</label>
                            <div class="input items-center gap-2">
                                <x-icon name="lock" class="my-auto size-5 shrink-0 text-base-
content/50" />
                                <input id="password" name="password" required class="grow"
                                    :type="show ? 'text' : 'password'" type="password" placeholder="••••••••">
                                <button type="button" @click="show = !show" class="my-auto text-
base-content/50 hover:text-primary">
                                    <x-icon name="eye" class="size-5" x-show="!show" />
                                    <x-icon name="eye-slash" class="size-5" x-show="show" x-cloak />
                                </button>
                            </div>
                        </div>
                        {{-- Ingat saya (komponen "checkbox" FlyonUI) --}}
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="remember" class="checkbox checkbox-
primary checkbox-sm">
                            <span class="label-text">Ingat saya</span>
                        </label>
                        <button type="submit" class="btn btn-primary btn-block btn-
lg">Masuk</button>
                    </form>
                    <p class="text-center text-sm">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="link link-primary font-semibold">Daftar</a>
                    </p>
                </div>
            </div>
            <p class="mt-6 text-center text-sm text-white/80">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-1 hover:text-white">
                    <x-icon name="arrow-left" class="size-4" /> Kembali ke website
                </a>
            </p>
        </div>
    </div>
</body>

</html>