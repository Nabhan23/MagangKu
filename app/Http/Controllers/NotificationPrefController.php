<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class NotificationPrefController extends Controller
{
    public function index()
    {
        $types = collect(\App\Models\User::notificationTypes())
            ->filter(fn ($t) => in_array(auth()->user()->role?->slug, $t['roles']))
            ->map(fn ($t, $key) => ['key' => $key] + $t);

        return view('notifikasi-prefs', compact('types'));
    }

    public function update(Request $request)
    {
        $allowed = array_keys(\App\Models\User::notificationTypes());
        $request->validate([
            'prefs' => ['nullable', 'array'],
            // Key di-whitelist oleh loop di bawah; nilai checkbox hanya 0/1/on/true
            'prefs.*' => ['nullable', 'in:0,1,on,true'],
        ]);

        $prefs = [];
        foreach ($allowed as $type) {
            $prefs[$type] = isset($request->prefs[$type]);
        }

        auth()->user()->update(['notification_prefs' => $prefs]);

        return back()->with('success', 'Preferensi notifikasi email berhasil disimpan.');
    }
}
