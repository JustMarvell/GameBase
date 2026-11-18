export function isHttpUrl(input) {
    try {
        const u = new URL(input.trim());
        return ['http:', 'https:'].includes(u.protocol);
    } catch {
        return false;
    }
}

export function youtubeId(input) {
    let u;
    try {
        u = new URL(input.trim());
    } catch {
        return null;
    }
    if (!['http:', 'https:'].includes(u.protocol)) return null;

    const host = u.hostname.toLowerCase().replace(/^(www\.|m\.)/, '');
    const seg = u.pathname.split('/').filter(Boolean);
    let id = null;

    if (host === 'youtu.be') {
        id = seg[0];
    } else if (host === 'youtube.com' || host === 'youtube-nocookie.com') {
        if (seg[0] === 'watch') id = u.searchParams.get('v');
        else if (['shorts', 'embed', 'live', 'v'].includes(seg[0])) id = seg[1];
    }

    return id && /^[A-Za-z0-9_-]{11}$/.test(id) ? id : null;
}

export function directImageUrl(input) {
    const url = input.trim();
    const m = url.match(/drive\.google\.com\/file\/d\/([A-Za-z0-9_-]+)/)
        || url.match(/drive\.google\.com\/(?:open|uc)\?(?:[^#]*&)?id=([A-Za-z0-9_-]+)/);

    return m ? `https://drive.google.com/thumbnail?id=${m[1]}&sz=w1600` : url;
}