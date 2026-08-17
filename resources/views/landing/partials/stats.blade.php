<section class="section pt-5" style="margin-top:-1px;background:var(--mk-bg);">
    <div class="container">
        <div class="row g-4 justify-content-center">
            @php
                $items = [
                    ['icon' => 'bi-people-fill', 'value' => $stats['pelamar'], 'suffix' => '+', 'label' => 'Pelamar', 'desc' => 'Talenta muda bergabung'],
                    ['icon' => 'bi-buildings-fill', 'value' => $stats['perusahaan'], 'suffix' => '+', 'label' => 'Perusahaan', 'desc' => 'Terdaftar & terverifikasi'],
                    ['icon' => 'bi-briefcase-fill', 'value' => $stats['lowongan'], 'suffix' => '+', 'label' => 'Lowongan', 'desc' => 'Posisi tersedia'],
                    ['icon' => 'bi-mortarboard-fill', 'value' => $stats['magang'], 'suffix' => '+', 'label' => 'Program Magang', 'desc' => 'Dari perusahaan ternama'],
                ];
            @endphp
            @foreach($items as $item)
                <div class="col-6 col-lg-3" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="card card-hover text-center py-4 h-100">
                        <div class="feature-icon mx-auto mb-3" style="width:56px;height:56px;font-size:1.3rem;border-radius:1rem;">
                            <i class="bi {{ $item['icon'] }}"></i>
                        </div>
                        <div class="fs-2 fw-bold text-accent" data-counter="{{ $item['value'] }}" data-suffix="{{ $item['suffix'] }}">0</div>
                        <div class="fw-semibold">{{ $item['label'] }}</div>
                        <div class="small text-muted-2">{{ $item['desc'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
