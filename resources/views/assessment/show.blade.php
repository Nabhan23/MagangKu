@extends('layouts.guest')

@section('title', $assessment->judul . ' - MagangKu')
@section('meta_description', 'Kerjakan ' . $assessment->judul . ' dan ukur kemampuanmu.')

@section('content')
    <section class="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 bg-mk-gradient-dark text-white p-4 mb-4" data-aos="fade-up">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div>
                                <span class="badge rounded-pill text-bg-light text-accent mb-2">{{ $assessment->kategori_label }}</span>
                                <h1 class="fw-bold fs-4 mb-1">{{ $assessment->judul }}</h1>
                                <div class="small" style="color:rgba(255,255,255,.9);">
                                    <i class="bi bi-patch-question me-1"></i>{{ $assessment->questions->count() }} soal •
                                    <i class="bi bi-stopwatch ms-1 me-1"></i>{{ $assessment->durasi_menit }} menit
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="fs-4 fw-bold" id="timer" style="font-variant-numeric:tabular-nums;">{{ $assessment->durasi_menit }}:00</div>
                                <div class="small" style="color:rgba(255,255,255,.85);">Sisa Waktu</div>
                            </div>
                        </div>
                        <div class="progress mt-3" style="height:6px;background:rgba(255,255,255,.25);">
                            <div class="progress-bar bg-white" id="progressBar" style="width:0%"></div>
                        </div>
                    </div>

                    @if($latest)
                        <div class="alert alert-info border-0 rounded-4 small d-flex align-items-center gap-2" data-aos="fade-up">
                            <i class="bi bi-info-circle-fill"></i>
                            <div>
                                Hasil terakhir kamu: <b>{{ $latest->skor }}/100</b> ({{ $latest->is_lulus ? 'Lulus ✅' : 'Belum lulus' }}).
                                Kamu bisa mengulang tes untuk memperbaiki skor.
                            </div>
                        </div>
                    @endif

                    <div class="card card-hover p-4" data-aos="fade-up">
                        @if($assessment->questions->isEmpty())
                            <p class="text-muted-2 text-center py-4 mb-0">Tes ini belum memiliki soal.</p>
                        @else
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0"><i class="bi bi-patch-question me-2 text-mk"></i>Soal <span id="qNum">1</span>/{{ $assessment->questions->count() }}</h6>
                                <button type="button" class="btn btn-sm btn-outline-mk" id="btnRagu"><i class="bi bi-flag me-1"></i>Ragu-ragu</button>
                            </div>

                            <div id="quizContainer">
                                @foreach($assessment->questions as $i => $q)
                                    <div class="quiz-question {{ $i === 0 ? '' : 'd-none' }}" data-index="{{ $i }}">
                                        <h5 class="fw-semibold mb-4">{{ $i + 1 }}. {{ $q->pertanyaan }}</h5>
                                        <div class="d-grid gap-2">
                                            @foreach(['a', 'b', 'c', 'd'] as $opsi)
                                                <label class="quiz-option" data-opt="{{ $opsi }}">
                                                    <input type="radio" name="q{{ $q->id }}" value="{{ $opsi }}" class="d-none"
                                                           data-qid="{{ $q->id }}" data-opt="{{ $opsi }}">
                                                    <span class="opt-letter">{{ strtoupper($opsi) }}</span>
                                                    <span>{{ $q->{'opsi_' . $opsi} }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <button type="button" class="btn btn-soft" id="btnPrev" disabled><i class="bi bi-arrow-left me-1"></i>Sebelumnya</button>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-soft" id="btnNext">Berikutnya<i class="bi bi-arrow-right ms-1"></i></button>
                                    <button type="button" class="btn btn-mk" id="btnSubmit"><i class="bi bi-check2-circle me-1"></i>Kumpulkan Jawaban</button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    @if(!$assessment->questions->isEmpty())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const questions = document.querySelectorAll('.quiz-question');
                const total = questions.length;
                const answers = {};
                const flagged = new Set();
                let idx = 0;

                const progressBar = document.getElementById('progressBar');
                const qNum = document.getElementById('qNum');
                const btnPrev = document.getElementById('btnPrev');
                const btnNext = document.getElementById('btnNext');
                const btnSubmit = document.getElementById('btnSubmit');

                const show = (i) => {
                    questions.forEach((el, k) => el.classList.toggle('d-none', k !== i));
                    idx = i;
                    qNum.textContent = i + 1;
                    btnPrev.disabled = i === 0;
                    btnNext.classList.toggle('d-none', i === total - 1);
                    btnSubmit.classList.toggle('d-none', i !== total - 1);
                    progressBar.style.width = (((i + 1) / total) * 100) + '%';
                };

                document.querySelectorAll('.quiz-option input').forEach((input) => {
                    input.addEventListener('change', () => {
                        const qid = input.dataset.qid;
                        answers[qid] = input.value;
                        document.querySelectorAll(`.quiz-option[data-opt="${input.dataset.opt}"]`).forEach((l) => {
                            const inp = l.querySelector('input');
                            l.classList.toggle('selected', inp.checked);
                        });
                    });
                });

                document.getElementById('btnRagu')?.addEventListener('click', () => {
                    const q = questions[idx];
                    flagged.has(idx) ? flagged.delete(idx) : flagged.add(idx);
                    q.classList.toggle('flagged');
                });

                btnPrev.addEventListener('click', () => idx > 0 && show(idx - 1));
                btnNext.addEventListener('click', () => idx < total - 1 && show(idx + 1));

                const submit = async () => {
                    btnSubmit.disabled = true;
                    const terjawab = Object.keys(answers).length;
                    const belum = total - terjawab;
                    if (belum > 0 && !confirm(`Masih ada ${belum} soal belum dijawab. Kumpulkan tetap?`)) {
                        btnSubmit.disabled = false;
                        return;
                    }
                    try {
                        const data = await mkFetch('{{ route('assessment.submit', $assessment) }}', {
                            method: 'POST',
                            body: { jawaban: answers },
                        });
                        window.location.href = data.redirect || '{{ route('assessment.result', $assessment) }}';
                    } catch (e) {
                        mkToast(e.message, 'error');
                        btnSubmit.disabled = false;
                    }
                };
                btnSubmit.addEventListener('click', submit);

                /* Timer */
                let remaining = {{ $assessment->durasi_menit }} * 60;
                const timerEl = document.getElementById('timer');
                const tick = () => {
                    if (remaining <= 0) {
                        mkToast('Waktu habis! Jawaban dikumpulkan otomatis.', 'info');
                        submit();
                        return;
                    }
                    remaining--;
                    const m = String(Math.floor(remaining / 60)).padStart(2, '0');
                    const s = String(remaining % 60).padStart(2, '0');
                    timerEl.textContent = `${m}:${s}`;
                };
                setInterval(tick, 1000);
                show(0);
            });
        </script>
    @endif
@endpush
