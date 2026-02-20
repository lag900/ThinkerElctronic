<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\LmsPackageController;
use App\Http\Controllers\OrderController;

Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('/shop', [ProductController::class, 'shop'])->name('shop');
Route::get('/lms-points', [LmsPackageController::class, 'frontend'])->name('lms-points');
Route::get('/privacy', [App\Http\Controllers\PageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [App\Http\Controllers\PageController::class, 'terms'])->name('terms');
Route::get('/refund-policy', [App\Http\Controllers\PageController::class, 'refundPolicy'])->name('refund');
Route::get('/contact', [App\Http\Controllers\PageController::class, 'contact'])->name('contact');
Route::get('/product/{product}', [ProductController::class, 'show'])->name('product.show');
Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
Route::post('/order', [OrderController::class, 'store'])->name('order.store');
Route::get('/thank-you/{id}', [OrderController::class, 'thankYou'])->name('thank.you');
Route::get('/order/invoice/{order_number}', [OrderController::class, 'downloadInvoice'])->name('order.invoice');

Route::get('/language/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('language.switch');

Route::get('/dashboard', function () {
    /** @var \App\Models\User $user */
    $user = Auth::user();
    if ($user->isAdmin() || str_ends_with($user->email, '@thinker.com')) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('profile.edit');
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

    // Admin Products
    Route::prefix('admin/products')->name('admin.products.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ProductController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\ProductController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\ProductController::class, 'store'])->name('store');
        Route::get('/{product}/edit', [\App\Http\Controllers\Admin\ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [\App\Http\Controllers\Admin\ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [\App\Http\Controllers\Admin\ProductController::class, 'destroy'])->name('destroy');
        Route::delete('/image/{imageId}', [\App\Http\Controllers\Admin\ProductController::class, 'deleteImage'])->name('image.delete');
    });

    // New Admin Dashboard Routes
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        
        // Content Management
        Route::prefix('content')->name('content.')->group(function () {
            Route::get('/pages/{slug}', [\App\Http\Controllers\Admin\CmsPageController::class, 'index'])->name('pages.index');
            Route::put('/pages/{page}', [\App\Http\Controllers\Admin\CmsPageController::class, 'update'])->name('pages.update');
        });

        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('store');
            Route::post('/{category}', [\App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('update');
            Route::delete('/{category}', [\App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('destroy');
        });
        Route::get('/inventory', fn() => Inertia::render('Admin/Inventory'))->name('inventory.index');
        Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('/customers', fn() => Inertia::render('Admin/Customers'))->name('customers.index');
        Route::get('/analytics', fn() => Inertia::render('Admin/Analytics'))->name('analytics');
        Route::get('/settings', fn() => Inertia::render('Admin/Settings'))->name('settings');

        // Enterprise User & Security Matrix
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->names('users');
        Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class)->names('roles');
        Route::get('/permissions', [\App\Http\Controllers\Admin\PermissionController::class, 'index'])->name('admin.permissions.index');
        Route::post('/permissions', [\App\Http\Controllers\Admin\PermissionController::class, 'store'])->name('admin.permissions.store');

        // Invoice Management
        Route::post('/orders/{order}/invoice', [\App\Http\Controllers\Admin\InvoiceController::class, 'generate'])->name('invoices.generate');
        Route::get('/invoices/download/{token}', [\App\Http\Controllers\Admin\InvoiceController::class, 'download'])->name('invoices.download');
        Route::post('/invoices/{invoice}/send-email', [\App\Http\Controllers\Admin\InvoiceController::class, 'sendEmail'])->name('invoices.send-email');
        Route::get('/orders/{id}/invoice/preview', [\App\Http\Controllers\Admin\InvoiceController::class, 'preview'])->name('invoices.preview');
    });


});

require __DIR__.'/auth.php';
