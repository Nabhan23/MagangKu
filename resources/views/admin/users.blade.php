@extends('layouts.app')

@section('title', 'Kelola Pengguna - MagangKu')
@section('page-title', 'Kelola Pengguna')

@section('content')
    <ul class="nav nav-pills mb-4 gap-2" data-aos="fade-up">
        @foreach(['pelamar' => 'Pelamar', 'perusahaan' => 'Perusahaan'] as $key => $label)
            <li class="nav-item">
                <a class="nav-link rounded-pill px-3 {{ $activeRole === $key ? 'active bg-mk-gradient text-white' : 'bg-soft text-accent' }}" href="{{ route('admin.users.index', ['role' => $key]) }}">
                    {{ $label }}
                    <span class="badge rounded-pill text-bg-light ms-1">{{ $counts[$key] }}</span>
                </a>
            </li>
        @endforeach
    </ul>

    <div class="card card-hover p-4" data-aos="fade-up">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>@if($activeRole === 'pelamar') Universitas @else Industri @endif</th>
                        <th>Bergabung</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($user->avatar_url)
                                        <img src="{{ $user->avatar_url }}" class="rounded-circle" width="36" height="36" style="object-fit:cover;">
                                    @else
                                        <div class="avatar avatar-sm" style="background:var(--mk-gradient);">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                    @endif
                                    <div>
                                        <div class="fw-semibold small">{{ $user->name }}</div>
                                        <div class="small text-muted-2">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="small">
                                @if($activeRole === 'pelamar')
                                    {{ $user->pelamarProfile?->universitas ?: '-' }}
                                @else
                                    {{ $user->perusahaanProfile?->industri ?: '-' }}
                                @endif
                            </td>
                            <td class="small text-muted-2">{{ $user->created_at->translatedFormat('d M Y') }}</td>
                            <td>
                                <span class="badge rounded-pill text-bg-{{ $user->is_active ? 'success' : 'danger' }}">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('admin.users.toggle', $user) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-{{ $user->is_active ? 'warning' : 'success' }} btn-sm">
                                        <i class="bi {{ $user->is_active ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-outline-danger btn-sm" data-confirm="Hapus akun &quot;{{ $user->name }}&quot;? Seluruh data terkait akun ini juga akan ikut terhapus."><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-5 text-muted-2">Tidak ada pengguna.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">{{ $users->links() }}</div>

    @include('partials.confirm-modal')
@endsection
