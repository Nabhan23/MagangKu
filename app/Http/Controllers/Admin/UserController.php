<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->get('role', 'pelamar');

        $users = User::whereHas('role', fn ($q) => $q->where('slug', $role))
            ->with($role === 'pelamar' ? 'pelamarProfile' : 'perusahaanProfile')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.users', [
            'users' => $users,
            'activeRole' => $role,
            'counts' => [
                'pelamar' => User::whereHas('role', fn ($q) => $q->where('slug', 'pelamar'))->count(),
                'perusahaan' => User::whereHas('role', fn ($q) => $q->where('slug', 'perusahaan'))->count(),
            ],
        ]);
    }

    public function toggle(User $user)
    {
        abort_if($user->isSuperAdmin(), 403);

        $user->update(['is_active' => !$user->is_active]);

        return back()->with('success', "Akun {$user->name} " . ($user->is_active ? 'diaktifkan' : 'dinonaktifkan') . '.');
    }

    public function destroy(User $user)
    {
        abort_if($user->isSuperAdmin(), 403);

        $user->delete();

        return back()->with('success', "Akun {$user->name} dihapus.");
    }
}
