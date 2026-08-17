<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lowongan;
use Illuminate\Http\Request;

class LowonganController extends Controller
{
    public function index(Request $request)
    {
        $query = Lowongan::with(['perusahaan', 'category'])->withCount('lamarans');

        if ($request->filled('q')) {
            $query->where('posisi', 'like', "%{$request->q}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $lowongans = $query->latest()->paginate(12)->withQueryString();

        return view('admin.lowongan', ['lowongans' => $lowongans]);
    }

    public function toggle(Lowongan $lowongan)
    {
        $lowongan->update(['status' => $lowongan->status === 'aktif' ? 'ditutup' : 'aktif']);

        return back()->with('success', "Lowongan {$lowongan->posisi} " . ($lowongan->status === 'aktif' ? 'diaktifkan' : 'ditutup') . '.');
    }

    public function destroy(Lowongan $lowongan)
    {
        $lowongan->delete();

        return back()->with('success', 'Lowongan dihapus.');
    }
}
