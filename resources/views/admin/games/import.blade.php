@extends('layouts.admin')

@section('title', 'Impor CSV')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.games.index') }}" class="btn btn-text btn-sm">
            <x-icon name="arrow-left" class="size-4" /> Daftar Game
        </a>
    </div>

    @if (session('import_report'))
        <div class="mb-8">
            <h2 class="mb-3 border-l-4 border-primary pl-3 text-xl font-extrabold">Hasil impor</h2>
            @include('admin.games._import-report', ['report' => session('import_report')])
        </div>
    @endif

    <div class="card mb-6 max-w-3xl">
        <div class="card-body gap-4">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h3 class="font-bold">Unggah file CSV</h3>
                <a href="{{ route('admin.games.import.template') }}" class="btn btn-soft btn-secondary btn-sm">Unduh
                    template</a>
            </div>

            <form method="POST" action="{{ route('admin.games.import.upload') }}" enctype="multipart/form-data"
                x-data="{ busy: false }" @submit="busy = true" class="space-y-4">
                @csrf

                <div>
                    <input type="file" name="file" accept=".csv,text/csv" required class="input">
                    @error('file')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <span class="label-text font-semibold">Password untuk akun developer baru</span>
                    <div class="mt-2 space-y-2">
                        <label class="flex items-start gap-2">
                            <input type="radio" name="password_mode" value="pattern"
                                class="radio radio-primary radio-sm mt-0.5" @checked(old('password_mode', 'pattern') === 'pattern')>
                            <span class="text-sm">
                                Sesuai pola: <code>nama_developer_password</code>
                                <span class="block text-xs text-base-content/60">Mudah ditebak. Ganti segera sebelum situs
                                    dipublikasikan.</span>
                            </span>
                        </label>
                        <label class="flex items-start gap-2">
                            <input type="radio" name="password_mode" value="random"
                                class="radio radio-primary radio-sm mt-0.5" @checked(old('password_mode') === 'random')>
                            <span class="text-sm">
                                Acak
                                <span class="block text-xs text-base-content/60">Password ditampilkan sekali setelah
                                    impor.</span>
                            </span>
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" :disabled="busy">
                    <span x-show="busy" x-cloak class="loading loading-spinner loading-sm"></span>
                    <span x-text="busy ? 'Memproses...' : 'Unggah & lihat pratinjau'"></span>
                </button>
            </form>
        </div>
    </div>

    <div class="card max-w-3xl">
        <div class="card-body gap-3">
            <h3 class="font-bold">Format file</h3>
            <p class="text-sm text-base-content/70">
                Baris pertama berisi nama kolom (urutan bebas). Maksimal 1000 baris dan 5MB per file.
                Semua gambar dan video disimpan sebagai <strong>link</strong>, tidak ada yang diunduh.
            </p>

            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Kolom</th>
                            <th>Wajib</th>
                            <th>Isi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>title</code></td>
                            <td>Ya</td>
                            <td>Judul game. Simbol ™ ® © dihapus.</td>
                        </tr>
                        <tr>
                            <td><code>developer</code></td>
                            <td>Ya</td>
                            <td>Nama developer. Akun dibuat otomatis jika belum ada.</td>
                        </tr>
                        <tr>
                            <td><code>developer_description</code></td>
                            <td>Tidak</td>
                            <td>Deskripsi developer (maks 1000 karakter). Untuk akun yang sudah ada, hanya diisi jika
                                bio-nya kosong.</td>
                        </tr>
                        <tr>
                            <td><code>release_year</code></td>
                            <td>Ya</td>
                            <td>Tahun rilis, mis. 2020.</td>
                        </tr>
                        <tr>
                            <td><code>cover_image</code></td>
                            <td>Tidak</td>
                            <td>Link gambar cover potret.</td>
                        </tr>
                        <tr>
                            <td><code>description</code></td>
                            <td>Tidak</td>
                            <td>Deskripsi game.</td>
                        </tr>
                        <tr>
                            <td><code>genre</code></td>
                            <td>Ya</td>
                            <td>Satu atau beberapa genre, dipisah koma.</td>
                        </tr>
                        <tr>
                            <td><code>platforms</code></td>
                            <td>Tidak</td>
                            <td>Dipisah koma. PC, PS5, Xbox, Switch, Android, iOS dikenali otomatis, nama lain dibuat
                                sebagai platform baru.</td>
                        </tr>
                        <tr>
                            <td><code>images</code></td>
                            <td>Tidak</td>
                            <td>Link gambar galeri dipisah <code>|</code> (maks 10).</td>
                        </tr>
                        <tr>
                            <td><code>video</code></td>
                            <td>Tidak</td>
                            <td>Satu link YouTube.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <ul class="list-disc space-y-1 pl-5 text-sm text-base-content/70">
                <li>Judul yang sudah ada dilewati. Jika game itu belum punya akun developer, developer dari CSV ditetapkan.
                </li>
                <li>Developer diproses lebih dulu, baru game. Satu developer hanya dibuatkan satu akun.</li>
                <li>Sebelum disimpan, Anda melihat pratinjau lengkap dan bisa membatalkan.</li>
            </ul>
        </div>
    </div>
@endsection