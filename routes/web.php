<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ThemeController;
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
    Route::post('/theme/update', [ThemeController::class, 'update'])->name('theme.update');
    Route::get('/theme/current', [ThemeController::class, 'current'])->name('theme.current');
    
    // Advanced Search API routes
    Route::get('/api/search/live', [SearchController::class, 'liveSearch'])->name('api.search.live');
    Route::get('/api/search/suggestions', [SearchController::class, 'suggestions'])->name('api.search.suggestions');
    Route::get('/api/companies/filter', [SearchController::class, 'filterCompanies'])->name('api.companies.filter');
    Route::post('/api/bulk-operation', [SearchController::class, 'bulkOperation'])->name('api.bulk.operation');
});
