<?php

namespace App\Http\Controllers;

use App\Models\EventKarir;
use App\Models\EventRegistration;
use Illuminate\Http\Request;

class CompanyEventController extends Controller
{
    public function index(Request $request)
    {
        $query = EventKarir::where('status', 'aktif');

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('q')) {
            $query->where('judul', 'like', "%{$request->q}%");
        }

        $events = $query->withCount('registrations')->orderBy('tanggal_mulai')->paginate(6)->withQueryString();

        return view('events.index', [
            'events' => $events,
            'kategoriList' => EventKarir::KATEGORI,
            'activeKategori' => $request->kategori,
        ]);
    }

    public function show(EventKarir $event)
    {
        $event->increment('views');
        $registered = auth()->check()
            ? EventRegistration::where('event_id', $event->id)->where('user_id', auth()->id())->exists()
            : false;

        return view('events.show', [
            'event' => $event->loadCount('registrations'),
            'registered' => $registered,
        ]);
    }

    public function register(Request $request, EventKarir $event)
    {
        $request->validate([
            'nama' => ['nullable', 'string', 'max:255'],
            'telepon' => ['nullable', 'string', 'max:20'],
        ]);

        $exists = EventRegistration::where('event_id', $event->id)->where('user_id', auth()->id())->exists();
        if ($exists) {
            return back()->with('error', 'Anda sudah terdaftar di event ini.');
        }

        if ($event->terdaftar >= $event->kapasitas) {
            return back()->with('error', 'Kuota event sudah penuh.');
        }

        EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => auth()->id(),
            'nama' => $request->nama ?: auth()->user()->name,
            'email' => auth()->user()->email,
            'telepon' => $request->telepon ?: auth()->user()->phone,
        ]);

        \App\Models\Notification::create([
            'user_id' => auth()->id(),
            'judul' => 'Registrasi Event Berhasil',
            'pesan' => "Anda berhasil terdaftar di event \"{$event->judul}\".",
            'type' => 'event',
            'link' => route('events.show', $event),
        ]);

        return back()->with('success', 'Registrasi event berhasil! Cek email Anda untuk detail.');
    }
}
