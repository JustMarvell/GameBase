@extends('layouts.admin')

@section('title', 'Pratinjau Impor')

@section('content')
    @php
        $s = $report['summary'];
        $canRun = !$report['fatal'] && ($s['created'] + $s['assigned'] + $s['developers_created'] + $s['bios_filled']) > 0;
    @endphp

    <div class="mb-4">
        <h2 class="text-xl font-extrabold">Pratinjau: {{ $pending['name'] }}</h2>
        <p class="text-sm text-base-content/60">Belum ada yang disimpan. Periksa hasilnya, lalu jalankan impor.</p>
    </div>

    @include('admin.games._import-report', ['report' => $report])

    <div class="mt-6 flex flex-wrap gap-2">
        <form method="POST" action="{{ route('admin.games.import.run') }}" x-data="{ busy: false }" @submit="busy = true">
            @csrf
            <button type="submit" class="btn btn-primary" :disabled="busy || {{ $canRun ? 'false' : 'true' }}">
                <span x-show="busy" x-cloak class="loading loading-spinner loading-sm"></span>
                <span x-text="busy ? 'Mengimpor...' : 'Jalankan impor'"></span>
            </button>
        </form>

        <form method="POST" action="{{ route('admin.games.import.cancel') }}">
            @csrf
            <button type="submit" class="btn btn-text">{{ $report['fatal'] ? 'Kembali' : 'Batal' }}</button>
        </form>
    </div>

    @unless ($canRun || $report['fatal'])
        <p class="mt-3 text-sm text-base-content/60">Tidak ada data baru untuk disimpan dari file ini.</p>
    @endunless
@endsection