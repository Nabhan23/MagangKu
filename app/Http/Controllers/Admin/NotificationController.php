<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        return view('admin.notifications', [
            'recent' => Notification::with('user')->latest()->take(10)->get(),
        ]);
    }

    public function broadcast(Request $request)
    {
        $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'pesan' => ['required', 'string', 'max:2000'],
            'target' => ['required', 'in:semua,pelamar,perusahaan'],
        ]);

        $query = User::where('is_active', true);
        if ($request->target !== 'semua') {
            $query->where('role_id', Role::where('slug', $request->target)->value('id'));
        }

        $count = 0;
        $query->chunkById(200, function ($users) use ($request, &$count) {
            foreach ($users as $user) {
                Notification::create([
                    'user_id' => $user->id,
                    'judul' => $request->judul,
                    'pesan' => $request->pesan,
                    'type' => 'broadcast',
                ]);
                $count++;
            }
        });

        return back()->with('success', "Notifikasi terkirim ke {$count} pengguna.");
    }
}
