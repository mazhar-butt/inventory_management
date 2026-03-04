<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InventoryMovementController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\ProductController;
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

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Branches
    Route::resource('branches', BranchController::class)->names('branches');

    // Products
    Route::resource('products', ProductController::class)->names('products');

    // Inventories
    Route::resource('inventories', InventoryController::class)->names('inventories');
    Route::post('/inventories/{inventory}/add-stock', [InventoryController::class, 'addStock'])->name('inventories.addStock');

    // Orders
    Route::resource('orders', OrderController::class)->names('orders');

    // Order Items
    Route::resource('order-items', OrderItemController::class)->names('order-items');

    // Inventory Movements
    Route::resource('inventory-movements', InventoryMovementController::class)->names('inventory-movements');
});
