@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
    <div class="mx-auto max-w-2xl space-y-6">
        <h1 class="border-l-4 border-primary pl-3 text-2xl font-extrabold">Edit Profil</h1>

        {{-- ===== DATA PROFIL ===== --}}
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="card">
            @csrf
            @method('PUT')

            <div class="card-body gap-5">

                {{-- Foto profil --}}
                <div x-data="{ preview: null, remove: false }" class="flex flex-wrap items-center gap-5">
                    <div :class="remove && 'opacity-30'" class="transition">
                        <template x-if="preview">
                            <img :src="preview" alt="Pratinjau" class="size-24 rounded-full object-cover ring-2 ring-primary/50">
                        </template>
                        <div x-show="!preview">
                            <x-avatar :user="$user" size="size-24" text="text-3xl" />
                        </div>
                    </div>

                    <div class="min-w-0 flex-1 space-y-2">
                        <label for="avatar" class="label-text font-semibold">Foto profil</label>
                        <input id="avatar" type="file" name="avatar" accept="image/jpeg,image/png,image/webp" class="input"
                               @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null; if (preview) remove = false">
                        <p class="text-xs text-base-content/60">JPG, PNG atau WEBP, maks 5MB. Foto ditampilkan dalam bentuk lingkaran.</p>

                        @if ($user->avatar)
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="remove_avatar" value="1" x-model="remove" class="checkbox checkbox-error checkbox-sm">
                                <span class="label-text">Hapus foto saat ini</span>
                            </label>
                        @endif

                        @error('avatar') <p class="text-sm text-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="name" class="label-text font-semibold">Nama</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required class="input">
                    @error('name') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="label-text font-semibold">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required class="input">
                    <p class="mt-1 text-xs text-base-content/60">Email tidak ditampilkan di profil publik.</p>
                    @error('email') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                </div>

                <div x-data="{ len: {{ mb_strlen(old('bio', $user->bio ?? '')) }} }">
                    <label for="bio" class="label-text font-semibold">Bio (opsional)</label>
                    <textarea id="bio" name="bio" rows="4" maxlength="300" class="textarea"
                              placeholder="Ceritakan sedikit tentang dirimu atau game favoritmu..."
                              @input="len = $event.target.value.length">{{ old('bio', $user->bio) }}</textarea>
                    <p class="mt-1 text-right text-xs text-base-content/50"><span x-text="len"></span>/300</p>
                    @error('bio') <p class="text-sm text-error">{{ $message }}</p> @enderror
                </div>

                <label class="flex items-center justify-between gap-4 rounded-xl border border-base-content/10 p-4">
                    <span>
                        <span class="block font-semibold">Tampilkan library di profil publik</span>
                        <span class="block text-sm text-base-content/60">Jika dimatikan, hanya Anda yang bisa melihat daftar game di library Anda.</span>
                    </span>
                    <input type="checkbox" name="library_public" value="1" class="switch switch-primary"
                           @checked(old('library_public', $user->library_public))>
                </label>

                <div class="flex justify-end gap-2">
                    <a href="{{ route('users.show', $user) }}" class="btn btn-text">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>

        {{-- ===== UBAH PASSWORD ===== --}}
        <form method="POST" action="{{ route('profile.password') }}" class="card">
            @csrf
            @method('PUT')

            <div class="card-body gap-4">
                <h2 class="font-bold">Ubah password</h2>

                <div>
                    <label for="current_password" class="label-text font-semibold">Password saat ini</label>
                    <input id="current_password" type="password" name="current_password" required class="input">
                    @error('current_password', 'updatePassword') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" class="label-text font-semibold">Password baru</label>
                    <input id="password" type="password" name="password" required class="input">
                    @error('password', 'updatePassword') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="label-text font-semibold">Konfirmasi password baru</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required class="input">
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="btn btn-primary">Ubah Password</button>
                </div>
            </div>
        </form>
    </div>
@endsection