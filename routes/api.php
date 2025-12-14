<?php

use App\Http\Controllers\Api\AdminKamarController;
use App\Http\Controllers\Api\AlbumController;
use App\Http\Controllers\Api\BeritaController;
use App\Http\Controllers\Api\DonasiController;
use App\Http\Controllers\Api\OrangTuaController;
use App\Http\Controllers\Api\PendaftaranController;
use App\Http\Controllers\Api\PengumumanController;
use App\Http\Controllers\Api\PerizinanController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SiswaController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);



Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/pendaftaran', [PendaftaranController::class, 'store']);

    Route::get('/orangtua', [OrangTuaController::class, 'show']);
    Route::post('/orangtua', [OrangTuaController::class, 'upsert']);

    Route::get('/students', [SiswaController::class, 'index']);
    Route::get('/student/{student}', [SiswaController::class, 'show']);
    Route::post('/students', [SiswaController::class, 'store']);

    Route::get('/perizinan', [PerizinanController::class, 'index']);  
    Route::get('/perizinan/{permit}', [PerizinanController::class, 'show']); 
    Route::post('/perizinan', [PerizinanController::class, 'store']);

    Route::get('/admin/kamar', [AdminKamarController::class, 'index'])->name('admin.kamar.index');
    Route::post('/admin/kamar/lock-distribute', [AdminKamarController::class, 'lockAndDistribute'])->name('admin.kamar.lock');
    Route::post('/admin/kamar/move', [AdminKamarController::class, 'move'])->name('admin.kamar.move');
    Route::post('/admin/kamar/release', [AdminKamarController::class, 'release'])->name('admin.kamar.release');

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::patch('/profile/{user}', [ProfileController::class, 'update']);
    Route::patch('/profile/{user}/password', [ProfileController::class, 'updatePassword']);

});

Route::get('/pengumuman',[PengumumanController::class, 'index']);
Route::get('/pengumuman/{slug}',[PengumumanController::class, 'show']);

Route::get('/berita',[BeritaController::class, 'index']);
Route::get('/berita/{slug}',[BeritaController::class, 'show']);

Route::get('/donasi',[DonasiController::class,'index']);
Route::post('/donasi',[DonasiController::class,'store']);

Route::get('/album', [AlbumController::class, 'index']);