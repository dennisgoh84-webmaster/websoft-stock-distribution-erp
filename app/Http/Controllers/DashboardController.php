<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $lowStockProducts = Product::query()
            ->where('is_active', true)
            ->get()
            ->filter(fn (Product $product) => $product->isLowStock())
            ->sortBy(fn (Product $product) => $product->totalStock())
            ->take(10);

        $pendingPurchaseOrders = PurchaseOrder::whereIn('status', [
            PurchaseOrder::STATUS_ORDERED,
            PurchaseOrder::STATUS_PARTIALLY_RECEIVED,
        ])->count();

        $pendingSalesOrders = SalesOrder::whereIn('status', [
            SalesOrder::STATUS_CONFIRMED,
            SalesOrder::STATUS_PARTIALLY_FULFILLED,
        ])->count();

        $outstandingInvoices = Invoice::whereIn('status', [
            Invoice::STATUS_UNPAID,
            Invoice::STATUS_PARTIALLY_PAID,
        ])->get();

        $outstandingBalance = $outstandingInvoices->sum(fn (Invoice $invoice) => $invoice->balance());

        $revenueThisMonth = Invoice::where('type', Invoice::TYPE_SALES)
            ->whereMonth('invoice_date', now()->month)
            ->whereYear('invoice_date', now()->year)
            ->sum('total');

        $productCount = Product::count();
        $warehouseCount = DB::table('warehouses')->count();

        return view('dashboard', compact(
            'lowStockProducts',
            'pendingPurchaseOrders',
            'pendingSalesOrders',
            'outstandingInvoices',
            'outstandingBalance',
            'revenueThisMonth',
            'productCount',
            'warehouseCount',
        ));
    }
}
