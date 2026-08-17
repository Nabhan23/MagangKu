<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Lowongan;
use Illuminate\Http\Request;

class SavedJobController extends Controller
{
    public function index()
    {
        $saved = auth()->user()->savedJobs()->with('lowongan.perusahaan')->latest()->paginate(8);

        return view('pelamar.saved', ['saved' => $saved]);
    }

    public function toggle(Request $request, Lowongan $lowongan)
    {
        $user = auth()->user();
        $existing = $user->savedJobs()->where('lowongan_id', $lowongan->id)->first();

        if ($existing) {
            $existing->delete();
            $saved = false;
        } else {
            $user->savedJobs()->create(['lowongan_id' => $lowongan->id]);
            $saved = true;
        }

        return response()->json(['saved' => $saved]);
    }
}
