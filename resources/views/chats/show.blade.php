@extends('layouts.app')

@section('title', $title . ' - MagangKu')
@section('page-title', $title)

@section('content')
    <div class="card card-hover p-4" data-aos="fade-up">
        {{-- Header --}}
        <div class="d-flex align-items-center gap-3 pb-3 mb-3 border-bottom" style="border-color:var(--mk-border) !important;">
            <a href="{{ $backUrl }}" class="btn btn-soft btn-sm"><i class="bi bi-arrow-left"></i></a>
            <div class="min-w-0">
                <div class="fw-bold small text-truncate">{{ $title }}</div>
                <div class="small text-muted-2 text-truncate">{{ $subtitle }}</div>
            </div>
            <span class="badge rounded-pill text-bg-{{ $lamaran->status_color }} ms-auto text-nowrap">{{ $lamaran->status_label }}</span>
        </div>

        {{-- Pesan --}}
        <div id="chatBox" class="d-flex flex-column gap-2 mb-3" style="max-height:420px;overflow-y:auto;padding:.25rem;">
            @foreach($messages as $m)
                <div class="d-flex {{ $m->sender_id === auth()->id() ? 'justify-content-end' : 'justify-content-start' }}" data-msg-id="{{ $m->id }}">
                    <div class="rounded-3 px-3 py-2" style="max-width:75%;{{ $m->sender_id === auth()->id() ? 'background:var(--mk-gradient);color:#fff;border-bottom-right-radius:.25rem;' : 'background:var(--mk-soft);border-bottom-left-radius:.25rem;' }}">
                        <div class="small" style="white-space:pre-wrap;">{{ $m->pesan }}</div>
                        <div class="small mt-1" style="font-size:.65rem;{{ $m->sender_id === auth()->id() ? 'color:rgba(255,255,255,.75)' : 'color:var(--mk-muted)' }}">{{ $m->created_at->translatedFormat('H:i') }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Form kirim --}}
        <form id="chatForm" class="d-flex gap-2">
            <input type="text" id="chatInput" class="form-control rounded-4" placeholder="Tulis pesan..." maxlength="2000" autocomplete="off" required>
            <button class="btn btn-mk rounded-4 px-4" id="chatSend"><i class="bi bi-send-fill"></i></button>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const box = document.getElementById('chatBox');
        const form = document.getElementById('chatForm');
        const input = document.getElementById('chatInput');
        const btn = document.getElementById('chatSend');
        const messagesUrl = @js($messagesUrl);
        let lastId = box.querySelector('[data-msg-id]:last-child')?.dataset.msgId || 0;
        let pending = false;

        const scrollBottom = () => { box.scrollTop = box.scrollHeight; };
        scrollBottom();

        const appendMessage = (m) => {
            if (box.querySelector(`[data-msg-id="${m.id}"]`)) return;
            const div = document.createElement('div');
            div.className = 'd-flex ' + (m.mine ? 'justify-content-end' : 'justify-content-start');
            div.dataset.msgId = m.id;
            div.innerHTML = `<div class="rounded-3 px-3 py-2" style="max-width:75%;${m.mine ? 'background:var(--mk-gradient);color:#fff;border-bottom-right-radius:.25rem;' : 'background:var(--mk-soft);border-bottom-left-radius:.25rem;'}">
                <div class="small" style="white-space:pre-wrap;"></div>
                <div class="small mt-1" style="font-size:.65rem;${m.mine ? 'color:rgba(255,255,255,.75)' : 'color:var(--mk-muted)'}"></div>
            </div>`;
            div.querySelector('.small').textContent = m.pesan;
            div.querySelectorAll('.small')[1].textContent = m.waktu;
            box.appendChild(div);
            lastId = Math.max(lastId, m.id);
            scrollBottom();
        };

        // Kirim pesan
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const pesan = input.value.trim();
            if (!pesan || pending) return;
            pending = true;
            btn.disabled = true;
            try {
                const res = await mkFetch(@js($sendUrl), { method: 'POST', body: JSON.stringify({ pesan }) });
                appendMessage({ id: res.id, pesan: res.pesan, mine: true, waktu: '' });
                input.value = '';
            } catch (err) {
                mkToast(err.message, 'error');
            }
            pending = false;
            btn.disabled = false;
            input.focus();
        });

        // Polling pesan baru setiap 3 detik
        setInterval(async () => {
            try {
                const res = await fetch(messagesUrl + (lastId ? '?after=' + lastId : ''), { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                data.forEach(appendMessage);
            } catch (e) { /* abaikan */ }
        }, 3000);
    });
</script>
@endpush
