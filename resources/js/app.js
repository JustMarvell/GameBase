import './bootstrap';
// Alpine.js: membuat elemen HTML menjadi interaktif
import Alpine from 'alpinejs';
import Sortable from 'sortablejs';

window.Alpine = Alpine;
window.Sortable = Sortable;

// Carousel for the game detail page (images + videos)
Alpine.data('gameCarousel', (count) => ({
    i: 0,
    n: count,
    timer: null,
    hovering: false,

    init() {
        if (this.n > 1) {
            this.timer = setInterval(() => this.tick(), 5000);
        }
    },

    tick() {
        if (this.hovering) return;

        // Do not slide away from a video that is playing or paused midway
        const video = this.$refs.slides.querySelector(`[data-index="${this.i}"] video`);
        if (video && (!video.paused || (video.currentTime > 0 && !video.ended))) return;

        this.go(this.i + 1);
    },

    go(index) {
        this.$refs.slides.querySelectorAll('video').forEach((v) => v.pause());
        this.i = (index + this.n) % this.n;
    },

    destroy() {
        clearInterval(this.timer);
    },
}));

// Drag-and-drop ordering in the admin gallery page
Alpine.data('mediaSorter', (url, token) => ({
    status: '',

    init() {
        Sortable.create(this.$refs.list, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: async () => {
                const order = [...this.$refs.list.querySelectorAll('[data-id]')].map((el) => el.dataset.id);
                this.status = 'Menyimpan urutan...';

                try {
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token,
                        },
                        body: JSON.stringify({ order }),
                    });
                    this.status = res.ok ? 'Urutan tersimpan.' : 'Gagal menyimpan urutan.';
                } catch (e) {
                    this.status = 'Gagal menyimpan urutan.';
                }
            },
        });
    },
}));

Alpine.start();
