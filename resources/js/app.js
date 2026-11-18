import './bootstrap';
// Alpine.js: membuat elemen HTML menjadi interaktif
import Alpine from 'alpinejs';
import Sortable from 'sortablejs';
import { youtubeId, directImageUrl, isHttpUrl } from './media-link';

window.Alpine = Alpine;
window.Sortable = Sortable;

// Carousel for the game detail page (images + videos)
Alpine.data('gameCarousel', (count) => {
    // Kept outside Alpine's reactive data on purpose (YouTube player objects)
    const players = {};
    const states = {};

    return {
        i: 0,
        n: count,
        timer: null,
        hovering: false,

        init() {
            this.$nextTick(() => {
                if (this.$refs.slides.querySelector('iframe[data-yt]')) {
                    this.setupYouTube();
                }
            });

            if (this.n > 1) {
                this.timer = setInterval(() => this.tick(), 5000);
            }
        },

        setupYouTube() {
            const create = () => {
                this.$refs.slides.querySelectorAll('iframe[data-yt]').forEach((frame) => {
                    const index = Number(frame.closest('[data-index]').dataset.index);
                    players[index] = new window.YT.Player(frame, {
                        events: { onStateChange: (e) => { states[index] = e.data; } },
                    });
                });
            };

            if (window.YT && window.YT.Player) {
                create();
                return;
            }

            window.onYouTubeIframeAPIReady = create;
            const script = document.createElement('script');
            script.src = 'https://www.youtube.com/iframe_api';
            document.head.appendChild(script);
        },

        tick() {
            if (this.hovering) return;

            const slide = this.$refs.slides.querySelector(`[data-index="${this.i}"]`);

            // Uploaded video: playing, or paused midway
            const video = slide?.querySelector('video');
            if (video && (!video.paused || (video.currentTime > 0 && !video.ended))) return;

            // YouTube: 1 = playing, 2 = paused, 3 = buffering
            if ([1, 2, 3].includes(states[this.i])) return;

            this.go(this.i + 1);
        },

        go(index) {
            this.$refs.slides.querySelectorAll('video').forEach((v) => v.pause());
            Object.values(players).forEach((p) => {
                try { p.pauseVideo(); } catch (e) { /* player not ready yet */ }
            });

            this.i = (index + this.n) % this.n;
        },

        destroy() {
            clearInterval(this.timer);
        },
    };
});

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

// Live preview for pasted links (gallery: image or YouTube, cover: image only)
Alpine.data('linkPreview', (mode = 'gallery', initial = '') => ({
    url: initial,
    status: 'idle',   // idle | loading | ok | error
    kind: null,       // 'image' | 'youtube'
    ytId: null,
    imgSrc: '',
    message: '',

    init() {
        this.$watch('url', () => this.check());
        if (initial) this.check();
    },

    check() {
        const value = this.url.trim();
        this.kind = null;
        this.ytId = null;
        this.imgSrc = '';
        this.message = '';

        if (!value) {
            this.status = 'idle';
            return;
        }

        if (!isHttpUrl(value)) {
            this.status = 'error';
            this.message = 'Link tidak valid. Harus diawali http:// atau https://';
            return;
        }

        const id = youtubeId(value);
        if (id) {
            if (mode === 'cover') {
                this.status = 'error';
                this.message = 'Cover harus berupa link gambar, bukan video.';
                return;
            }
            this.kind = 'youtube';
            this.ytId = id;
            this.status = 'ok';
            return;
        }

        this.kind = 'image';
        this.status = 'loading';
        this.imgSrc = directImageUrl(value);
    },

    loaded() {
        this.status = 'ok';
    },

    failed() {
        this.status = 'error';
        this.message = 'Gambar tidak bisa dimuat. Pastikan link langsung ke file gambar dan bisa diakses publik.';
    },

    get ready() {
        return this.status === 'ok';
    },
}));

Alpine.start();
