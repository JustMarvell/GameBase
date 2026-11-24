@props(['media'])

<div x-data="gameCarousel({{ $media->count() }})"
     @mouseenter="hovering = true" @mouseleave="hovering = false"
     class="overflow-hidden rounded-xl border border-base-content/10 bg-black shadow-lg">

    <div class="relative">
        <div x-ref="slides" class="relative aspect-video w-full">
        @foreach ($media as $k => $item)
            <div data-index="{{ $k }}" x-show="i === {{ $k }}" x-transition.opacity.duration.300ms
                @if ($k > 0) x-cloak @endif
                class="absolute inset-0">
                @if ($item->isYoutube() && $item->youtubeId())
                    <iframe data-yt class="size-full"
                            src="https://www.youtube-nocookie.com/embed/{{ $item->youtubeId() }}?enablejsapi=1&rel=0&playsinline=1&origin={{ urlencode(request()->getSchemeAndHttpHost()) }}"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen title="Video {{ $k + 1 }}"></iframe>
                @elseif ($item->isVideo())
                    <video src="{{ $item->url }}" controls preload="metadata" playsinline
                        class="size-full object-contain"></video>
                @else
                    <img src="{{ $item->url }}" alt="" referrerpolicy="no-referrer" onerror="this.remove()"
                        class="size-full object-cover"
                        @if ($k > 0) loading="lazy" @endif>
                @endif
            </div>
        @endforeach
        </div>

        @if ($media->count() > 1)
            <button @click="go(i - 1)" class="btn btn-circle btn-sm absolute left-3 top-1/2 -translate-y-1/2 border-0 bg-base-200/70 text-base-content backdrop-blur hover:bg-primary hover:text-primary-content" aria-label="Sebelumnya">
                <x-icon name="chevron-left" class="size-5" />
            </button>
            <button @click="go(i + 1)" class="btn btn-circle btn-sm absolute right-3 top-1/2 -translate-y-1/2 border-0 bg-base-200/70 text-base-content backdrop-blur hover:bg-primary hover:text-primary-content" aria-label="Berikutnya">
                <x-icon name="chevron-right" class="size-5" />
            </button>
        @endif
    </div>

    @if ($media->count() > 1)
        <div class="flex items-center justify-center gap-2 py-3">
            @foreach ($media as $k => $item)
                <button @click="go({{ $k }})" aria-label="Slide {{ $k + 1 }}"
                        :class="i === {{ $k }} ? 'w-6 bg-primary' : 'w-2 bg-white/40'"
                        class="h-2 rounded-full transition-all"></button>
            @endforeach
        </div>
    @endif
</div>