<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Lamaran;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        $perusahaan = auth()->user()->perusahaanProfile;

        $conversations = Lamaran::whereIn('lowongan_id', $perusahaan->lowongans()->pluck('id'))
            ->with(['pelamar.user', 'lowongan', 'messages' => fn ($q) => $q->latest()])
            ->withCount(['messages as unread_count' => fn ($q) => $q->where('receiver_id', auth()->id())->where('is_read', false)])
            ->latest()
            ->get()
            ->filter(fn ($l) => $l->messages->isNotEmpty())
            ->values();

        return view('chats.index', [
            'conversations' => $conversations,
            'title' => 'Chat Pelamar',
            'emptyText' => 'Belum ada percakapan. Pelamar akan menghubungi Anda lewat chat ini.',
        ]);
    }

    public function show(Lamaran $lamaran)
    {
        $this->authorizeOwn($lamaran);

        $lamaran->load(['pelamar.user', 'lowongan']);

        Message::where('lamaran_id', $lamaran->id)
            ->where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = $lamaran->messages()->with('sender')->orderBy('created_at')->get();

        return view('chats.show', [
            'lamaran' => $lamaran,
            'messages' => $messages,
            'title' => $lamaran->pelamar->nama,
            'subtitle' => $lamaran->lowongan->posisi . ' • ' . $lamaran->lowongan->perusahaan->nama_perusahaan,
            'sendUrl' => route('perusahaan.chat.send', $lamaran),
            'messagesUrl' => route('perusahaan.chat.messages', $lamaran),
            'backUrl' => route('perusahaan.chat.index'),
        ]);
    }

    public function send(Request $request, Lamaran $lamaran)
    {
        $this->authorizeOwn($lamaran);

        $data = $request->validate(['pesan' => ['required', 'string', 'max:2000']]);

        $message = Message::create([
            'lamaran_id' => $lamaran->id,
            'sender_id' => auth()->id(),
            'receiver_id' => $lamaran->pelamar->user_id,
            'pesan' => $data['pesan'],
        ]);

        return response()->json([
            'id' => $message->id,
            'pesan' => $message->pesan,
            'created_at' => $message->created_at->diffForHumans(),
        ]);
    }

    public function messages(Request $request, Lamaran $lamaran)
    {
        $this->authorizeOwn($lamaran);

        $query = Message::where('lamaran_id', $lamaran->id)->with('sender');
        if ($request->filled('after')) {
            $query->where('id', '>', (int) $request->after);
        }

        return response()->json(
            $query->orderBy('created_at')->get()->map(fn (Message $m) => [
                'id' => $m->id,
                'pesan' => $m->pesan,
                'mine' => $m->sender_id === auth()->id(),
                'sender' => $m->sender->name,
                'waktu' => $m->created_at->translatedFormat('H:i'),
            ])
        );
    }

    protected function authorizeOwn(Lamaran $lamaran): void
    {
        $perusahaan = auth()->user()->perusahaanProfile;
        abort_unless($perusahaan->lowongans()->where('id', $lamaran->lowongan_id)->exists(), 403);
    }
}
