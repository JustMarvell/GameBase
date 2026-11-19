@csrf

@php
$selected = old('platforms', isset($game) ? $game->platforms->pluck('id')->all() : []);
@endphp

<div class="space-y-5">
    <div>
        <label for="title" class="label-text font-semibold">Judul</label>
        <input id="title" type="text" name="title" class="input" value="{{ old('title', $game->title ?? '') }}"
            required>
        @error('title')
        <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label for="developer" class="label-text font-semibold">Developer</label>
            <input id="developer" type="text" name="developer" class="input"
                value="{{ old('developer', $game->developer ?? '') }}" required>
            @error('developer')
            <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="release_year" class="label-text font-semibold">Tahun Rilis</label>
            <input id="release_year" type="number" name="release_year" class="input"
                value="{{ old('release_year', $game->release_year ?? '') }}" required>
            @error('release_year')
            <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label for="genre_id" class="label-text font-semibold">Genre</label>
        <select id="genre_id" name="genre_id" class="select" required>
            <option value="">-- Pilih genre --</option>
            @foreach ($genres as $genre)
                <option value="{{ $genre->id }}" @selected(old('genre_id', $game->genre_id ?? '') == $genre->id)>
                    {{ $genre->name }}
                </option>
            @endforeach
        </select>
        @error('genre_id')
        <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <span class="label-text font-semibold">Platform</span>
        <div class="mt-2 flex flex-wrap gap-x-6 gap-y-2">
            @foreach ($platforms as $platform)
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="platforms[]" value="{{ $platform->id }}"
                        class="checkbox checkbox-primary checkbox-sm" @checked(in_array($platform->id, $selected))>
                    <span class="label-text">{{ $platform->name }}</span>
                </label>
            @endforeach
        </div>
        @error('platforms')
        <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    {{-- Cover potret: unggah file atau tempel link, lalu atur bagian yang ditampilkan --}}
    <div x-data="coverField({
            mode: @js(old('cover_mode', isset($game) && $game->cover_link ? 'link' : 'upload')),
            fileSrc: @js(isset($game) && $game->cover ? asset('storage/' . $game->cover) : null),
            linkUrl: @js(old('cover_link', $game->cover_link ?? '')),
            x: {{ (int) old('cover_focus_x', $game->cover_focus_x ?? 50) }},
            y: {{ (int) old('cover_focus_y', $game->cover_focus_y ?? 50) }},
         })">
        <input type="hidden" name="cover_mode" :value="mode">
        <input type="hidden" name="cover_focus_x" :value="Math.round(x)">
        <input type="hidden" name="cover_focus_y" :value="Math.round(y)">
    
        <span class="label-text font-semibold">Poster / Cover potret</span>
        <div class="mt-2 flex gap-2">
            <button type="button" @click="mode = 'upload'"
                :class="mode === 'upload' ? 'btn-primary' : 'btn-soft btn-primary'" class="btn btn-sm">Unggah file</button>
            <button type="button" @click="mode = 'link'" :class="mode === 'link' ? 'btn-primary' : 'btn-soft btn-primary'"
                class="btn btn-sm">Tempel link</button>
        </div>
    
        {{-- Unggah file --}}
        <div x-show="mode === 'upload'" x-cloak class="mt-3">
            <input id="cover" type="file" name="cover" accept="image/*" class="input" @change="onFile($event)">
            <p class="mt-1 text-xs text-base-content/60">
                Maks 10MB. Tampil di beranda dan daftar game dengan rasio 3:4.
                Gambar landscape &amp; video diatur lewat "Kelola Galeri".
            </p>
            @error('cover')
            <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
        </div>
    
        {{-- Tempel link --}}
        <div x-show="mode === 'link'" x-cloak class="mt-3">
            <input type="url" name="cover_link" x-model.debounce.400ms="linkUrl" autocomplete="off" class="input"
                placeholder="https://... (link langsung ke gambar)">
            <p x-show="linkStatus === 'loading'" x-cloak class="mt-2 flex items-center gap-2 text-sm text-base-content/60">
                <span class="loading loading-spinner loading-xs"></span> Memuat pratinjau...
            </p>
            <p x-show="linkStatus === 'error'" x-cloak x-text="linkMessage" class="mt-2 text-sm text-error"></p>
            @error('cover_link')
            <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
        </div>
    
        {{-- Pengaturan bagian yang ditampilkan --}}
        <div x-show="showEditor" x-cloak class="mt-4 flex flex-wrap items-start gap-6">
            <div class="min-w-0">
                <p class="label-text font-semibold">Atur bagian yang ditampilkan</p>
                <p class="text-xs text-base-content/60">
                    Klik atau seret untuk memindahkan bingkai. Bagian di dalam bingkai itulah yang tampil sebagai cover.
                </p>
    
                <div x-ref="stage"
                    class="relative mt-2 inline-block max-w-full cursor-move touch-none select-none overflow-hidden rounded-xl bg-black"
                    @pointerdown="startDrag($event)" @pointermove="drag($event)" @pointerup="endDrag()"
                    @pointercancel="endDrag()">
                    <img :src="previewSrc" alt="" referrerpolicy="no-referrer" draggable="false" @load="imgLoaded($event)"
                        x-on:error="imgFailed()" class="block max-h-72 max-w-full">
                    <div class="pointer-events-none absolute rounded border-2 border-white" :style="frameStyle"></div>
                </div>
    
                <div class="mt-2">
                    <button type="button" class="btn btn-text btn-xs" @click="x = 50; y = 50">Reset ke tengah</button>
                </div>
            </div>
    
            <div>
                <p class="label-text font-semibold">Hasil di situs</p>
                <div class="mt-2 aspect-[3/4] w-32 overflow-hidden rounded-xl bg-base-300 ring-1 ring-base-content/10">
                    <img :src="previewSrc" alt="" referrerpolicy="no-referrer" class="size-full object-cover"
                        :style="`object-position: ${x}% ${y}%`">
                </div>
            </div>
        </div>
    </div>

    <div>
        <label for="description" class="label-text font-semibold">Deskripsi (opsional)</label>
        <textarea id="description" name="description" rows="5"
            class="textarea">{{ old('description', $game->description ?? '') }}</textarea>
        @error('description')
        <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div class="flex justify-end gap-2">
        <a href="{{ route('admin.games.index') }}" class="btn btn-text">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</div>