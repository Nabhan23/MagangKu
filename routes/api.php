<?php

use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/lowongan', [ApiController::class, 'jobs']);
    Route::get('/lowongan/{lowongan}', [ApiController::class, 'jobDetail']);
    Route::get('/perusahaan', [ApiController::class, 'companies']);
    Route::get('/event', [ApiController::class, 'events']);
    Route::get('/artikel', [ApiController::class, 'articles']);
    Route::get('/statistik', [ApiController::class, 'stats']);
    Route::get('/cari', [ApiController::class, 'search']);
});
