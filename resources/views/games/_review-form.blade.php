<form method="POST" action="{{ $action }}" class="space-y-4">
    @csrf
    @if ($review)
        @method('PUT')
    @endif

    <div>
        <span class="label-text font-semibold">Rating Anda</span>
        <x-rating-input :value="old('rating', $review->rating ?? 0)" />
        @error('rating')
        <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="label-text font-semibold">Ulasan (opsional)</label>
        <textarea name="body" rows="3" maxlength="1000" class="textarea"
            placeholder="Tulis pendapatmu tentang game ini...">{{ old('body', $review->body ?? '') }}</textarea>
        @error('body')
        <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
    </div>

    <div class="flex justify-end">
        <button type="submit" class="btn btn-primary">{{ $review ? 'Perbarui' : 'Kirim Ulasan' }}</button>
    </div>
</form>