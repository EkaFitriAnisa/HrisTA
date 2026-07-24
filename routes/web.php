<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Karyawan\ClearanceController;
use App\Http\Controllers\Hod\ClearanceController as HodClearanceController;
use App\Http\Controllers\Mis\ClearanceController as MisClearanceController;
use App\Http\Controllers\Hrd\ClearanceController as HrdClearanceController;

use App\Http\Controllers\Karyawan\DashboardController as KaryawanDashboardControllers;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RiwayatController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::prefix('hrd')->middleware('auth')->name('hrd.')->group(function () {
    Route::resource('dashboard', DashboardController::class)->only(['index'])->names('dashboard');
    Route::resource('clearance', HrdClearanceController::class)->only(['index'])->names('clearance');
    Route::post('/clearance/aset/{clearanceAset}/approve', [HrdClearanceController::class, 'approveAset'])->name('clearance.aset.approve');
    Route::post('/clearance/aset/{clearanceAset}/reject',  [HrdClearanceController::class, 'rejectAset'])->name('clearance.aset.reject');
    Route::post('/clearance/{clearance}/finalize', [HrdClearanceController::class, 'finalize'])->name('clearance.finalize');
    Route::get('riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');
});

Route::prefix('hod')->middleware('auth')->name('hod.')->group(function () {
    Route::resource('dashboard', DashboardController::class)->only(['index'])->names('dashboard');
    Route::resource('clearance', HodClearanceController::class)->only(['index'])->names('clearance');
    Route::post('/clearance/aset/{clearanceAset}/approve', [HodClearanceController::class, 'approveAset'])->name('clearance.aset.approve');
    Route::post('/clearance/aset/{clearanceAset}/reject', [HodClearanceController::class, 'rejectAset'])->name('clearance.aset.reject');
    Route::get('riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');
});

Route::prefix('mis')->middleware('auth')->name('mis.')->group(function () {
    Route::resource('dashboard', DashboardController::class)->only(['index'])->names('dashboard');
    Route::resource('clearance', MisClearanceController::class)->only(['index'])->names('clearance');
    Route::post('/clearance/aset/{clearanceAset}/approve', [MisClearanceController::class, 'approveAset'])->name('clearance.aset.approve');
    Route::post('/clearance/aset/{clearanceAset}/reject', [MisClearanceController::class, 'rejectAset'])->name('clearance.aset.reject');
    Route::get('riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');
});

Route::prefix('karyawan')->middleware('auth')->name('karyawan.')->group(function () {
    Route::get('/dashboard', [KaryawanDashboardControllers::class, 'index'])->name('dashboard');

    Route::prefix('clearance')->name('clearance.')->group(function () {
        Route::get('/',         [ClearanceController::class, 'index'])->name('index');
        Route::post('/',        [ClearanceController::class, 'store'])->name('store');
        Route::get('/{id}',     [ClearanceController::class, 'show'])->name('show');
        Route::post('/{id}/revisi', [ClearanceController::class, 'revisi'])->name('revisi');
    });
});  