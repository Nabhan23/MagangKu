{{-- Modal konfirmasi generik: dipakai tombol dengan atribut data-confirm="Pesan konfirmasi".
     Tombol harus berada di dalam <form>; tombol "Ya, Lanjutkan" akan submit form tersebut. --}}
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-body text-center p-4 pt-5 pb-4">
                <div class="feature-icon mx-auto mb-3" style="width:60px;height:60px;font-size:1.4rem;border-radius:50%;background:#fef3c7;color:#b45309;"><i class="bi bi-question-lg"></i></div>
                <h5 class="fw-bold mb-2">Yakin Melanjutkan?</h5>
                <p class="text-muted-2 small mb-4" id="confirmModalText"></p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-soft px-4" data-bs-dismiss="modal">Tidak</button>
                    <button type="button" class="btn btn-mk px-4" id="confirmModalBtn"><i class="bi bi-check-lg me-1"></i>Ya, Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modalEl = document.getElementById('confirmModal');
        if (!modalEl) return;
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        const textEl = document.getElementById('confirmModalText');
        const btnEl = document.getElementById('confirmModalBtn');
        let targetForm = null;

        document.querySelectorAll('[data-confirm]').forEach((el) => {
            el.addEventListener('click', (e) => {
                e.preventDefault();
                targetForm = el.closest('form');
                textEl.textContent = el.dataset.confirm;
                modal.show();
            });
        });

        btnEl?.addEventListener('click', () => {
            if (targetForm) targetForm.submit();
        });
    });
</script>
@endpush
