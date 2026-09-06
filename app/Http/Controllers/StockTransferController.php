<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use App\Services\StockTransferService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class StockTransferController extends Controller
{
    public function __construct(protected StockTransferService $stockTransferService) {}

    public function index(): View
    {
        $stockTransfers = StockTransfer::with(['fromWarehouse', 'toWarehouse'])
            ->latest('transfer_date')
            ->paginate(20);

        return view('stock-transfers.index', compact('stockTransfers'));
    }

    public function create(): View
    {
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('stock-transfers.create', compact('warehouses', 'products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'from_warehouse_id' => ['required', 'exists:warehouses,id', 'different:to_warehouse_id'],
            'to_warehouse_id' => ['required', 'exists:warehouses,id'],
            'transfer_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $transfer = $this->stockTransferService->create(
                [
                    'from_warehouse_id' => $data['from_warehouse_id'],
                    'to_warehouse_id' => $data['to_warehouse_id'],
                    'transfer_date' => $data['transfer_date'],
                    'notes' => $data['notes'] ?? null,
                ],
                $data['items'],
                auth()->id(),
            );
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('stock-transfers.show', $transfer)->with('status', 'Stock transfer completed.');
    }

    public function show(StockTransfer $stockTransfer): View
    {
        $stockTransfer->load(['fromWarehouse', 'toWarehouse', 'items.product', 'user']);

        return view('stock-transfers.show', compact('stockTransfer'));
    }
}
