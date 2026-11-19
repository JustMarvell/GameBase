@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman"
        class="flex flex-col items-center justify-between gap-3 sm:flex-row">

        <p class="text-sm text-base-content/60">
            Menampilkan
            <span class="font-semibold text-base-content">{{ $paginator->firstItem() }}</span>-<span
                class="font-semibold text-base-content">{{ $paginator->lastItem() }}</span>
            dari
            <span class="font-semibold text-base-content">{{ $paginator->total() }}</span>
        </p>

        <div class="flex items-center gap-1">
            {{-- Sebelumnya --}}
            @if ($paginator->onFirstPage())
                <span class="btn btn-soft btn-square btn-sm btn-disabled" aria-disabled="true" aria-label="Sebelumnya">
                    <x-icon name="chevron-left" class="size-4" />
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn btn-soft btn-primary btn-square btn-sm"
                    aria-label="Sebelumnya">
                    <x-icon name="chevron-left" class="size-4" />
                </a>
            @endif

            {{-- Nomor halaman (di HP hanya halaman aktif yang tampil) --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="hidden px-1 text-base-content/40 sm:inline">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="btn btn-primary btn-sm glow min-w-9 px-2">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="btn btn-text btn-sm hidden min-w-9 px-2 sm:inline-flex"
                                aria-label="Halaman {{ $page }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Berikutnya --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn btn-soft btn-primary btn-square btn-sm"
                    aria-label="Berikutnya">
                    <x-icon name="chevron-right" class="size-4" />
                </a>
            @else
                <span class="btn btn-soft btn-square btn-sm btn-disabled" aria-disabled="true" aria-label="Berikutnya">
                    <x-icon name="chevron-right" class="size-4" />
                </span>
            @endif
        </div>
    </nav>
@endif