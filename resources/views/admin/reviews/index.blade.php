@extends('layouts.admin')

@section('title', 'Ulasan')

@section('content')
    @php
        $filtering = collect(['q', 'rating', 'text'])->contains(fn($key) => request()->filled($key));
    @endphp
        <p class="mb-6 text-base-content/60">Moderasi ulasan dari pengguna.</p>
        <form method="GET" action="{{ route('admin.reviews.index') }}" class="card mb-6">
            <div class="card-body grid gap-3 p-4 md:grid-cols-5">
                @if (request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                @if (request('dir')) <input type="hidden" name="dir" value="{{ request('dir') }}"> @endif

                <label class="input items-center gap-2 md:col-span-2">
                    <x-icon name="search" class="size-4 text-base-content/50" />
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari game, pengguna, atau isi ulasan..." class="grow">
                </label>

                <select name="rating" class="select">
                    <option value="">Semua rating</option>
                    @for ($n = 10; $n >= 1; $n--)
                        <option value="{{ $n }}" @selected(request('rating') == $n)>Rating {{ $n }}</option>
                    @endfor
                </select>

                <select name="text" class="select">
                    <option value="">Semua ulasan</option>
                    <option value="with" @selected(request('text') === 'with')>Ada teks</option>
                    <option value="without" @selected(request('text') === 'without')>Hanya rating</option>
                </select>

                <div class="flex gap-2">
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-text">Reset</a>
                    <button type="submit" class="btn btn-primary">Cari</button>
                </div>
            </div>
        </form>

        <div class="card">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th><x-sort-link column="game" label="Game" /></th>
                            <th><x-sort-link column="user" label="Pengguna" /></th>
                            <th><x-sort-link column="rating" label="Rating" /></th>
                            <th>Ulasan</th>
                            <th><x-sort-link column="date" label="Tanggal" /></th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reviews as $review)
                            <tr>
                                <td class="font-semibold">
                                    <a href="{{ route('games.show', $review->game) }}" target="_blank" class="link">{{ $review->game->title }}</a>
                                </td>
                                <td>{{ $review->user->name }}</td>
                                <td><span class="badge badge-soft badge-warning">{{ $review->rating }}/10</span></td>
                                <td>{{ $review->body ? Str::limit($review->body, 60) : '-' }}</td>
                                <td class="whitespace-nowrap text-sm">{{ $review->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="flex justify-end">
                                        <button type="button" class="btn btn-soft btn-error btn-square btn-sm text-error" aria-label="Hapus"
                                                @click="$dispatch('open-delete', @js(['action' => route('admin.reviews.destroy', $review), 'name' => 'ulasan dari ' . $review->user->name]))">
                                            <x-icon name="trash" class="size-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-base-content/60">
                                    {{ $filtering ? 'Tidak ada ulasan yang cocok dengan filter.' : 'Belum ada ulasan.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $reviews->links() }}</div>

        <x-confirm-delete />
@endsection