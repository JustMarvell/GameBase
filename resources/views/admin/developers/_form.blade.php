@csrf

<div class="space-y-5">

    {{-- Foto --}}
    <div x-data="{ preview: null, remove: false }" class="flex flex-wrap items-center gap-5">
        <div :class="remove && 'opacity-30'" class="transition">
            <template x-if="preview">
                <img :src="preview" alt="Pratinjau" class="size-24 rounded-full object-cover ring-2 ring-primary/50">
            </template>
            <div x-show="!preview">
                @isset($developer)
                    <x-avatar :user="$developer" size="size-24" text="text-3xl" />
                @else
                    <span class="flex size-24 items-center justify-center rounded-full bg-base-300 text-base-content/40">
                        <x-icon name="user" class="size-10" />
                    </span>
                @endisset
            </div>
        </div>

        <div class="min-w-0 flex-1 space-y-2">
            <label for="avatar" class="label-text font-semibold">Foto developer</label>
            <input id="avatar" type="file" name="avatar" accept="image/jpeg,image/png,image/webp" class="input"
                   @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null; if (preview) remove = false">
            <p class="text-xs text-base-content/60">JPG, PNG atau WEBP, maks 5MB.</p>

            @if (isset($developer) && $developer->avatar)
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="remove_avatar" value="1" x-model="remove" class="checkbox checkbox-error checkbox-sm">
                    <span class="label-text">Hapus foto saat ini</span>
                </label>
            @endif

            @error('avatar') <p class="text-sm text-error">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label for="name" class="label-text font-semibold">Nama developer / studio</label>
        <input id="name" type="text" name="name" value="{{ old('name', $developer->name ?? '') }}" required class="input">
        @error('name') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="email" class="label-text font-semibold">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email', $developer->email ?? '') }}" required class="input">
        @error('email') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="website" class="label-text font-semibold">Website (opsional)</label>
        <input id="website" type="url" name="website" value="{{ old('website', $developer->website ?? '') }}"
               placeholder="https://..." class="input">
        @error('website') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div x-data="{ len: {{ mb_strlen(old('bio', $developer->bio ?? '')) }} }">
        <label for="bio" class="label-text font-semibold">Deskripsi developer (opsional)</label>
        <textarea id="bio" name="bio" rows="4" maxlength="300" class="textarea"
                  @input="len = $event.target.value.length">{{ old('bio', $developer->bio ?? '') }}</textarea>
        <p class="mt-1 text-right text-xs text-base-content/50"><span x-text="len"></span>/300</p>
        @error('bio') <p class="text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label for="password" class="label-text font-semibold">
                {{ isset($developer) ? 'Password baru (kosongkan jika tidak diubah)' : 'Password' }}
            </label>
            <input id="password" type="password" name="password" class="input" @if (! isset($developer)) required @endif>
            @error('password') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password_confirmation" class="label-text font-semibold">Konfirmasi password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="input"
                   @if (! isset($developer)) required @endif>
        </div>
    </div>

    <div class="flex justify-end gap-2">
        <a href="{{ route('admin.developers.index') }}" class="btn btn-text">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</div>