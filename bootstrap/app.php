<?php

use App\Http\Middleware\EnsureRole;
use App\Http\Middleware\Localize;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/*
 * Beberapa mesin Windows mengekspos variabel lingkungan global (mis. DB_CONNECTION=sqlite,
 * MAIL_FROM_ADDRESS=hello@example.com) yang menimpa nilai dari file .env karena Laravel
 * membaca env OS lebih dulu. Bersihkan variabel tersebut agar .env menjadi satu-satunya
 * sumber konfigurasi (database, mail, dll).
 */
foreach (['DB_CONNECTION', 'MAIL_FROM_ADDRESS', 'APP_LOCALE', 'SESSION_DRIVER', 'CACHE_STORE', 'MAIL_MAILER', 'QUEUE_CONNECTION'] as $envKey) {
    if (getenv($envKey) !== false) {
        putenv($envKey);
        unset($_SERVER[$envKey], $_ENV[$envKey]);
    }
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => EnsureRole::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        ]);

        // Lokalitas (ID/EN): jalankan setelah session tersedia, di dalam grup web.
        $middleware->web(append: [
            Localize::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
