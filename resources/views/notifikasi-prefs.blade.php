@extends('layouts.app')

@section('title', 'Preferensi Notifikasi - MagangKu')
@section('page-title', 'Preferensi Notifikasi Email')

@section('content')
    @if(session('success'))
        <div class="alert alert-success border-0 rounded-4 d-flex align-items-center gap-2" data-aos="fade-up">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="card card-hover border-0 rounded-4 p-4" style="max-width:760px;" data-aos="fade-up">
        <div class="d-flex align-items-start gap-3 mb-4">
            <div class="stat-icon" style="background:#0EA5E91A;color:#0EA5E9;width:52px;height:52px;font-size:1.3rem;"><i class="bi bi-envelope-check"></i></div>
            <div>
                <h6 class="fw-bold mb-1">Atur email apa saja yang ingin Anda terima</h6>
                <p class="small text-muted-2 mb-0">Centang jenis notifikasi yang masih ingin dikirim ke email Anda. Kosongkan untuk berhenti menerima email jenis tersebut.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('notifikasi-prefs.update') }}">
            @csrf
            <div class="d-grid gap-2">
                @foreach($types as $type)
                    <label class="d-flex align-items-start gap-3 p-3 rounded-4 border {{ old('prefs.' . $type['key'], auth()->user()->wantsEmail($type['key'])) ? 'border-success-subtle' : '' }}" style="border-color:var(--mk-border);cursor:pointer;">
                        <input type="checkbox" name="prefs[{{ $type['key'] }}]" value="1" class="form-check-input mt-1 flex-shrink-0"
                               style="width:1.2em;height:1.2em;"
                               {{ old('prefs.' . $type['key'], auth()->user()->wantsEmail($type['key'])) ? 'checked' : '' }}>
                        <span class="min-w-0">
                            <span class="d-block fw-semibold small">{{ $type['label'] }}</span>
                            <span class="d-block small text-muted-2">{{ $type['desc'] }}</span>
                        </span>
                    </label>
                @endforeach
            </div>

            @error('prefs')
                <div class="text-danger small mt-2">{{ $message }}</div>
            @enderror

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-mk"><i class="bi bi-check-lg me-1"></i>Simpan Preferensi</button>
            </div>
        </form>
    </div>
@endsection
