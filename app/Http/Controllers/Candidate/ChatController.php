<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Lamaran;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Daftar percakapan pelamar (satu thread per lamaran).
     */
    public function index()
    {
        $profile = auth()->user()->pelamarProfile;

        $conversations = Lamaran::where('pelamar_profile_id', $profile->id)
            ->with(['lowongan.perusahaan', 'messages' => fn ($q) => $q->latest()])
            ->withCount(['messages as unread_count' => fn ($q) => $q->where('receiver_id', auth()->id())->where('is_read', false)])
            ->latest()
            ->get()
            ->filter(fn ($l) => $l->messages->isNotEmpty())
            ->values();

        return view('chats.index', [
            'conversations' => $conversations,
            'title' => 'Chat Saya',
            'emptyText' => 'Belum ada percakapan. Mulai dengan mengirim pesan dari halaman lamaran Anda.',
        ]);
    }

    public function show(Lamaran $lamaran)
    {
        $this->authorizeOwn($lamaran);

        $lamaran->load('lowongan.perusahaan');

        // Tandai pesan masuk sudah dibaca
        Message::where('lamaran_id', $lamaran->id)
            ->where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = $lamaran->messages()->with('sender')->orderBy('created_at')->get();

        return view('chats.show', [
            'lamaran' => $lamaran,
            'messages' => $messages,
            'title' => $lamaran->lowongan->posisi,
            'subtitle' => $lamaran->lowongan->perusahaan->nama_perusahaan,
            'sendUrl' => route('pelamar.chat.send', $lamaran),
            'messagesUrl' => route('pelamar.chat.messages', $lamaran),
            'backUrl' => route('pelamar.chat.index'),
        ]);
    }

    public function send(Request $request, Lamaran $lamaran)
    {
        $this->authorizeOwn($lamaran);

        $data = $request->validate(['pesan' => ['required', 'string', 'max:2000']]);

        $receiver = $lamaran->lowongan->created_by ?: $lamaran->lowongan->perusahaan->user_id;

        $message = Message::create([
            'lamaran_id' => $lamaran->id,
            'sender_id' => auth()->id(),
            'receiver_id' => $receiver,
            'pesan' => $data['pesan'],
        ]);

        return response()->json([
            'id' => $message->id,
            'pesan' => $message->pesan,
            'created_at' => $message->created_at->diffForHumans(),
        ]);
    }

    /**
     * Ambil pesan baru (polling AJAX). ?after=id_messages
     */
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
        abort_unless(
            $lamaran->pelamar_profile_id === auth()->user()->pelamarProfile->id,
            403
        );
    }
}
