<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WarehouseController extends Controller
{
    public function index(): View
    {
        $warehouses = Warehouse::withCount('products')->orderBy('name')->paginate(20);

        return view('warehouses.index', compact('warehouses'));
    }

    public function create(): View
    {
        return view('warehouses.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');

        Warehouse::create($data);

        return redirect()->route('warehouses.index')->with('status', 'Warehouse created.');
    }

    public function show(Warehouse $warehouse): View
    {
        $stock = $warehouse->products()->orderBy('name')->paginate(25);

        return view('warehouses.show', compact('warehouse', 'stock'));
    }

    public function edit(Warehouse $warehouse): View
    {
        return view('warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse): RedirectResponse
    {
        $data = $this->validated($request, $warehouse->id);
        $data['is_active'] = $request->boolean('is_active');

        $warehouse->update($data);

        return redirect()->route('warehouses.index')->with('status', 'Warehouse updated.');
    }

    public function destroy(Warehouse $warehouse): RedirectResponse
    {
        abort_unless(auth()->user()->hasRole('Admin'), 403);

        if ($warehouse->products()->exists()) {
            return back()->with('error', 'Cannot delete a warehouse that still holds stock.');
        }

        $warehouse->delete();

        return redirect()->route('warehouses.index')->with('status', 'Warehouse deleted.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:warehouses,code'.($ignoreId ? ",{$ignoreId}" : '')],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);
    }
}
