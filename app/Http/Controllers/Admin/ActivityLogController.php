<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('aksi')) {
            $query->where('aksi', $request->aksi);
        }

        if ($request->filled('user')) {
            $query->where('user_id', (int) $request->user);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }

        return view('admin.activity', [
            'logs' => $query->paginate(20)->withQueryString(),
            'aksiList' => ActivityLog::select('aksi')->distinct()->orderBy('aksi')->pluck('aksi'),
            'users' => ActivityLog::with('user')->select('user_id')->distinct()->get()->pluck('user.name', 'user_id')->filter(),
            'filters' => $request->only(['aksi', 'user', 'tanggal']),
        ]);
    }
}
