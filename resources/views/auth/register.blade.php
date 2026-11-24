<!DOCTYPE html>
<html lang="id" data-theme="gamebase">
<head>
    <title>Daftar - {{ config('app.name') }}</title>
    @include('layouts._head')
</head>
<body class="techno-bg min-h-screen font-sans antialiased">
<div class="flex min-h-screen items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <div class="mb-8 text-center">
            <div class="hero-gradient glow mx-auto mb-4 flex size-14 items-center justify-center rounded-xl text-white">
                <x-icon name="gamepad" class="size-8" />
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight">{{ config('app.name') }}</h1>
            <p class="mt-1 text-sm text-base-content/60">Buat akun baru</p>
        </div>

        <div class="card bg-base-100/90 shadow-2xl backdrop-blur">
            <div class="card-body gap-5 p-8">
                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="label-text font-semibold">Nama</label>
                        <div class="input items-center gap-2">
                            <x-icon name="user" class="my-auto size-5 shrink-0 text-base-content/50" />
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus class="grow">
                        </div>
                        @error('name') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="label-text font-semibold">Email</label>
                        <div class="input items-center gap-2">
                            <x-icon name="envelope" class="my-auto size-5 shrink-0 text-base-content/50" />
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required class="grow">
                        </div>
                        @error('email') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password" class="label-text font-semibold">Password</label>
                        <div class="input items-center gap-2">
                            <x-icon name="lock" class="my-auto size-5 shrink-0 text-base-content/50" />
                            <input id="password" type="password" name="password" required class="grow">
                        </div>
                        @error('password') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="label-text font-semibold">Konfirmasi Password</label>
                        <div class="input items-center gap-2">
                            <x-icon name="lock" class="my-auto size-5 shrink-0 text-base-content/50" />
                            <input id="password_confirmation" type="password" name="password_confirmation" required class="grow">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg glow">Daftar</button>
                </form>

                <p class="text-center text-sm">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="link link-primary font-semibold">Masuk</a>
                </p>
            </div>
        </div>
    </div>
</div>
</body>
</html>