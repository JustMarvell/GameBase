@csrf

<div class="space-y-5">
    <div>
        <label for="name" class="label-text font-semibold">Nama Genre</label>
        <input id="name" type="text" name="name" class="input"
               value="{{ old('name', $genre->name ?? '') }}" required>
        @error('name') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="description" class="label-text font-semibold">Deskripsi (opsional)</label>
        <textarea id="description" name="description" rows="4"
            class="textarea">{{ old('description', $genre->description ?? '') }}</textarea>
        @error('description')
        <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div class="flex justify-end gap-2">
        <a href="{{ route('admin.genres.index') }}" class="btn btn-text">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</div>