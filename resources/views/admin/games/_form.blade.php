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

    {{-- Cover dengan preview (Alpine.js) --}}
    <div x-data="{ preview: @js(isset($game) ? $game->cover_url : null) }">
        <label for="cover" class="label-text font-semibold">Cover (maks 2MB)</label>
        <input id="cover" type="file" name="cover" accept="image/*" class="input"
            @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : preview">
        @error('cover')
        <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror

        <img x-show="preview" x-cloak :src="preview" alt="Preview cover"
            class="mt-3 h-48 rounded-lg object-cover shadow">
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