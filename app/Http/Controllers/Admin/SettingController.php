<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /** Daftar kunci setting yang bisa diedit admin. */
    protected const KEYS = [
        'contact_address',
        'contact_email',
        'contact_phone',
        'contact_hours',
        'social_instagram',
        'social_linkedin',
        'social_facebook',
        'social_tiktok',
        'social_youtube',
    ];

    public function index()
    {
        return view('admin.settings', [
            'settings' => collect(self::KEYS)->mapWithKeys(fn ($k) => [$k => Setting::get($k)]),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'contact_address' => ['nullable', 'string', 'max:500'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_hours' => ['nullable', 'string', 'max:255'],
            'social_instagram' => ['nullable', 'url', 'max:255'],
            'social_linkedin' => ['nullable', 'url', 'max:255'],
            'social_facebook' => ['nullable', 'url', 'max:255'],
            'social_tiktok' => ['nullable', 'url', 'max:255'],
            'social_youtube' => ['nullable', 'url', 'max:255'],
        ]);

        Setting::set($data);

        ActivityLog::record('settings_update', 'Admin memperbarui pengaturan kontak & media sosial', auth()->id());

        return back()->with('success', 'Pengaturan kontak & media sosial berhasil disimpan.');
    }
}
