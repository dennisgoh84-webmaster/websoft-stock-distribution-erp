<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\Warehouse;
use App\Services\SalesOrderService;
use App\Support\DocumentNumber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SalesOrderController extends Controller
{
    public function __construct(protected SalesOrderService $salesOrderService) {}

    public function index(Request $request): View
    {
        $salesOrders = SalesOrder::with(['customer', 'warehouse'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest('order_date')
            ->paginate(20)
            ->withQueryString();

        return view('sales-orders.index', compact('salesOrders'));
    }

    public function create(): View
    {
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('sales-orders.create', compact('customers', 'warehouses', 'products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'order_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $salesOrder = DB::transaction(function () use ($data) {
            $subtotal = collect($data['items'])->sum(fn ($item) => $item['quantity'] * $item['unit_price']);

            $salesOrder = SalesOrder::create([
                'so_number' => DocumentNumber::generate(SalesOrder::class, 'SO'),
                'customer_id' => $data['customer_id'],
                'warehouse_id' => $data['warehouse_id'],
                'user_id' => auth()->id(),
                'status' => SalesOrder::STATUS_CONFIRMED,
                'order_date' => $data['order_date'],
                'notes' => $data['notes'] ?? null,
                'subtotal' => $subtotal,
                'total' => $subtotal,
            ]);

            foreach ($data['items'] as $item) {
                $salesOrder->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            return $salesOrder;
        });

        return redirect()->route('sales-orders.show', $salesOrder)->with('status', 'Sales order created.');
    }

    public function show(SalesOrder $salesOrder): View
    {
        $salesOrder->load(['customer', 'warehouse', 'items.product', 'invoice']);

        return view('sales-orders.show', compact('salesOrder'));
    }

    public function fulfill(Request $request, SalesOrder $salesOrder): RedirectResponse
    {
        $data = $request->validate([
            'quantities' => ['required', 'array'],
            'quantities.*' => ['nullable', 'integer', 'min:0'],
        ]);

        try {
            $this->salesOrderService->fulfill($salesOrder, array_map('intval', $data['quantities']), auth()->id());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('sales-orders.show', $salesOrder)->with('status', 'Order fulfilled.');
    }

    public function cancel(SalesOrder $salesOrder): RedirectResponse
    {
        try {
            $this->salesOrderService->cancel($salesOrder);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('sales-orders.show', $salesOrder)->with('status', 'Sales order cancelled.');
    }
}
