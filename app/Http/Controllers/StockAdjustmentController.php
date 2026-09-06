<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\Warehouse;
use App\Services\StockAdjustmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class StockAdjustmentController extends Controller
{
    public function __construct(protected StockAdjustmentService $stockAdjustmentService) {}

    public function index(): View
    {
        $stockAdjustments = StockAdjustment::with('warehouse')->latest('adjustment_date')->paginate(20);

        return view('stock-adjustments.index', compact('stockAdjustments'));
    }

    public function create(): View
    {
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('stock-adjustments.create', compact('warehouses', 'products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'reason' => ['required', 'string', 'max:255'],
            'adjustment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity_change' => ['required', 'integer', 'not_in:0'],
        ]);

        try {
            $adjustment = $this->stockAdjustmentService->create(
                [
                    'warehouse_id' => $data['warehouse_id'],
                    'reason' => $data['reason'],
                    'adjustment_date' => $data['adjustment_date'],
                    'notes' => $data['notes'] ?? null,
                ],
                $data['items'],
                auth()->id(),
            );
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('stock-adjustments.show', $adjustment)->with('status', 'Stock adjustment recorded.');
    }

    public function show(StockAdjustment $stockAdjustment): View
    {
        $stockAdjustment->load(['warehouse', 'items.product', 'user']);

        return view('stock-adjustments.show', compact('stockAdjustment'));
    }
}
