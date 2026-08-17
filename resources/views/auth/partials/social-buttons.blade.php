{{-- Tombol social login (Google & LinkedIn) --}}
<div class="d-grid gap-2">
    <a href="{{ route('social.redirect', 'google') }}" class="btn btn-outline-secondary rounded-4 d-flex align-items-center justify-content-center gap-2 py-2">
        <svg width="18" height="18" viewBox="0 0 48 48" aria-hidden="true">
            <path fill="#FFC107" d="M43.6 20.1H42V20H24v8h11.3C33.7 32.7 29.2 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.9 1.2 8 3l5.7-5.7C34.5 6.1 29.5 4 24 4 13 4 4 13 4 24s9 20 20 20 20-9 20-20c0-1.3-.1-2.6-.4-3.9z"/>
            <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.7 15.1 18.9 12 24 12c3.1 0 5.9 1.2 8 3l5.7-5.7C34.5 6.1 29.5 4 24 4 16.3 4 9.7 8.3 6.3 14.7z"/>
            <path fill="#4CAF50" d="M24 44c5.2 0 10-2 13.6-5.2l-6.3-5.3C29.2 35.1 26.7 36 24 36c-5.2 0-9.6-3.3-11.3-8l-6.5 5C9.6 39.6 16.2 44 24 44z"/>
            <path fill="#1976D2" d="M43.6 20.1H42V20H24v8h11.3c-.8 2.3-2.3 4.3-4.2 5.7l6.3 5.3C36.9 42.6 48 34 48 24c0-1.3-.1-2.6-.4-3.9z"/>
        </svg>
        <span class="small fw-semibold">Lanjutkan dengan Google</span>
    </a>
    <a href="{{ route('social.redirect', 'linkedin-openid') }}" class="btn btn-outline-secondary rounded-4 d-flex align-items-center justify-content-center gap-2 py-2">
        <i class="bi bi-linkedin fs-5" style="color:#0A66C2;"></i>
        <span class="small fw-semibold">Lanjutkan dengan LinkedIn</span>
    </a>
</div>

<div class="d-flex align-items-center gap-3 my-3">
    <hr class="flex-grow-1 m-0">
    <span class="small text-muted-2 text-nowrap">atau</span>
    <hr class="flex-grow-1 m-0">
</div>
