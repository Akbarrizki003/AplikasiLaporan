<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DokumenController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('login', 'App\Http\Controllers\Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'App\Http\Controllers\Auth\LoginController@login');
Route::post('logout', 'App\Http\Controllers\Auth\LoginController@logout')->name('logout');

// Registration Routes
Route::get('register', 'App\Http\Controllers\Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'App\Http\Controllers\Auth\RegisterController@register');

// Password Reset Routes
Route::get('password/reset', 'App\Http\Controllers\Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'App\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'App\Http\Controllers\Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'App\Http\Controllers\Auth\ResetPasswordController@reset')->name('password.update');

// Email Verification Routes
Route::get('email/verify', 'App\Http\Controllers\Auth\VerificationController@show')->name('verification.notice');
Route::get('email/verify/{id}/{hash}', 'App\Http\Controllers\Auth\VerificationController@verify')->name('verification.verify');
Route::post('email/resend', 'App\Http\Controllers\Auth\VerificationController@resend')->name('verification.resend');

// Protected routes
Route::middleware(['auth'])->group(function () {
     // Dokumen resource routes
     Route::resource('dokumen', DokumenController::class);
     
     // View and download dokumen
     Route::get('dokumen/{id}/view', [DokumenController::class, 'viewDokumen'])->name('dokumen.view');
     Route::get('dokumen/{id}/download', [DokumenController::class, 'download'])->name('dokumen.download');
     
     // Keuangan actions
     Route::middleware(['role:keuangan'])->group(function () {
         Route::post('dokumen/{id}/terima-keuangan', [DokumenController::class, 'terimaKeuangan'])->name('dokumen.terima-keuangan');
         Route::post('dokumen/{id}/teruskan-ke-manajer', [DokumenController::class, 'teruskanKeManajer'])->name('dokumen.teruskan-ke-manajer');
     });
     
     // Manajer actions
     Route::middleware(['role:manajer'])->group(function () {
         Route::post('dokumen/{id}/setujui-manajer', [DokumenController::class, 'setujuiManajer'])->name('dokumen.setujui-manajer');
         Route::post('dokumen/{id}/tolak-manajer', [DokumenController::class, 'tolakManajer'])->name('dokumen.tolak-manajer');
         Route::post('dokumen/{id}/teruskan-ke-atasan', [DokumenController::class, 'teruskanKeAtasan'])->name('dokumen.teruskan-ke-atasan');
     });
     
     // Atasan actions
     Route::middleware(['role:atasan'])->group(function () {
         Route::post('dokumen/{id}/setujui-atasan', [DokumenController::class, 'setujuiAtasan'])->name('dokumen.setujui-atasan');
         Route::post('dokumen/{id}/tolak-atasan', [DokumenController::class, 'tolakAtasan'])->name('dokumen.tolak-atasan');
     });
 });
 
 Route::middleware(['auth'])->group(function () {
    // Routes untuk admin
    Route::middleware(['role:admin'])->group(function () {
        // CRUD Unit untuk admin
        Route::resource('units', App\Http\Controllers\UnitController::class);
    });
    
    // Routes untuk user dengan role unit
    Route::middleware(['role:unit'])->group(function () {
        // Profile Unit
        Route::get('/unit/profile', [App\Http\Controllers\UnitController::class, 'profile'])->name('unit.profile');
        Route::post('/unit/profile/update', [App\Http\Controllers\UnitController::class, 'updateProfile'])->name('unit.updateProfile');
    });
});
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
