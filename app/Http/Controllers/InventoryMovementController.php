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
        $user = auth()->user();
        $isAdmin = $user->role && $user->role->slug === 'admin';
        
        // Get user's managed branches if manager
        $managedBranchIds = [];
        if (!$isAdmin && $user->role && $user->role->slug === 'manager') {
            $managedBranchIds = $user->managedBranches()->pluck('id')->toArray();
        }
        
        $movements = InventoryMovement::with(['branch', 'product', 'user'])
            ->when(!$isAdmin && count($managedBranchIds) > 0, fn($q) => $q->whereIn('branch_id', $managedBranchIds))
            ->paginate(10);
        
        $branches = Branch::select('id', 'name')
            ->when(!$isAdmin && count($managedBranchIds) > 0, fn($q) => $q->whereIn('id', $managedBranchIds))
            ->get();
            
        $products = Product::select('id', 'name', 'sku')->get();
        return Inertia::render('InventoryMovements/Index', [
            'movements' => $movements,
            'branches' => $branches,
            'products' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->role && $user->role->slug === 'admin';
        
        // Get user's managed branches if manager
        $managedBranchIds = [];
        if (!$isAdmin && $user->role && $user->role->slug === 'manager') {
            $managedBranchIds = $user->managedBranches()->pluck('id')->toArray();
        }
        
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:in,out,adjustment',
            'quantity_change' => 'required|integer',
            'notes' => 'nullable|string|max:500',
        ]);

        // Check if user can manage this branch
        if (!$isAdmin && !in_array($validated['branch_id'], $managedBranchIds)) {
            return back()->withErrors(['branch_id' => 'You can only manage movements for your assigned branch.']);
        }

        $validated['user_id'] = auth()->id();

        InventoryMovement::create($validated);

        return redirect()->route('inventory-movements.index')
            ->with('success', 'Inventory movement recorded successfully.');
    }

    public function update(Request $request, InventoryMovement $inventoryMovement)
    {
        $user = auth()->user();
        $isAdmin = $user->role && $user->role->slug === 'admin';
        
        // Get user's managed branches if manager
        $managedBranchIds = [];
        if (!$isAdmin && $user->role && $user->role->slug === 'manager') {
            $managedBranchIds = $user->managedBranches()->pluck('id')->toArray();
        }
        
        // Check if user can manage this branch
        if (!$isAdmin && !in_array($inventoryMovement->branch_id, $managedBranchIds)) {
            return back()->withErrors(['branch_id' => 'You can only manage movements for your assigned branch.']);
        }
        
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
        $user = auth()->user();
        $isAdmin = $user->role && $user->role->slug === 'admin';
        
        // Get user's managed branches if manager
        $managedBranchIds = [];
        if (!$isAdmin && $user->role && $user->role->slug === 'manager') {
            $managedBranchIds = $user->managedBranches()->pluck('id')->toArray();
        }
        
        // Check if user can manage this branch
        if (!$isAdmin && !in_array($inventoryMovement->branch_id, $managedBranchIds)) {
            return back()->withErrors(['branch_id' => 'You can only delete movements for your assigned branch.']);
        }
        
        $inventoryMovement->delete();

        return redirect()->route('inventory-movements.index')
            ->with('success', 'Inventory movement deleted successfully.');
    }
}
