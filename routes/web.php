<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CuttingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmbroideryController;
use App\Http\Controllers\FactoryController;
use App\Http\Controllers\FinishingController;
use App\Http\Controllers\GarmentTypeController;
use App\Http\Controllers\LineController;
use App\Http\Controllers\Notification\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WashController;
use Illuminate\Support\Facades\Route;

// Login route
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

// Show form to request reset link
Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');

// Send reset link
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

// Show reset form with token
Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');

// Handle new password submission
Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// Handle login POST request
Route::post('/login', [LoginController::class, 'login'])->name('auth.login.submit');

// Authentication routes
Route::middleware(['auth'])->group(function () {

    // Dashboard route
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/', 'index')->name('dashboard.index');
        Route::get('/logout', 'logout')->name('auth.logout');
    });

    // Role routes
    Route::controller(RoleController::class)->group(function () {
        Route::get('/roles', 'index')->name('roles.index');
        Route::get('/roles/create', 'create')->name('roles.create');
        Route::post('/roles', 'store')->name('roles.store');
        Route::get('/roles/{role}/edit', 'edit')->name('roles.edit');
        Route::put('/roles/{role}', 'update')->name('roles.update');
        Route::delete('/roles/{role}', 'destroy')->name('roles.destroy');
    });

    // User routes
    Route::controller(UserController::class)->group(function () {
        Route::get('/users', 'index')->name('users.index');
        Route::get('/users/create', 'create')->name('users.create');
        Route::post('/users', 'store')->name('users.store');
        Route::get('/users/{user}/edit', 'edit')->name('users.edit');
        Route::put('/users/{user}', 'update')->name('users.update');
        Route::delete('/users/{user}', 'destroy')->name('users.destroy');
    });

    // Garments types routes
    Route::controller(GarmentTypeController::class)->group(function () {
        Route::get('/garment-types', 'index')->name('garment_types.index');
        Route::get('/garment-types/create', 'create')->name('garment_types.create');
        Route::post('/garment-types', 'store')->name('garment_types.store');
        Route::post('/garment-types/update-status', 'updateStatus')->name('garment_types.updateStatus');
        Route::get('/garment-types/{garment_type}/edit', 'edit')->name('garment_types.edit');
        Route::put('/garment-types/{garment_type}', 'update')->name('garment_types.update');
        Route::delete('/garment-types/{garment_type}', 'destroy')->name('garment_types.destroy');
    });

    // Order routes
    Route::controller(OrderController::class)->group(function () {
        Route::get('/orders', 'index')->name('orders.index');
        Route::get('/orders/create', 'create')->name('orders.create');
        Route::post('/orders', 'store')->name('orders.store');
        Route::get('/orders/{order}/show', 'show')->name('orders.show');
        Route::get('/orders/{order}/edit', 'edit')->name('orders.edit');
        Route::put('/orders/{order}', 'update')->name('orders.update');
        Route::delete('/orders/{order}', 'destroy')->name('orders.destroy');
        Route::get('/orders/{order}/export-excel', 'export')->name('orders.export');
        Route::get('orders/{order}/export-pdf', 'exportPdf')->name('orders.exportPdf');
    });

    // Cutting routes
    Route::controller(CuttingController::class)->group(function () {
        Route::get('/cutting-report', 'index')->name('cuttings.index');
        Route::get('/cutting-report/create', 'create')->name('cuttings.create');
        Route::post('/cutting-report', 'store')->name('cuttings.store');
        Route::get('/cutting-report/{cutting}/show', 'show')->name('cuttings.show');
        Route::get('/cutting-report/{cutting}/edit', 'edit')->name('cuttings.edit');
        Route::put('/cutting-report/{cutting}', 'update')->name('cuttings.update');
        Route::delete('/cutting-report/{cutting}', 'destroy')->name('cuttings.destroy');
        Route::get('/cutting-report/{cutting}/export-excel', 'exportExcel')->name('cuttings.exportExcel');
        Route::get('/cutting-report/{cutting}/export-pdf', 'exportPdf')->name('cuttings.exportPdf');
    });

    // Embroidery routes
    Route::controller(EmbroideryController::class)->group(function () {
        Route::get('/embroideries', 'index')->name('embroideries.index');
        Route::get('/embroideries/create', 'create')->name('embroideries.create');
        Route::post('/embroideries', 'store')->name('embroideries.store');
        Route::get('/embroideries/{embroidery}/show', 'show')->name('embroideries.show');
        Route::get('/embroideries/{embroidery}/edit', 'edit')->name('embroideries.edit');
        Route::put('/embroideries/{embroidery}', 'update')->name('embroideries.update');
        Route::delete('/embroideries/{embroidery}', 'destroy')->name('embroideries.destroy');
        Route::get('/embroideries/filter', 'filter')->name('embroideries.filter');
        Route::get('/embroideries/{embroidery}/export-excel', 'exportExcel')->name('embroideries.exportExcel');
        Route::get('/embroideries/{embroidery}/export-pdf', 'exportPdf')->name('embroideries.exportPdf');
    });

    // Print routes
    Route::controller(PrintController::class)->group(function () {
        Route::get('/prints', 'index')->name('prints.index');
        Route::get('/prints/create', 'create')->name('prints.create');
        Route::post('/prints', 'store')->name('prints.store');
        Route::get('/prints/{print}/show', 'show')->name('prints.show');
        Route::get('/prints/{print}/edit', 'edit')->name('prints.edit');
        Route::put('/prints/{print}', 'update')->name('prints.update');
        Route::delete('/prints/{print}', 'destroy')->name('prints.destroy');
        Route::get('/prints/filter', 'filter')->name('prints.filter');
        Route::get('/prints/{print}/export-excel', 'exportExcel')->name('prints.exportExcel');
        Route::get('/prints/{print}/export-pdf', 'exportPdf')->name('prints.exportPdf');
    });

    // Factory routes
    Route::controller(FactoryController::class)->group(function () {
        Route::get('/factories', 'index')->name('factories.index');
        Route::get('/factories/create', 'create')->name('factories.create');
        Route::post('/factories', 'store')->name('factories.store');
        Route::post('/factories/update-status', 'updateStatus')->name('factories.updateStatus');
        Route::get('/factories/{factory}/edit', 'edit')->name('factories.edit');
        Route::put('/factories/{factory}', 'update')->name('factories.update');
        Route::delete('/factories/{factory}', 'destroy')->name('factories.destroy');
    });

    // Line routes
    Route::controller(LineController::class)->group(function () {
        Route::get('/lines', 'index')->name('lines.index');
        Route::get('/lines/create', 'create')->name('lines.create');
        Route::post('/lines', 'store')->name('lines.store');
        Route::post('/lines/update-status', 'updateStatus')->name('lines.updateStatus');
        Route::get('/lines/{line}/edit', 'edit')->name('lines.edit');
        Route::put('/lines/{line}', 'update')->name('lines.update');
        Route::delete('/lines/{line}', 'destroy')->name('lines.destroy');
    });

    // Production routes
    Route::controller(ProductionController::class)->group(function () {
        Route::get('/productions', 'index')->name('productions.index');
        Route::get('/productions/create', 'create')->name('productions.create');
        Route::post('/productions', 'store')->name('productions.store');
        Route::get('/productions/{production}/show', 'show')->name('productions.show');
        Route::get('/productions/{production}/edit', 'edit')->name('productions.edit');
        Route::put('/productions/{production}', 'update')->name('productions.update');
        Route::delete('/productions/{production}', 'destroy')->name('productions.destroy');
        Route::get('/productions/{production}/export-excel', 'exportExcel')->name('productions.exportExcel');
        Route::get('/productions/{production}/export-pdf', 'exportPdf')->name('productions.exportPdf');
    });

    // Wash routes
    Route::controller(WashController::class)->group(function () {
        Route::get('/washes', 'index')->name('washes.index');
        Route::get('/washes/create', 'create')->name('washes.create');
        Route::post('/washes', 'store')->name('washes.store');
        Route::get('/washes/{wash}/show', 'show')->name('washes.show');
        Route::get('/washes/{wash}/edit', 'edit')->name('washes.edit');
        Route::put('/washes/{wash}', 'update')->name('washes.update');
        Route::delete('/washes/{wash}', 'destroy')->name('washes.destroy');
        Route::get('/washes/{wash}/export', 'export')->name('washes.export');
    });

    // Production routes
    Route::controller(FinishingController::class)->group(function () {
        Route::get('/finishing', 'index')->name('finishing.index');
        Route::get('/finishing/create', 'create')->name('finishing.create');
        Route::post('/finishing', 'store')->name('finishing.store');
        Route::get('/finishing/{finishing}/show', 'show')->name('finishing.show');
        Route::get('/finishing/{finishing}/edit', 'edit')->name('finishing.edit');
        Route::put('/finishing/{finishing}', 'update')->name('finishing.update');
        Route::delete('/finishing/{finishing}', 'destroy')->name('finishing.destroy');
        Route::get('/finishing/{finishing}/export', 'export')->name('finishing.export');
    });

    // Notification routes
    Route::controller(NotificationController::class)->group(function () {
        Route::post('/notifications/{id}/read', 'markAsRead')->name('notifications.read');
        Route::post('/notifications/read-all', 'markAllAsRead')->name('notifications.readAll');
        Route::get('/notifications', 'index')->name('notifications.index');
    });
});
