<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentVoucherController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('units', UnitController::class)->except(['show']);
    Route::resource('warehouses', WarehouseController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('products', ProductController::class);

    Route::resource('purchase-orders', PurchaseOrderController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('purchase-orders/{purchase_order}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');
    Route::post('purchase-orders/{purchase_order}/cancel', [PurchaseOrderController::class, 'cancel'])->name('purchase-orders.cancel');

    Route::resource('sales-orders', SalesOrderController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('sales-orders/{sales_order}/fulfill', [SalesOrderController::class, 'fulfill'])->name('sales-orders.fulfill');
    Route::post('sales-orders/{sales_order}/cancel', [SalesOrderController::class, 'cancel'])->name('sales-orders.cancel');

    Route::resource('stock-transfers', StockTransferController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('stock-adjustments', StockAdjustmentController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('stock-movements', [StockMovementController::class, 'index'])->name('stock-movements.index');

    Route::resource('invoices', InvoiceController::class)->only(['index', 'show']);
    Route::post('invoices/{invoice}/pay', [InvoiceController::class, 'pay'])->name('invoices.pay');

    Route::resource('receipts', ReceiptController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('receipts/{receipt}/allocate', [ReceiptController::class, 'allocate'])->name('receipts.allocate');

    Route::resource('payment-vouchers', PaymentVoucherController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('payment-vouchers/{payment_voucher}/allocate', [PaymentVoucherController::class, 'allocate'])->name('payment-vouchers.allocate');

    Route::middleware('role:Admin')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::patch('users/{user}/role', [UserController::class, 'updateRole'])->name('users.update-role');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
