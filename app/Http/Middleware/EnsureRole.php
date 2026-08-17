<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user || !$user->role) {
            abort(403, 'Akses ditolak.');
        }

        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        // User terautentikasi tapi role-nya tidak sesuai: arahkan ke dashboard-nya
        // sendiri alih-alih menampilkan halaman 403 yang buntu.
        $dashboard = match ($user->role->slug) {
            \App\Models\Role::PELAMAR => 'pelamar.dashboard',
            \App\Models\Role::PERUSAHAAN => 'perusahaan.dashboard',
            \App\Models\Role::SUPER_ADMIN => 'admin.dashboard',
            default => 'home',
        };

        return redirect()->route($dashboard);
    }
}
