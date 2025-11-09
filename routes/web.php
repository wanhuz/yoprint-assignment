<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\HomeController;


Route::get('/', [HomeController::class, 'index'])->name('index');
Route::post('/upload', [UploadController::class, 'store'])->name('uploads.store');
Route::get('/refresh', [UploadController::class, 'refresh'])->name('uploads.refresh');
