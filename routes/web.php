<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InventoryMovementController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Admin Routes
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('branches', BranchController::class)->names('branches');
    Route::resource('products', ProductController::class)->names('products');
    Route::resource('inventories', InventoryController::class)->names('inventories');
    Route::post('/inventories/{inventory}/add-stock', [InventoryController::class, 'addStock'])->name('inventories.addStock');
    Route::resource('inventory-movements', InventoryMovementController::class)->names('inventory-movements');
    // Route::resource('orders', OrderController::class)->names('orders');
    // Route::resource('order-items', OrderItemController::class)->names('order-items');
})->middleware(RoleMiddleware::class . ':admin');

// Manager Routes
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('inventories', InventoryController::class)->names('inventories');
    Route::post('/inventories/{inventory}/add-stock', [InventoryController::class, 'addStock'])->name('inventories.addStock');
    Route::resource('orders', OrderController::class)->names('orders');
    Route::resource('order-items', OrderItemController::class)->names('order-items');
})->middleware(RoleMiddleware::class . ':manager');

// Sales Routes
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('sales')->name('sales.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('orders', OrderController::class)->names('orders');
    Route::resource('order-items', OrderItemController::class)->names('order-items');
})->middleware(RoleMiddleware::class . ':sales');

// Fallback route for dashboard redirect based on role
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->role && $user->role->slug === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role && $user->role->slug === 'manager') {
            return redirect()->route('manager.dashboard');
        } elseif ($user->role && $user->role->slug === 'sales') {
            return redirect()->route('sales.dashboard');
        }
        return redirect()->route('admin.dashboard');
    });
});
