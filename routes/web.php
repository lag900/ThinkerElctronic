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
Route::get('/live-search', [ProductController::class, 'liveSearch'])->name('live-search');
Route::get('/lms-points', [LmsPackageController::class, 'frontend'])->name('lms-points');
Route::get('/privacy', [App\Http\Controllers\PageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [App\Http\Controllers\PageController::class, 'terms'])->name('terms');
Route::get('/refund-policy', [App\Http\Controllers\PageController::class, 'refundPolicy'])->name('refund-policy');
Route::get('/contact', [App\Http\Controllers\PageController::class, 'contact'])->name('contact');
Route::get('/product/{product}', [ProductController::class, 'show'])->name('product.show');
Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
Route::post('/order', [OrderController::class, 'store'])->name('order.store');
Route::get('/thank-you/{id}', [OrderController::class, 'thankYou'])->name('thank.you');
Route::get('/order/invoice/{order_number}', [OrderController::class, 'downloadInvoice'])->name('order.invoice');

// QR System Public Routes
Route::get('/qr', [\App\Http\Controllers\QrController::class, 'index'])->name('qr.index');
Route::get('/qr/link/{link}', [\App\Http\Controllers\QrController::class, 'click'])->name('qr.click');

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
    return redirect('/');
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
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard')->middleware('admin:admin,super_admin');
        
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
        Route::get('/orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{order}/invoice', [\App\Http\Controllers\Admin\OrderController::class, 'printInvoice'])->name('orders.invoice');
        Route::put('/orders/{order}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.status');
        Route::put('/orders/{order}/items/{item}', [\App\Http\Controllers\Admin\OrderController::class, 'updateItem'])->name('orders.updateItem');
        Route::resource('customers', \App\Http\Controllers\Admin\CustomerController::class);
        Route::resource('invoices', \App\Http\Controllers\Admin\InvoiceController::class)->except(['index']);
        Route::get('/analytics', [\App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics');
        Route::get('/settings', fn() => Inertia::render('Admin/Settings'))->name('settings');
        Route::get('/sales-cash-control', [\App\Http\Controllers\Admin\SalesCashControlController::class, 'index'])->name('sales-cash-control');

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

        // QR Management
        Route::prefix('qr')->name('qr.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\QrController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\QrController::class, 'store'])->name('store');
            Route::put('/{qr}', [\App\Http\Controllers\Admin\QrController::class, 'update'])->name('update');
            Route::delete('/{qr}', [\App\Http\Controllers\Admin\QrController::class, 'destroy'])->name('destroy');
            Route::post('/logo', [\App\Http\Controllers\Admin\QrController::class, 'updateLogo'])->name('logo.update');
        });

        // Home Content Management
        Route::get('/home-content', [\App\Http\Controllers\Admin\HomeContentController::class, 'index'])->name('home-content.index');
        Route::post('/home-content', [\App\Http\Controllers\Admin\HomeContentController::class, 'update'])->name('home-content.update');

        // POS System
        Route::get('/pos', [\App\Http\Controllers\Admin\PosController::class, 'index'])->name('pos.index');
        Route::post('/pos/invoice', [\App\Http\Controllers\Admin\PosController::class, 'generateInvoice'])->name('pos.invoice');
        Route::get('/pos/search', [\App\Http\Controllers\Admin\PosController::class, 'search'])->name('pos.search');
        Route::get('/pos/customers/search', [\App\Http\Controllers\Admin\PosController::class, 'searchCustomers'])->name('pos.customers.search');
        Route::post('/pos/customers', [\App\Http\Controllers\Admin\PosController::class, 'storeCustomer'])->name('pos.customers.store');
        Route::get('/pos/customers/{id}/stats', [\App\Http\Controllers\Admin\PosController::class, 'customerStats'])->name('pos.customers.stats');
        Route::get('/pos/customers/{id}/history', [\App\Http\Controllers\Admin\PosController::class, 'customerHistory'])->name('pos.customers.history');

        // Expense Management
        Route::resource('expenses', \App\Http\Controllers\Admin\ExpenseController::class);

        // Professional Inventory & Purchase FIFO System
        Route::get('/purchases', [\App\Http\Controllers\Admin\PurchaseController::class, 'index'])->name('purchases.index');
        Route::post('/purchases', [\App\Http\Controllers\Admin\PurchaseController::class, 'store'])->name('purchases.store');
        
        Route::get('/inventory/reports/profit', [\App\Http\Controllers\Admin\InventoryReportController::class, 'profit'])->name('inventory.reports.profit');
        Route::get('/inventory/reports/valuation', [\App\Http\Controllers\Admin\InventoryReportController::class, 'valuation'])->name('inventory.reports.valuation');
        Route::get('/inventory/dashboard', [\App\Http\Controllers\Admin\InventoryReportController::class, 'dashboard'])->name('inventory.dashboard');
    });


});

require __DIR__.'/auth.php';
