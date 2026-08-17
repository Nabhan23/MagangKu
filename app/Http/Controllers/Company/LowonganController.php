<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Mail\JobAlertMail;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Lowongan;
use App\Models\Notification;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class LowonganController extends Controller
{
    public function index()
    {
        $lowongans = auth()->user()->perusahaanProfile
            ->lowongans()
            ->withCount('lamarans')
            ->latest()
            ->paginate(10);

        return view('perusahaan.lowongan.index', ['lowongans' => $lowongans]);
    }

    public function create()
    {
        return view('perusahaan.lowongan.form', [
            'lowongan' => new Lowongan,
            'kategoriList' => Category::where('tipe', 'lowongan')->get(),
            'skills' => Skill::orderBy('nama')->get(),
            'jenisList' => Lowongan::JENIS_LABEL,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $lowongan = auth()->user()->perusahaanProfile->lowongans()->create($data + [
            'slug' => Str::slug($data['posisi']) . '-' . Str::random(5),
            'created_by' => auth()->id(),
        ]);

        $lowongan->skills()->sync($request->input('skills', []));

        ActivityLog::record('lowongan_create', "Membuat lowongan {$lowongan->posisi}", auth()->id());

        $this->notifyFollowers($lowongan);

        return redirect()->route('perusahaan.lowongan.index')->with('success', 'Lowongan berhasil dipublikasikan.');
    }

    public function edit(Lowongan $lowongan)
    {
        $this->authorizeOwn($lowongan);

        return view('perusahaan.lowongan.form', [
            'lowongan' => $lowongan->load('skills'),
            'kategoriList' => Category::where('tipe', 'lowongan')->get(),
            'skills' => Skill::orderBy('nama')->get(),
            'jenisList' => Lowongan::JENIS_LABEL,
        ]);
    }

    public function update(Request $request, Lowongan $lowongan)
    {
        $this->authorizeOwn($lowongan);

        $data = $this->validateData($request);
        $lowongan->update($data);
        $lowongan->skills()->sync($request->input('skills', []));

        ActivityLog::record('lowongan_update', "Memperbarui lowongan {$lowongan->posisi}", auth()->id());

        return redirect()->route('perusahaan.lowongan.index')->with('success', 'Lowongan berhasil diperbarui.');
    }

    public function destroy(Lowongan $lowongan)
    {
        $this->authorizeOwn($lowongan);
        $lowongan->delete();

        return back()->with('success', 'Lowongan dihapus.');
    }

    /**
     * Kirim notifikasi (in-app + email) lowongan baru ke seluruh follower perusahaan.
     */
    protected function notifyFollowers(Lowongan $lowongan): void
    {
        $perusahaan = $lowongan->perusahaan;

        foreach ($perusahaan->followers()->with('user')->get() as $follow) {
            Notification::create([
                'user_id' => $follow->user_id,
                'judul' => 'Lowongan Baru dari ' . $perusahaan->nama_perusahaan . ' 🎉',
                'pesan' => $lowongan->posisi . ' (' . $lowongan->jenis_label . ') • ' . ($lowongan->lokasi ?: 'Remote') . '. Lamar sekarang!',
                'type' => 'lowongan',
                'link' => route('jobs.show', $lowongan),
            ]);

            if ($follow->user->wantsEmail('lowongan_baru')) {
                try {
                    Mail::to($follow->user->email)->send(new JobAlertMail($lowongan));
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        }
    }

    protected function authorizeOwn(Lowongan $lowongan): void
    {
        abort_unless($lowongan->perusahaan_profile_id === auth()->user()->perusahaanProfile->id, 403);
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'posisi' => ['required', 'string', 'max:255'],
            'jenis' => ['required', 'in:magang,full_time,part_time,freelance,remote'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'deskripsi' => ['required', 'string', 'max:10000'],
            'persyaratan' => ['nullable', 'string', 'max:10000'],
            'gaji_min' => ['nullable', 'numeric', 'min:0'],
            'gaji_max' => ['nullable', 'numeric', 'gte:gaji_min'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'is_remote' => ['nullable', 'boolean'],
            'kuota' => ['nullable', 'integer', 'min:1', 'max:999'],
            'deadline' => ['required', 'date', 'after_or_equal:today'],
            'status' => ['required', 'in:aktif,draft,ditutup'],
        ]);
    }
}
