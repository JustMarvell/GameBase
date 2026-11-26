@props(['code', 'title', 'message'])

<div class="mx-auto max-w-lg py-16">
    <div class="card">
        <div class="card-body items-center gap-2 py-12 text-center">
            <p
                class="bg-linear-to-br from-primary to-secondary bg-clip-text font-display text-7xl font-extrabold text-transparent">
                {{ $code }}</p>
            <h1 class="mt-2 text-2xl font-bold">{{ $title }}</h1>
            <p class="text-base-content/60">{{ $message }}</p>
            <a href="{{ route('home') }}" class="btn btn-primary glow mt-4">Kembali ke Beranda</a>
        </div>
    </div>
</div>