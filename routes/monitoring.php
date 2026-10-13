<?php

use App\Http\Controllers\Eksekutif\MonitoringAplikasiController;
use Illuminate\Support\Facades\Route;

// Monitoring Daftar Aplikasi Pemerintah Provinsi Riau (Portal Eksekutif)
Route::middleware('auth')
    ->prefix('eksekutif')
    ->as('eksekutif.')
    ->group(function () {
        Route::resource('monitoring-aplikasi', MonitoringAplikasiController::class)->only(['create', 'store'])->middleware('permission:inventaris.create');
        Route::resource('monitoring-aplikasi', MonitoringAplikasiController::class)->only(['index', 'show'])->middleware('permission:inventaris.view');
        Route::resource('monitoring-aplikasi', MonitoringAplikasiController::class)->only(['edit', 'update'])->middleware('permission:inventaris.edit');
        Route::resource('monitoring-aplikasi', MonitoringAplikasiController::class)->only(['destroy'])->middleware('permission:inventaris.delete');
    });
