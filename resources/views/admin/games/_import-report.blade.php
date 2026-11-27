@php
    $s = $report['summary'];
    $badges = [
        'created' => ['badge-success', 'Baru'],
        'skipped' => ['badge-ghost', 'Dilewati'],
        'assigned' => ['badge-info', 'Developer ditetapkan'],
        'error' => ['badge-error', 'Error'],
    ];
@endphp

@if ($report['fatal'])
    <div class="alert alert-soft alert-error">{{ $report['fatal'] }}</div>
@else
    <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
        @foreach ([
                ['Game baru', $s['created'], 'text-success'],
                ['Dilewati (sudah ada)', $s['skipped'], ''],
                ['Developer ditetapkan ke game lama', $s['assigned'], 'text-info'],
                ['Baris error', $s['errors'], 'text-error'],
                ['Developer baru', $s['developers_created'], 'text-primary'],
                ['Developer sudah ada', $s['developers_reused'], ''],
                ['Deskripsi developer diisi', $s['bios_filled'], ''],
                ['Genre / platform baru', $s['genres_created'] . ' / ' . $s['platforms_created'], ''],
            ] as [$label, $value, $color])
                <div class="card">
                    <div class="card-body p-4 text-center">
                        <p class="text-2xl font-extrabold {{ $color }}">{{ $value }}</p>
                        <p class="text-xs text-base-content/60">{{ $label }}</p>
                    </div>
                </div>
        @endforeach
    </div>

    @if (!empty($report['new_genres']) || !empty($report['new_platforms']))
        <p class="mt-3 text-sm text-base-content/70">
            @if (!empty($report['new_genres']))
                Genre baru: <span class="font-semibold">{{ implode(', ', $report['new_genres']) }}</span>.
            @endif
            @if (!empty($report['new_platforms']))
                Platform baru: <span class="font-semibold">{{ implode(', ', $report['new_platforms']) }}</span>.
            @endif
        </p>
    @endif

    @if (!empty($report['developers']))
        <div class="card mt-6">
            <div class="card-body gap-3 p-4">
                <h3 class="font-bold">Developer</h3>

                @if ($s['developers_created'] > 0)
                    <div class="alert alert-soft alert-warning text-sm">
                        Akun developer baru memakai email <code>@dev.test</code>. Ganti password-nya (Developer, lalu Edit)
                        sebelum situs dipublikasikan.
                        @if ($report['committed'])
                            Catat password di tabel ini, karena tidak ditampilkan lagi.
                        @endif
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="table table-sm">
                        <thead>
                            <tr><th>Nama</th><th>Email</th><th>Password</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($report['developers'] as $dev)
                                <tr>
                                    <td class="font-semibold">{{ $dev['name'] }}</td>
                                    <td>{{ $dev['email'] }}</td>
                                    <td>
                                        @if ($dev['password'])
                                            <code>{{ $dev['password'] }}</code>
                                        @elseif ($dev['status'] === 'created')
                                            <span class="text-base-content/50">acak (tampil setelah impor)</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-soft badge-sm {{ $dev['status'] === 'created' ? 'badge-primary' : 'badge-ghost' }}">
                                            {{ $dev['status'] === 'created' ? 'Akun baru' : 'Sudah ada' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <div class="card mt-6">
        <div class="card-body gap-3 p-4">
            <h3 class="font-bold">Detail per baris</h3>
            <div class="max-h-96 overflow-auto">
                <table class="table table-sm">
                    <thead>
                        <tr><th>Baris</th><th>Judul</th><th>Status</th><th>Catatan</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($report['rows'] as $row)
                            <tr>
                                <td>{{ $row['no'] }}</td>
                                <td class="font-semibold">{{ $row['title'] }}</td>
                                <td>
                                    <span class="badge badge-soft badge-sm {{ $badges[$row['status']][0] }}">{{ $badges[$row['status']][1] }}</span>
                                </td>
                                <td class="text-sm text-base-content/70">
                                    @foreach ($row['notes'] as $note)
                                        <div>{{ $note }}</div>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif