<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\LmsPackageController;

Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('/shop', [ProductController::class, 'shop'])->name('shop');
Route::get('/privacy', fn () => Inertia::render('Privacy'))->name('privacy');
Route::get('/terms', fn () => Inertia::render('Terms'))->name('terms');
Route::get('/refund-policy', fn () => Inertia::render('RefundPolicy'))->name('refund-policy');
Route::get('/contact', fn () => Inertia::render('Contact'))->name('contact');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Batu LMS Points - Admin (auth user can add/edit packages)
    Route::get('/admin/lms-packages', [LmsPackageController::class, 'index'])->name('lms-packages.index');
    Route::post('/admin/lms-packages', [LmsPackageController::class, 'store'])->name('lms-packages.store');
    Route::put('/admin/lms-packages/{lmsPackage}', [LmsPackageController::class, 'update'])->name('lms-packages.update');
    Route::delete('/admin/lms-packages/{lmsPackage}', [LmsPackageController::class, 'destroy'])->name('lms-packages.destroy');
});

require __DIR__.'/auth.php';
