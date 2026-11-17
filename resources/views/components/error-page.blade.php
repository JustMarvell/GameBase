@props(['code', 'title', 'message'])

<div class="py-16 text-center">
    <p class="text-7xl font-extrabold text-primary">{{ $code }}</p>
    <h1 class="mt-4 text-2xl font-bold">{{ $title }}</h1>
    <p class="mt-2 text-base-content/60">{{ $message }}</p>
    <a href="{{ route('home') }}" class="btn btn-primary mt-6">Kembali ke Beranda</a>
</div>