<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Companies management
    Route::resource('companies', CompanyController::class);
    
    // Contacts management
    Route::resource('contacts', ContactController::class);
    
    // Theme management routes
    Route::post('/theme/update', [App\Http\Controllers\ThemeController::class, 'update'])->name('theme.update');
    Route::get('/theme/current', [App\Http\Controllers\ThemeController::class, 'current'])->name('theme.current');
});
