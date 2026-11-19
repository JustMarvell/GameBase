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

// Cover field: upload or link, plus a draggable frame to choose the visible part
Alpine.data('coverField', (o) => ({
    mode: o.mode,            // 'upload' | 'link'
    fileSrc: o.fileSrc,      // existing uploaded cover, or the newly chosen file
    linkUrl: o.linkUrl,
    linkSrc: '',
    linkStatus: 'idle',      // idle | loading | ok | error
    linkMessage: '',
    x: o.x,
    y: o.y,
    ratio: 0,                // natural width / height of the shown image
    dragging: false,

    init() {
        if (this.linkUrl) this.checkLink();

        this.$watch('linkUrl', () => {
            this.x = 50;
            this.y = 50;
            this.checkLink();
        });
    },

    get previewSrc() {
        return this.mode === 'upload' ? this.fileSrc : this.linkSrc;
    },

    get showEditor() {
        return !!this.previewSrc && !(this.mode === 'link' && this.linkStatus === 'error');
    },

    // Size of the cover frame relative to the image (the cover is 3:4 portrait)
    frameSize() {
        const a = this.ratio || 0.75;
        return {
            fw: a > 0.75 ? 0.75 / a : 1,
            fh: a < 0.75 ? a / 0.75 : 1,
        };
    },

    get frameStyle() {
        const { fw, fh } = this.frameSize();
        return `left:${(1 - fw) * this.x}%;top:${(1 - fh) * this.y}%;width:${fw * 100}%;height:${fh * 100}%;box-shadow:0 0 0 9999px rgba(0,0,0,.6);`;
    },

    onFile(event) {
        const file = event.target.files[0];
        if (file) {
            this.fileSrc = URL.createObjectURL(file);
            this.x = 50;
            this.y = 50;
        }
    },

    checkLink() {
        const value = this.linkUrl.trim();
        this.linkSrc = '';
        this.linkMessage = '';

        if (!value) {
            this.linkStatus = 'idle';
            return;
        }
        if (!isHttpUrl(value)) {
            this.linkStatus = 'error';
            this.linkMessage = 'Link tidak valid. Harus diawali http:// atau https://';
            return;
        }
        if (youtubeId(value)) {
            this.linkStatus = 'error';
            this.linkMessage = 'Cover harus berupa link gambar, bukan video.';
            return;
        }

        this.linkStatus = 'loading';
        this.linkSrc = directImageUrl(value);
    },

    imgLoaded(event) {
        const img = event.target;
        this.ratio = img.naturalWidth / img.naturalHeight;
        if (this.mode === 'link') this.linkStatus = 'ok';
    },

    imgFailed() {
        if (this.mode === 'link') {
            this.linkStatus = 'error';
            this.linkMessage = 'Gambar tidak bisa dimuat. Pastikan link langsung ke file gambar dan bisa diakses publik.';
        }
    },

    startDrag(event) {
        this.dragging = true;
        this.$refs.stage.setPointerCapture(event.pointerId);
        this.moveTo(event);
    },

    drag(event) {
        if (this.dragging) this.moveTo(event);
    },

    endDrag() {
        this.dragging = false;
    },

    // The frame's center follows the pointer
    moveTo(event) {
        const rect = this.$refs.stage.getBoundingClientRect();
        const px = (event.clientX - rect.left) / rect.width;
        const py = (event.clientY - rect.top) / rect.height;
        const { fw, fh } = this.frameSize();

        this.x = fw >= 0.999 ? 50 : Math.min(100, Math.max(0, ((px - fw / 2) / (1 - fw)) * 100));
        this.y = fh >= 0.999 ? 50 : Math.min(100, Math.max(0, ((py - fh / 2) / (1 - fh)) * 100));
    },
}));

Alpine.start();
