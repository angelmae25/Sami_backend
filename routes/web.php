
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;

// Redirect homepage to administrative login
Route::redirect('/', '/admin/login');

// Guest routes
Route::middleware('guest')->group(function () {

    Route::get('/admin/login', [AuthController::class, 'showLogin'])
        ->name('admin.login');

    Route::post('/admin/login', [AuthController::class, 'login'])
        ->name('admin.login.submit');

});

// Authenticated administrative routes
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AuthController::class, 'dashboard'])
        ->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

});