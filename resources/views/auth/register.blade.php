<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-secondary via-secondary to-primary font-sans antialiased">
<div class="flex min-h-screen items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <div class="mb-8 text-center text-white">
            <div class="mx-auto mb-4 flex size-14 items-center justify-center rounded-2xl bg-white/15 ring-1 ring-white/30">
                <x-icon name="star" class="size-8" />
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight">{{ config('app.name') }}</h1>
            <p class="mt-1 text-sm text-white/80">Buat akun baru</p>
        </div>

        <div class="card shadow-2xl">
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

                    <button type="submit" class="btn btn-primary btn-block btn-lg">Daftar</button>
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