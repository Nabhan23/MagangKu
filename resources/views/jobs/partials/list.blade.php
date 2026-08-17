@if($jobs->count())
    <div class="row g-4" data-aos="fade-up">
        @foreach($jobs as $job)
            <div class="col-md-6 col-xl-4">
                <x-job-card :job="$job"/>
            </div>
        @endforeach
    </div>
@else
    <div class="text-center py-5">
        <i class="bi bi-search fs-1 text-muted-2"></i>
        <h6 class="fw-bold mt-3">Tidak Ada Lowongan Ditemukan</h6>
        <p class="text-muted-2 small">Coba ubah kata kunci atau filter pencarianmu.</p>
    </div>
@endif
