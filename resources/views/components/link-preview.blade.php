@props(['name' => 'link', 'placeholder' => 'https://...', 'cover' => false])

<div>
    <input type="url" name="{{ $name }}" x-model.debounce.400ms="url"
           placeholder="{{ $placeholder }}" autocomplete="off" class="input">

    <p x-show="status === 'loading'" x-cloak class="mt-2 flex items-center gap-2 text-sm text-base-content/60">
        <span class="loading loading-spinner loading-xs"></span> Memuat pratinjau...
    </p>
    <p x-show="status === 'error'" x-cloak x-text="message" class="mt-2 text-sm text-error"></p>

    <div x-show="status === 'ok'" x-cloak
         class="mt-3 overflow-hidden rounded-xl bg-black shadow {{ $cover ? 'aspect-3/4 w-40' : 'aspect-video w-full max-w-md' }}">
        <template x-if="kind === 'image' && imgSrc">
            <img :src="imgSrc" alt="Pratinjau" referrerpolicy="no-referrer"
                 x-on:load="loaded()" x-on:error="failed()" class="size-full object-cover">
        </template>
        <template x-if="kind === 'youtube'">
            <iframe :src="'https://www.youtube-nocookie.com/embed/' + ytId" class="size-full"
                    allow="encrypted-media; picture-in-picture" allowfullscreen title="Pratinjau video"></iframe>
        </template>
    </div>
</div>