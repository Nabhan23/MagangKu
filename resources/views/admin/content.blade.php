@extends('layouts.app')

@section('title', 'Kelola Konten - MagangKu')
@section('page-title', 'Kelola Konten')

@section('content')
    <ul class="nav nav-pills mb-4 gap-2" data-aos="fade-up">
        @foreach([
            'event' => ['bi-calendar-event', 'Event'],
            'artikel' => ['bi-journal-richtext', 'Artikel'],
            'banner' => ['bi-images', 'Banner'],
            'faq' => ['bi-question-circle', 'FAQ'],
            'testimoni' => ['bi-chat-quote', 'Testimoni'],
            'kategori' => ['bi-tags', 'Kategori'],
            'skill' => ['bi-lightning', 'Skill'],
        ] as $key => [$icon, $label])
            <li class="nav-item">
                <a class="nav-link rounded-pill px-3 {{ $tab === $key ? 'active bg-mk-gradient text-white' : 'bg-soft text-accent' }}" href="{{ route('admin.content.index', ['tab' => $key]) }}">
                    <i class="bi {{ $icon }} me-1"></i>{{ $label }}
                </a>
            </li>
        @endforeach
    </ul>

    {{-- ============ EVENT ============ --}}
    @if($tab === 'event')
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-mk" data-bs-toggle="modal" data-bs-target="#modalEvent"><i class="bi bi-plus-circle me-1"></i>Tambah Event</button>
        </div>
        <div class="card card-hover p-4" data-aos="fade-up">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Event</th><th>Kategori</th><th>Tanggal</th><th>Kuota</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($events as $event)
                            <tr>
                                <td>
                                    <div class="fw-semibold small">{{ $event->judul }}</div>
                                    <div class="small text-muted-2">{{ $event->views }}x dilihat</div>
                                </td>
                                <td><span class="badge rounded-pill text-bg-primary">{{ $event->kategori_label }}</span></td>
                                <td class="small">{{ $event->tanggal_mulai->translatedFormat('d M Y') }}</td>
                                <td class="small">{{ $event->terdaftar }}/{{ $event->kapasitas }}</td>
                                <td><span class="badge rounded-pill text-bg-{{ $event->status === 'aktif' ? 'success' : 'secondary' }}">{{ ucfirst($event->status) }}</span></td>
                                <td class="text-end">
                                    <button class="btn btn-soft btn-sm" data-bs-toggle="modal" data-bs-target="#modalEvent"
                                        data-edit='{{ json_encode(['id' => $event->id, 'judul' => $event->judul, 'kategori' => $event->kategori, 'deskripsi' => $event->deskripsi, 'tanggal_mulai' => $event->tanggal_mulai->format('Y-m-d'), 'tanggal_selesai' => ($event->tanggal_selesai ? $event->tanggal_selesai->format('Y-m-d') : null), 'waktu' => $event->waktu, 'lokasi' => $event->lokasi, 'is_online' => $event->is_online, 'link_event' => $event->link_event, 'kapasitas' => $event->kapasitas, 'penyelenggara' => $event->penyelenggara, 'status' => $event->status], JSON_HEX_APOS | JSON_HEX_QUOT) }}'>
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.content.destroy', ['resource' => 'event', 'item' => $event->id]) }}" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-outline-danger btn-sm" data-confirm="Hapus event &quot;{{ $event->judul }}&quot;? Aksi ini tidak bisa dibatalkan."><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-5 text-muted-2">Belum ada event.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal fade" id="modalEvent" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content p-2">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold" id="modalEventTitle">Tambah Event</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('admin.content.store', ['resource' => 'event']) }}" enctype="multipart/form-data" class="modal-body">
                        @csrf
                        <input type="hidden" name="_method" value="POST" id="eventMethod">
                        <div class="row g-3">
                            <div class="col-md-8"><label class="form-label">Judul</label><input type="text" name="judul" class="form-control" required></div>
                            <div class="col-md-4">
                                <label class="form-label">Kategori</label>
                                <select name="kategori" class="form-select">
                                    @foreach($kategoriEvent as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach
                                </select>
                            </div>
                            <div class="col-md-4"><label class="form-label">Tanggal Mulai</label><input type="date" name="tanggal_mulai" class="form-control" required></div>
                            <div class="col-md-4"><label class="form-label">Tanggal Selesai</label><input type="date" name="tanggal_selesai" class="form-control"></div>
                            <div class="col-md-4"><label class="form-label">Waktu</label><input type="time" name="waktu" class="form-control"></div>
                            <div class="col-md-6"><label class="form-label">Lokasi</label><input type="text" name="lokasi" class="form-control"></div>
                            <div class="col-md-6">
                                <label class="form-label">Link Event (online)</label>
                                <input type="url" name="link_event" class="form-control">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="is_online" id="eventOnline" value="1">
                                    <label class="form-check-label small" for="eventOnline">Event Online</label>
                                </div>
                            </div>
                            <div class="col-md-4"><label class="form-label">Kapasitas</label><input type="number" name="kapasitas" class="form-control" value="100" min="1"></div>
                            <div class="col-md-4"><label class="form-label">Penyelenggara</label><input type="text" name="penyelenggara" class="form-control" value="MagangKu"></div>
                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="aktif">Aktif</option>
                                    <option value="draft">Draft</option>
                                    <option value="selesai">Selesai</option>
                                </select>
                            </div>
                            <div class="col-md-6"><label class="form-label">Deskripsi</label><textarea name="deskripsi" rows="5" class="form-control" required></textarea></div>
                            <div class="col-md-6"><label class="form-label">Gambar Cover (opsional)</label><input type="file" name="gambar" class="form-control" accept="image/*"></div>
                        </div>
                        <button class="btn btn-mk w-100 mt-3" data-confirm="Simpan data event ini?">Simpan Event</button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- ============ ARTIKEL ============ --}}
    @if($tab === 'artikel')
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-mk" data-bs-toggle="modal" data-bs-target="#modalArtikel"><i class="bi bi-plus-circle me-1"></i>Tambah Artikel</button>
        </div>
        <div class="card card-hover p-4" data-aos="fade-up">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Artikel</th><th>Kategori</th><th>Penulis</th><th>Dilihat</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($articles as $article)
                            <tr>
                                <td>
                                    <div class="fw-semibold small">{{ $article->judul }}</div>
                                    <div class="small text-muted-2">{{ $article->published_at?->translatedFormat('d M Y') }}</div>
                                </td>
                                <td><span class="badge rounded-pill text-bg-primary">{{ $article->kategori_label }}</span></td>
                                <td class="small">{{ $article->penulis }}</td>
                                <td class="small">{{ $article->views }}</td>
                                <td><span class="badge rounded-pill text-bg-{{ $article->status === 'published' ? 'success' : 'secondary' }}">{{ $article->status }}</span></td>
                                <td class="text-end">
                                    <button class="btn btn-soft btn-sm" data-bs-toggle="modal" data-bs-target="#modalArtikel"
                                        data-edit='{{ json_encode(['id' => $article->id, 'judul' => $article->judul, 'kategori' => $article->kategori, 'ringkasan' => $article->ringkasan, 'konten' => $article->konten, 'penulis' => $article->penulis, 'status' => $article->status], JSON_HEX_APOS | JSON_HEX_QUOT) }}'>
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.content.destroy', ['resource' => 'artikel', 'item' => $article->id]) }}" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-outline-danger btn-sm" data-confirm="Hapus artikel &quot;{{ $article->judul }}&quot;? Aksi ini tidak bisa dibatalkan."><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-5 text-muted-2">Belum ada artikel.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal fade" id="modalArtikel" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content p-2">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Tambah Artikel</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('admin.content.store', ['resource' => 'artikel']) }}" enctype="multipart/form-data" class="modal-body">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-8"><label class="form-label">Judul</label><input type="text" name="judul" class="form-control" required></div>
                            <div class="col-md-4">
                                <label class="form-label">Kategori</label>
                                <select name="kategori" class="form-select">
                                    @foreach($kategoriArtikel as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach
                                </select>
                            </div>
                            <div class="col-md-6"><label class="form-label">Ringkasan</label><input type="text" name="ringkasan" class="form-control"></div>
                            <div class="col-md-3"><label class="form-label">Penulis</label><input type="text" name="penulis" class="form-control" value="Tim MagangKu"></div>
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="published">Published</option>
                                    <option value="draft">Draft</option>
                                </select>
                            </div>
                            <div class="col-md-9"><label class="form-label">Konten</label><textarea name="konten" rows="8" class="form-control" required></textarea></div>
                            <div class="col-md-3"><label class="form-label">Gambar (opsional)</label><input type="file" name="gambar" class="form-control" accept="image/*"></div>
                        </div>
                        <button class="btn btn-mk w-100 mt-3" data-confirm="Simpan data artikel ini?">Simpan Artikel</button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- ============ BANNER ============ --}}
    @if($tab === 'banner')
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-mk" data-bs-toggle="modal" data-bs-target="#modalBanner"><i class="bi bi-plus-circle me-1"></i>Tambah Banner</button>
        </div>
        <div class="card card-hover p-4" data-aos="fade-up">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Banner</th><th>Posisi</th><th>Urutan</th><th>Aktif</th><th class="text-end">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($banners as $banner)
                            <tr>
                                <td>
                                    <div class="fw-semibold small">{{ $banner->judul }}</div>
                                    <div class="small text-muted-2">{{ Str::limit($banner->teks, 50) }}</div>
                                </td>
                                <td><span class="badge rounded-pill text-bg-info">{{ $banner->posisi }}</span></td>
                                <td class="small">{{ $banner->urutan }}</td>
                                <td>
                                    <span class="badge rounded-pill text-bg-{{ $banner->is_active ? 'success' : 'secondary' }}">{{ $banner->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-soft btn-sm" data-bs-toggle="modal" data-bs-target="#modalBanner"
                                        data-edit='{{ json_encode(['id' => $banner->id, 'judul' => $banner->judul, 'teks' => $banner->teks, 'link' => $banner->link, 'posisi' => $banner->posisi, 'urutan' => $banner->urutan, 'is_active' => $banner->is_active], JSON_HEX_APOS | JSON_HEX_QUOT) }}'>
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.content.destroy', ['resource' => 'banner', 'item' => $banner->id]) }}" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-outline-danger btn-sm" data-confirm="Hapus banner ini? Aksi ini tidak bisa dibatalkan."><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-5 text-muted-2">Belum ada banner.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal fade" id="modalBanner" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-2">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Tambah Banner</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('admin.content.store', ['resource' => 'banner']) }}" enctype="multipart/form-data" class="modal-body d-grid gap-3">
                        @csrf
                        <div><label class="form-label">Judul</label><input type="text" name="judul" class="form-control" required></div>
                        <div><label class="form-label">Teks</label><textarea name="teks" rows="2" class="form-control"></textarea></div>
                        <div><label class="form-label">Link</label><input type="url" name="link" class="form-control"></div>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label">Posisi</label>
                                <select name="posisi" class="form-select">
                                    <option value="home">Home</option>
                                    <option value="jobs">Halaman Lowongan</option>
                                </select>
                            </div>
                            <div class="col-6"><label class="form-label">Urutan</label><input type="number" name="urutan" class="form-control" value="0"></div>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="bannerActive" value="1" checked>
                            <label class="form-check-label small" for="bannerActive">Banner Aktif</label>
                        </div>
                        <div><label class="form-label">Gambar Banner</label><input type="file" name="gambar" class="form-control" accept="image/*" required></div>
                        <button class="btn btn-mk w-100" data-confirm="Simpan data banner ini?">Simpan Banner</button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- ============ FAQ ============ --}}
    @if($tab === 'faq')
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-mk" data-bs-toggle="modal" data-bs-target="#modalFaq"><i class="bi bi-plus-circle me-1"></i>Tambah FAQ</button>
        </div>
        <div class="card card-hover p-4" data-aos="fade-up">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Pertanyaan</th><th>Kategori</th><th>Urutan</th><th>Aktif</th><th class="text-end">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($faqs as $faq)
                            <tr>
                                <td>
                                    <div class="fw-semibold small">{{ $faq->pertanyaan }}</div>
                                    <div class="small text-muted-2 text-truncate" style="max-width:400px;">{{ $faq->jawaban }}</div>
                                </td>
                                <td><span class="badge rounded-pill text-bg-info">{{ $faq->kategori }}</span></td>
                                <td class="small">{{ $faq->urutan }}</td>
                                <td><span class="badge rounded-pill text-bg-{{ $faq->is_active ? 'success' : 'secondary' }}">{{ $faq->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                                <td class="text-end">
                                    <button class="btn btn-soft btn-sm" data-bs-toggle="modal" data-bs-target="#modalFaq"
                                        data-edit='{{ json_encode(['id' => $faq->id, 'pertanyaan' => $faq->pertanyaan, 'jawaban' => $faq->jawaban, 'kategori' => $faq->kategori, 'urutan' => $faq->urutan, 'is_active' => $faq->is_active], JSON_HEX_APOS | JSON_HEX_QUOT) }}'>
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.content.destroy', ['resource' => 'faq', 'item' => $faq->id]) }}" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-outline-danger btn-sm" data-confirm="Hapus FAQ ini? Aksi ini tidak bisa dibatalkan."><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-5 text-muted-2">Belum ada FAQ.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal fade" id="modalFaq" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-2">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Tambah FAQ</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('admin.content.store', ['resource' => 'faq']) }}" class="modal-body d-grid gap-3">
                        @csrf
                        <div><label class="form-label">Pertanyaan</label><input type="text" name="pertanyaan" class="form-control" required></div>
                        <div><label class="form-label">Jawaban</label><textarea name="jawaban" rows="4" class="form-control" required></textarea></div>
                        <div class="row g-3">
                            <div class="col-6"><label class="form-label">Kategori</label><input type="text" name="kategori" class="form-control" value="umum"></div>
                            <div class="col-6"><label class="form-label">Urutan</label><input type="number" name="urutan" class="form-control" value="0"></div>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="faqActive" value="1" checked>
                            <label class="form-check-label small" for="faqActive">FAQ Aktif</label>
                        </div>
                        <button class="btn btn-mk w-100" data-confirm="Simpan data FAQ ini?">Simpan FAQ</button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- ============ TESTIMONI ============ --}}
    @if($tab === 'testimoni')
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-mk" data-bs-toggle="modal" data-bs-target="#modalTestimoni"><i class="bi bi-plus-circle me-1"></i>Tambah Testimoni</button>
        </div>
        <div class="card card-hover p-4" data-aos="fade-up">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Nama</th><th>Universitas</th><th>Komentar</th><th>Aktif</th><th class="text-end">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($testimonials as $testimoni)
                            <tr>
                                <td>
                                    <div class="fw-semibold small">{{ $testimoni->nama }}</div>
                                    <div class="small text-muted-2">{{ $testimoni->role }}</div>
                                </td>
                                <td class="small">{{ $testimoni->universitas }}</td>
                                <td class="small text-truncate" style="max-width:300px;">{{ $testimoni->komentar }}</td>
                                <td><span class="badge rounded-pill text-bg-{{ $testimoni->is_active ? 'success' : 'secondary' }}">{{ $testimoni->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                                <td class="text-end">
                                    <button class="btn btn-soft btn-sm" data-bs-toggle="modal" data-bs-target="#modalTestimoni"
                                        data-edit='{{ json_encode(['id' => $testimoni->id, 'nama' => $testimoni->nama, 'universitas' => $testimoni->universitas, 'role' => $testimoni->role, 'komentar' => $testimoni->komentar, 'is_active' => $testimoni->is_active], JSON_HEX_APOS | JSON_HEX_QUOT) }}'>
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.content.destroy', ['resource' => 'testimoni', 'item' => $testimoni->id]) }}" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-outline-danger btn-sm" data-confirm="Hapus testimoni dari &quot;{{ $testimoni->nama }}&quot;? Aksi ini tidak bisa dibatalkan."><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-5 text-muted-2">Belum ada testimoni.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal fade" id="modalTestimoni" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-2">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Tambah Testimoni</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('admin.content.store', ['resource' => 'testimoni']) }}" enctype="multipart/form-data" class="modal-body d-grid gap-3">
                        @csrf
                        <div class="row g-3">
                            <div class="col-6"><label class="form-label">Nama</label><input type="text" name="nama" class="form-control" required></div>
                            <div class="col-6"><label class="form-label">Universitas</label><input type="text" name="universitas" class="form-control"></div>
                            <div class="col-6"><label class="form-label">Role / Status</label><input type="text" name="role" class="form-control" placeholder="Peserta Magang"></div>
                            <div class="col-6"><label class="form-label">Foto (opsional)</label><input type="file" name="foto" class="form-control" accept="image/*"></div>
                        </div>
                        <div><label class="form-label">Komentar</label><textarea name="komentar" rows="4" class="form-control" required></textarea></div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="testimoniActive" value="1" checked>
                            <label class="form-check-label small" for="testimoniActive">Testimoni Aktif</label>
                        </div>
                        <button class="btn btn-mk w-100" data-confirm="Simpan data testimoni ini?">Simpan Testimoni</button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- ============ KATEGORI & SKILL ============ --}}
    @if($tab === 'kategori')
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-mk" data-bs-toggle="modal" data-bs-target="#modalKategori"><i class="bi bi-plus-circle me-1"></i>Tambah Kategori</button>
        </div>
        <div class="card card-hover p-4" data-aos="fade-up">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Kategori</th><th>Slug</th><th>Tipe</th><th class="text-end">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($categories as $kategori)
                            <tr>
                                <td class="fw-semibold small">{{ $kategori->nama }}</td>
                                <td class="small text-muted-2">{{ $kategori->slug }}</td>
                                <td><span class="badge rounded-pill text-bg-info">{{ $kategori->tipe }}</span></td>
                                <td class="text-end">
                                    <button class="btn btn-soft btn-sm" data-bs-toggle="modal" data-bs-target="#modalKategori"
                                        data-edit='{{ json_encode(['id' => $kategori->id, 'nama' => $kategori->nama, 'tipe' => $kategori->tipe, 'icon' => $kategori->icon], JSON_HEX_APOS | JSON_HEX_QUOT) }}'>
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.content.destroy', ['resource' => 'kategori', 'item' => $kategori->id]) }}" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-outline-danger btn-sm" data-confirm="Hapus kategori &quot;{{ $kategori->nama }}&quot;? Aksi ini tidak bisa dibatalkan."><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-5 text-muted-2">Belum ada kategori.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal fade" id="modalKategori" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-2">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Tambah Kategori</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('admin.content.store', ['resource' => 'kategori']) }}" class="modal-body d-grid gap-3">
                        @csrf
                        <div><label class="form-label">Nama</label><input type="text" name="nama" class="form-control" required></div>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label">Tipe</label>
                                <select name="tipe" class="form-select">
                                    <option value="lowongan">Lowongan</option>
                                    <option value="event">Event</option>
                                    <option value="artikel">Artikel</option>
                                </select>
                            </div>
                            <div class="col-6"><label class="form-label">Icon (opsional)</label><input type="text" name="icon" class="form-control" placeholder="bi-code-slash"></div>
                        </div>
                        <button class="btn btn-mk w-100" data-confirm="Simpan data kategori ini?">Simpan Kategori</button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if($tab === 'skill')
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div class="small text-muted-2"><i class="bi bi-collection me-1"></i>Total <b>{{ $skills->count() }}</b> skill dari <b>{{ $skills->groupBy('kategori')->count() }}</b> bidang</div>
            <button class="btn btn-mk" data-bs-toggle="modal" data-bs-target="#modalSkill"><i class="bi bi-plus-circle me-1"></i>Tambah Skill</button>
        </div>

        @php $skillGroups = $skills->sortBy('kategori')->groupBy('kategori'); @endphp
        @forelse($skillGroups as $kategori => $group)
            <div class="card card-hover p-4 mb-3" data-aos="fade-up">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <h6 class="fw-bold mb-0">
                        <i class="bi {{ match ($kategori) {
                            'teknologi' => 'bi-code-slash',
                            'desain' => 'bi-palette',
                            'marketing' => 'bi-megaphone',
                            'keuangan' => 'bi-cash-coin',
                            'sdm' => 'bi-people',
                            'administrasi' => 'bi-clipboard-check',
                            'pendidikan' => 'bi-mortarboard',
                            'media' => 'bi-camera-reels',
                            'hukum' => 'bi-bank',
                            'kesehatan' => 'bi-heart-pulse',
                            'konstruksi' => 'bi-bricks',
                            default => 'bi-stars',
                        } }} me-2 text-mk"></i>
                        {{ \App\Models\Skill::KATEGORI[$kategori] ?? $kategori }}
                        <span class="badge rounded-pill text-bg-light text-muted-2 ms-1">{{ $group->count() }}</span>
                    </h6>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($group as $skill)
                        <div class="d-inline-flex align-items-center gap-1 badge rounded-pill text-bg-light border px-3 py-2" style="font-size:.8rem;font-weight:500;">
                            {{ $skill->nama }}
                            <button type="button" class="btn p-0 border-0 text-muted-2" style="line-height:1;" data-bs-toggle="modal" data-bs-target="#modalSkill"
                                data-edit='{{ json_encode(['id' => $skill->id, 'nama' => $skill->nama, 'kategori' => $skill->kategori], JSON_HEX_APOS | JSON_HEX_QUOT) }}'
                                title="Edit {{ $skill->nama }}">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form method="POST" action="{{ route('admin.content.destroy', ['resource' => 'skill', 'item' => $skill->id]) }}" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="button" class="btn p-0 border-0 text-danger" style="line-height:1;" title="Hapus {{ $skill->nama }}" data-confirm="Hapus skill &quot;{{ $skill->nama }}&quot;? Aksi ini tidak bisa dibatalkan."><i class="bi bi-x-circle"></i></button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="card card-hover p-5 text-center" data-aos="fade-up">
                <i class="bi bi-lightning fs-1 text-muted-2"></i>
                <p class="text-muted-2 mt-2 mb-0">Belum ada skill.</p>
            </div>
        @endforelse

        <div class="modal fade" id="modalSkill" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-2">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Tambah Skill</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('admin.content.store', ['resource' => 'skill']) }}" class="modal-body d-grid gap-3">
                        @csrf
                        <div><label class="form-label">Nama Skill</label><input type="text" name="nama" class="form-control" required placeholder="Laravel, React, Figma..."></div>
                        <div>
                            <label class="form-label">Bidang / Kategori</label>
                            <select name="kategori" class="form-select" required>
                                @foreach(\App\Models\Skill::KATEGORI as $key => $label)
                                    <option value="{{ $key }}" @selected(old('kategori', 'umum') === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn btn-mk w-100" data-confirm="Simpan data skill ini?">Simpan Skill</button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @include('partials.confirm-modal')
@endsection

@push('scripts')
<script>
    // Helper: isi form modal untuk edit (ubah action & method)
    function setupEditModal(modalId, editBtnSelector, fields, updateUrl) {
        document.querySelectorAll(editBtnSelector).forEach(btn => {
            btn.addEventListener('click', () => {
                const data = JSON.parse(btn.dataset.edit || '{}');
                const modal = document.getElementById(modalId);
                const form = modal.querySelector('form');
                const title = modal.querySelector('.modal-title');
                title.textContent = 'Edit';
                form.action = updateUrl.replace('__ID__', data.id);
                // method spoofing via hidden input
                let methodInput = form.querySelector('input[name="_method"]');
                if (!methodInput) {
                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    form.prepend(methodInput);
                }
                methodInput.value = 'PUT';
                fields.forEach(f => {
                    const input = form.querySelector(`[name="${f}"]`);
                    if (input) input.value = data[f] ?? '';
                });
            });
        });
        // Reset saat modal ditutup setelah dibuka
        document.getElementById(modalId)?.addEventListener('hidden.bs.modal', () => {
            const form = document.getElementById(modalId).querySelector('form');
            form.reset();
            const methodInput = form.querySelector('input[name="_method"]');
            if (methodInput) methodInput.value = 'POST';
            const title = document.getElementById(modalId).querySelector('.modal-title');
            title.textContent = 'Tambah';
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        setupEditModal('modalEvent', '[data-bs-target="#modalEvent"][data-edit]', ['judul', 'kategori', 'deskripsi', 'tanggal_mulai', 'tanggal_selesai', 'waktu', 'lokasi', 'link_event', 'kapasitas', 'penyelenggara', 'status'], '{{ route('admin.content.update', ['resource' => 'event', 'item' => '__ID__']) }}');
        setupEditModal('modalArtikel', '[data-bs-target="#modalArtikel"][data-edit]', ['judul', 'kategori', 'ringkasan', 'konten', 'penulis', 'status'], '{{ route('admin.content.update', ['resource' => 'artikel', 'item' => '__ID__']) }}');
        setupEditModal('modalBanner', '[data-bs-target="#modalBanner"][data-edit]', ['judul', 'teks', 'link', 'posisi', 'urutan'], '{{ route('admin.content.update', ['resource' => 'banner', 'item' => '__ID__']) }}');
        setupEditModal('modalFaq', '[data-bs-target="#modalFaq"][data-edit]', ['pertanyaan', 'jawaban', 'kategori', 'urutan'], '{{ route('admin.content.update', ['resource' => 'faq', 'item' => '__ID__']) }}');
        setupEditModal('modalTestimoni', '[data-bs-target="#modalTestimoni"][data-edit]', ['nama', 'universitas', 'role', 'komentar'], '{{ route('admin.content.update', ['resource' => 'testimoni', 'item' => '__ID__']) }}');
        setupEditModal('modalKategori', '[data-bs-target="#modalKategori"][data-edit]', ['nama', 'tipe', 'icon'], '{{ route('admin.content.update', ['resource' => 'kategori', 'item' => '__ID__']) }}');
        setupEditModal('modalSkill', '[data-bs-target="#modalSkill"][data-edit]', ['nama', 'kategori'], '{{ route('admin.content.update', ['resource' => 'skill', 'item' => '__ID__']) }}');
    });
</script>
@endpush
