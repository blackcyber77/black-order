<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Customer\MenuController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\PaymentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\TowerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\SettingController;

/*
|--------------------------------------------------------------------------
| Customer Routes (No Auth Required)
|--------------------------------------------------------------------------
*/

Route::get('/', [MenuController::class, 'index'])->name('home');
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
Route::get('/menu/{menuItem}', [MenuController::class, 'show'])->name('menu.show');
Route::get('/location/clear', [MenuController::class, 'clearLocation'])->name('location.clear');

// Cart
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::delete('/remove/{id}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
    Route::get('/count', [CartController::class, 'count'])->name('count');

    // Protected Actions (Need Location)
    Route::middleware('location.check')->group(function () {
        Route::post('/add/{menuItem}', [CartController::class, 'add'])->name('add');
        Route::patch('/update/{id}', [CartController::class, 'update'])->name('update');
    });
});

// Orders
Route::get('/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');
Route::post('/checkout', [OrderController::class, 'store'])->name('orders.store');
Route::get('/orders/confirmation/{orderNumber}', [OrderController::class, 'confirmation'])->name('orders.confirmation');
Route::get('/orders/track', [OrderController::class, 'track'])->name('orders.track');

// Payment Gateway Routes (Public - for webhooks)
Route::prefix('payment')->name('payment.')->group(function () {
    Route::post('/callback', [PaymentController::class, 'callback'])->name('callback');
    Route::post('/notification', [PaymentController::class, 'notification'])->name('notification');
    Route::get('/{order}/status', [PaymentController::class, 'checkStatus'])->name('status');
});

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');



/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // POS
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::get('/pos/create', [PosController::class, 'create'])->name('pos.create');
    Route::post('/pos', [PosController::class, 'store'])->name('pos.store');
    Route::patch('/pos/{order}/pay', [PosController::class, 'markAsPaid'])->name('pos.pay');
    Route::patch('/pos/{order}/hold', [PosController::class, 'hold'])->name('pos.hold');
    Route::patch('/pos/{order}/recall', [PosController::class, 'recall'])->name('pos.recall');
    Route::patch('/pos/{order}/void', [PosController::class, 'void'])->name('pos.void');
    Route::get('/pos/{order}/receipt', [PosController::class, 'receipt'])->name('pos.receipt');
    
    // Categories
    Route::resource('categories', CategoryController::class);

    // Menu Management (POS + QR Availability)
    Route::resource('menus', MenuItemController::class)->except(['show']);
    Route::get('/menus-availability', [MenuItemController::class, 'availability'])->name('menus.availability');
    Route::patch('/menus/{menu}/availability', [MenuItemController::class, 'toggleAvailability'])->name('menus.toggle-availability');
    
    // Orders
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/report', [AdminOrderController::class, 'report'])->name('orders.report');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/verify', [AdminOrderController::class, 'verifyPayment'])->name('orders.verify');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    

    
    // Tables Management (Tower creation is disabled)
    Route::get('/towers', [TowerController::class, 'index'])->name('towers.index');
    Route::post('/tables', [TowerController::class, 'storeTableGlobal'])->name('tables.store');
    Route::delete('/tables/{table}', [TowerController::class, 'destroyTable'])->name('tables.destroy');
    Route::get('/tables/{table}/qr', [TowerController::class, 'generateQr'])->name('tables.qr');
    
    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});
