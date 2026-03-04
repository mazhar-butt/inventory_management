<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InventoryMovementController extends Controller
{
    public function index()
    {
        $movements = InventoryMovement::with(['branch', 'product', 'user'])->paginate(10);
        $branches = Branch::select('id', 'name')->get();
        $products = Product::select('id', 'name', 'sku')->get();
        return Inertia::render('InventoryMovements/Index', [
            'movements' => $movements,
            'branches' => $branches,
            'products' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:in,out,adjustment',
            'quantity_change' => 'required|integer',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['user_id'] = auth()->id();

        InventoryMovement::create($validated);

        return redirect()->route('inventory-movements.index')
            ->with('success', 'Inventory movement recorded successfully.');
    }

    public function update(Request $request, InventoryMovement $inventoryMovement)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:in,out,adjustment',
            'quantity_change' => 'required|integer',
            'notes' => 'nullable|string|max:500',
        ]);

        $inventoryMovement->update($validated);

        return redirect()->route('inventory-movements.index')
            ->with('success', 'Inventory movement updated successfully.');
    }

    public function destroy(InventoryMovement $inventoryMovement)
    {
        $inventoryMovement->delete();

        return redirect()->route('inventory-movements.index')
            ->with('success', 'Inventory movement deleted successfully.');
    }
}
