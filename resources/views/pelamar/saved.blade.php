@extends('layouts.app')

@section('title', 'Lowongan Tersimpan - MagangKu')
@section('page-title', 'Lowongan Tersimpan')

@section('content')
    @if($saved->count())
        <div class="row g-4">
            @foreach($saved as $item)
                <div class="col-md-6 col-xl-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 60 }}">
                    <div class="position-relative">
                        <x-job-card :job="$item->lowongan"/>
                        <button class="btn btn-soft btn-sm position-absolute top-0 end-0 m-3 z-1" data-save-url="{{ route('pelamar.saved.toggle', $item->lowongan) }}" style="border-radius:50rem;">
                            <i class="bi bi-trash me-1"></i>Hapus
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="d-flex justify-content-center mt-4">{{ $saved->links() }}</div>
    @else
        <div class="text-center py-5" data-aos="fade-up">
            <i class="bi bi-bookmark-star fs-1 text-muted-2"></i>
            <h6 class="fw-bold mt-3">Belum Ada Lowongan Tersimpan</h6>
            <p class="text-muted-2 small">Simpan lowongan menarik dengan menekan ikon bookmark.</p>
            <a href="{{ route('jobs.index') }}" class="btn btn-mk mt-2"><i class="bi bi-search me-1"></i>Cari Lowongan</a>
        </div>
    @endif
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-save-url]').forEach(btn => {
            btn.addEventListener('click', async () => {
                try {
                    const data = await mkFetch(btn.dataset.saveUrl, { method: 'POST' });
                    mkToast(data.saved ? 'Lowongan disimpan' : 'Dihapus dari simpanan', 'success');
                    if (!data.saved) btn.closest('.col-md-6').remove();
                } catch (err) { mkToast(err.message, 'error'); }
            });
        });
    });
</script>
@endpush
