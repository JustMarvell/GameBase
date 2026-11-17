<div x-data="{ open: false, action: '', name: '' }"
     @open-delete.window="action = $event.detail.action; name = $event.detail.name; open = true"
     @keydown.escape.window="open = false">

    <div x-show="open" x-cloak x-transition.opacity @click.self="open = false"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
        <div class="card w-full max-w-md bg-base-100 shadow-2xl">
            <div class="card-body">
                <h3 class="text-lg font-bold">Hapus data?</h3>
                <p class="text-base-content/70">
                    Anda yakin ingin menghapus <span class="font-semibold" x-text="name"></span>?
                    Tindakan ini tidak bisa dibatalkan.
                </p>
                <form method="POST" :action="action" class="mt-4 flex justify-end gap-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="open = false" class="btn btn-text">Batal</button>
                    <button type="submit" class="btn btn-error">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>