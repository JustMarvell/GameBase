@extends('layouts.admin')

@section('title', 'Galeri: ' . $game->title)

@section('content')
    <div class="mb-4 flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.games.index') }}" class="btn btn-text btn-sm">
            <x-icon name="arrow-left" class="size-4" /> Daftar Game
        </a>
        <a href="{{ route('admin.games.edit', $game) }}" class="btn btn-text btn-sm">Edit data game</a>
    </div>

    {{-- ===== TAMBAH MEDIA ===== --}}
    <div class="card mb-6 max-w-3xl" x-data="{ tab: '{{ $errors->has('link') ? 'link' : 'file' }}' }">
        <div class="card-body">
            <h3 class="font-bold">Tambah gambar / video</h3>

            <div class="flex gap-2">
                <button type="button" @click="tab = 'file'" :class="tab === 'file' ? 'btn-primary' : 'btn-soft btn-primary'" class="btn btn-sm">Unggah file</button>
                <button type="button" @click="tab = 'link'" :class="tab === 'link' ? 'btn-primary' : 'btn-soft btn-primary'" class="btn btn-sm">Tempel link</button>
            </div>

            {{-- Tab: unggah file --}}
            <div x-show="tab === 'file'">
                <p class="text-sm text-base-content/60">
                    Gambar maks 10MB (JPG, PNG, WEBP, GIF). Video maks 100MB (MP4, WEBM, MOV).
                    Maks 20 file sekali unggah. Gunakan gambar landscape (16:9) untuk hasil terbaik.
                </p>

                <form method="POST" action="{{ route('admin.games.media.store', $game) }}" enctype="multipart/form-data"
                    x-data="{ busy: false }" @submit="busy = true" class="mt-2 space-y-3">
                    @csrf

                    <input type="file" name="files[]" multiple required class="input"
                        accept="image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm,video/quicktime">

                    @php
                        $fileErrors = collect($errors->messages())
                            ->filter(fn ($messages, $key) => str_starts_with($key, 'files'))
                            ->flatten();
                    @endphp
                    @if ($fileErrors->isNotEmpty())
                        <div class="alert alert-soft alert-error text-sm">
                            <ul>
                                @foreach ($fileErrors as $error)
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

            {{-- Tab: tempel link --}}
            <div x-show="tab === 'link'" x-cloak>
                <p class="text-sm text-base-content/60">
                    Tempel link gambar (harus langsung ke file gambar, boleh dari Google Drive dengan akses
                    "Siapa saja yang memiliki link") atau link video YouTube. Satu link sekali tambah.
                </p>

                <form method="POST" action="{{ route('admin.games.media.link', $game) }}"
                    x-data="linkPreview('gallery', @js(old('link', '')))" class="mt-2 space-y-3">
                    @csrf

                    <x-link-preview name="link" placeholder="https://..." />

                    @error('link')
                        <p class="text-sm text-error">{{ $message }}</p>
                    @enderror

                    <button type="submit" class="btn btn-primary" :disabled="!ready">Tambahkan</button>
                </form>
            </div>
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

                        @if ($item->isYoutube())
                            <img src="{{ $item->youtubeThumb() }}" alt="" referrerpolicy="no-referrer"
                                class="h-20 w-36 shrink-0 rounded-lg bg-black object-cover">
                        @elseif ($item->isVideo())
                            <video src="{{ $item->url }}#t=0.5" preload="metadata" muted
                                class="h-20 w-36 shrink-0 rounded-lg bg-black object-cover"></video>
                        @else
                            <img src="{{ $item->url }}" alt="" referrerpolicy="no-referrer"
                                class="h-20 w-36 shrink-0 rounded-lg bg-base-300 object-cover">
                        @endif

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap gap-1">
                                <span class="badge badge-soft {{ $item->isVideo() ? 'badge-warning' : 'badge-primary' }} badge-sm">
                                    {{ $item->isYoutube() ? 'YouTube' : ($item->isVideo() ? 'Video' : 'Gambar') }}
                                </span>
                                <span class="badge badge-soft badge-secondary badge-sm">{{ $item->isLink() ? 'Link' : 'File' }}</span>
                            </div>
                            <p class="mt-1 truncate text-xs text-base-content/60">{{ $item->isLink() ? $item->link_url : basename($item->path) }}</p>
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