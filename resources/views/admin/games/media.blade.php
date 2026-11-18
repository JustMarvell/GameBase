@extends('layouts.admin')

@section('title', 'Galeri: ' . $game->title)

@section('content')
    <div class="mb-4 flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.games.index') }}" class="btn btn-text btn-sm">
            <x-icon name="arrow-left" class="size-4" /> Daftar Game
        </a>
        <a href="{{ route('admin.games.edit', $game) }}" class="btn btn-text btn-sm">Edit data game</a>
    </div>

    {{-- ===== UPLOAD ===== --}}
    <div class="card mb-6 max-w-3xl">
        <div class="card-body">
            <h3 class="font-bold">Unggah gambar / video</h3>
            <p class="text-sm text-base-content/60">
                Gambar maks 10MB (JPG, PNG, WEBP, GIF). Video maks 100MB (MP4, WEBM, MOV).
                Maks 20 file sekali unggah. Gunakan gambar landscape (16:9) untuk hasil terbaik.
            </p>

            <form method="POST" action="{{ route('admin.games.media.store', $game) }}" enctype="multipart/form-data"
                  x-data="{ busy: false }" @submit="busy = true" class="mt-2 space-y-3">
                @csrf

                <input type="file" name="files[]" multiple required class="input"
                       accept="image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm,video/quicktime">

                @if ($errors->any())
                    <div class="alert alert-soft alert-error text-sm">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <button type="submit" class="btn btn-primary" :disabled="busy">
                    <span x-show="busy" x-cloak class="loading loading-spinner loading-sm"></span>
                    <span x-text="busy ? 'Mengunggah... jangan tutup halaman' : 'Unggah'"></span>
                </button>
            </form>
        </div>
    </div>

    {{-- ===== DAFTAR MEDIA (drag untuk mengurutkan) ===== --}}
    <div class="card max-w-3xl"
         x-data="mediaSorter('{{ route('admin.games.media.reorder', $game) }}', '{{ csrf_token() }}')">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <h3 class="font-bold">Urutan galeri ({{ $game->media->count() }})</h3>
                <span class="text-sm text-success" x-text="status"></span>
            </div>
            <p class="text-sm text-base-content/60">
                Seret ikon <x-icon name="menu" class="inline size-4" /> untuk mengubah urutan.
                Urutan teratas tampil pertama di carousel.
            </p>

            <ul x-ref="list" class="mt-3 space-y-3">
                @forelse ($game->media as $item)
                    <li data-id="{{ $item->id }}"
                        class="flex items-center gap-4 rounded-xl border border-base-content/10 bg-base-100 p-3">
                        <span class="drag-handle cursor-grab touch-none text-base-content/40" title="Seret untuk mengurutkan">
                            <x-icon name="menu" class="size-6" />
                        </span>

                        @if ($item->isVideo())
                            <video src="{{ $item->url }}#t=0.5" preload="metadata" muted
                                   class="h-20 w-36 shrink-0 rounded-lg bg-black object-cover"></video>
                        @else
                            <img src="{{ $item->url }}" alt="" class="h-20 w-36 shrink-0 rounded-lg object-cover">
                        @endif

                        <div class="min-w-0 flex-1">
                            <span class="badge badge-soft {{ $item->isVideo() ? 'badge-warning' : 'badge-primary' }} badge-sm">
                                {{ $item->isVideo() ? 'Video' : 'Gambar' }}
                            </span>
                            <p class="mt-1 truncate text-xs text-base-content/60">{{ basename($item->path) }}</p>
                        </div>

                        <button type="button" class="btn btn-text btn-square btn-sm text-error" aria-label="Hapus"
                                @click="$dispatch('open-delete', @js(['action' => route('admin.media.destroy', $item), 'name' => $item->isVideo() ? 'video ini' : 'gambar ini']))">
                            <x-icon name="trash" class="size-4" />
                        </button>
                    </li>
                @empty
                    <li class="py-6 text-center text-base-content/60">Belum ada media. Unggah di atas.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <x-confirm-delete />
@endsection