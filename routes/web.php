<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;

// Route untuk cetak semua daftar aset (Laporan Inventaris)
Route::get('/asset/cetak-pdf', [AssetController::class, 'cetakPdf'])->name('asset.cetakPdf');

// Route untuk cetak detail satu aset spesifik jika dibutuhkan
Route::get('/asset/{id}/cetak-detail', [AssetController::class, 'cetakDetailPdf'])->name('asset.cetakDetailPdf');
