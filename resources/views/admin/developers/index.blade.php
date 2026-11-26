@extends('layouts.admin')

@section('title', 'Developer')

@section('content')
    @php
        $filtering = request()->filled('q');
    @endphp

    <div class="mb-6 flex items-center justify-between gap-4">
        <p class="text-base-content/60">Kelola akun developer dan lihat performa game mereka.</p>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.developers.assign') }}" class="btn btn-soft btn-secondary">
                <x-icon name="tag" class="size-5" /> Tetapkan Game
            </a>
            <a href="{{ route('admin.developers.create') }}" class="btn btn-primary">
                <x-icon name="plus" class="size-5" /> Tambah Developer
            </a>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.developers.index') }}" class="card mb-6">
        <div class="card-body flex-row flex-wrap items-center gap-3 p-4">
            @if (request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
            @if (request('dir')) <input type="hidden" name="dir" value="{{ request('dir') }}"> @endif

            <label class="input grow items-center gap-2 sm:max-w-md">
                <x-icon name="search" class="size-4 text-base-content/50" />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama atau email..." class="grow">
            </label>

            <div class="flex gap-2">
                <a href="{{ route('admin.developers.index') }}" class="btn btn-text">Reset</a>
                <button type="submit" class="btn btn-primary">Cari</button>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th><x-sort-link column="name" label="Developer" /></th>
                        <th><x-sort-link column="games" label="Game" /></th>
                        <th><x-sort-link column="reviews" label="Ulasan" /></th>
                        <th><x-sort-link column="rating" label="Rating" /></th>
                        <th><x-sort-link column="date" label="Bergabung" /></th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($developers as $developer)
                        <tr>
                            <td>
                                <a href="{{ route('users.show', $developer) }}" target="_blank" class="flex items-center gap-3">
                                    <x-avatar :user="$developer" size="size-9" text="text-sm" />
                                    <span>
                                        <span class="block font-semibold">{{ $developer->name }}</span>
                                        <span class="block text-xs text-base-content/60">{{ $developer->email }}</span>
                                    </span>
                                </a>
                            </td>
                            <td><span class="badge badge-soft badge-primary">{{ $developer->developed_games_count }}</span></td>
                            <td>{{ $developer->reviews_total ?? 0 }}</td>
                            <td>{{ $developer->rating_avg ? number_format($developer->rating_avg, 1) : '-' }}</td>
                            <td class="whitespace-nowrap text-sm">{{ $developer->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('users.show', $developer) }}" target="_blank"
                                       class="btn btn-soft btn-secondary btn-square btn-sm" aria-label="Halaman publik" title="Lihat halaman publik">
                                        <x-icon name="eye" class="size-4" />
                                    </a>
                                    <a href="{{ route('admin.developers.assign', ['developer' => $developer->slug]) }}"
                                    class="btn btn-soft btn-success btn-square btn-sm" aria-label="Tetapkan game" title="Tetapkan game">
                                        <x-icon name="plus" class="size-4" />
                                    </a>
                                    <a href="{{ route('admin.developers.edit', $developer) }}"
                                       class="btn btn-soft btn-primary btn-square btn-sm" aria-label="Edit" title="Edit developer">
                                        <x-icon name="pencil" class="size-4" />
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-base-content/60">
                                {{ $filtering ? 'Tidak ada developer yang cocok.' : 'Belum ada developer.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $developers->links() }}</div>
@endsection