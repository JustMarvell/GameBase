@csrf

<div class="space-y-5">
    <div>
        <label for="name" class="label-text font-semibold">Nama Platform</label>
        <input id="name" type="text" name="name" class="input"
               value="{{ old('name', $platform->name ?? '') }}" required>
        @error('name') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div class="flex justify-end gap-2">
        <a href="{{ route('admin.platforms.index') }}" class="btn btn-text">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</div>