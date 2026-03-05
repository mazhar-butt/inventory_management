<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InventoryController extends Controller
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
        
        $inventories = Inventory::with(['branch', 'product'])
            ->when(!$isAdmin && count($managedBranchIds) > 0, fn($q) => $q->whereIn('branch_id', $managedBranchIds))
            ->paginate(10);
        
        $branches = Branch::select('id', 'name')
            ->when(!$isAdmin && count($managedBranchIds) > 0, fn($q) => $q->whereIn('id', $managedBranchIds))
            ->get();
            
        $products = Product::select('id', 'name', 'sku')->get();
        return Inertia::render('Inventories/Index', [
            'inventories' => $inventories,
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
            'quantity' => 'required|integer|min:0',
        ]);

        // Check if manager can manage this branch
        if (!$isAdmin && !in_array($validated['branch_id'], $managedBranchIds)) {
            return back()->withErrors(['branch_id' => 'You can only manage your own branch inventory.']);
        }

        $existing = Inventory::where('branch_id', $validated['branch_id'])
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($existing) {
            $oldQuantity = $existing->quantity;
            $existing->update(['quantity' => $existing->quantity + $validated['quantity']]);
            
            // Create inventory movement for stock addition
            InventoryMovement::create([
                'branch_id' => $validated['branch_id'],
                'product_id' => $validated['product_id'],
                'user_id' => auth()->id(),
                'type' => 'addition',
                'quantity_change' => $validated['quantity'],
                'reference_id' => $existing->id,
                'reference_type' => Inventory::class,
                'notes' => 'Stock added via inventory creation (existing item updated)',
            ]);
            
            $routePrefix = $isAdmin ? 'admin' : 'manager';
            return redirect()->route($routePrefix . '.inventories.index')
                ->with('success', 'Inventory updated successfully.');
        }

        $inventory = Inventory::create($validated);
        
        // Create inventory movement for new inventory
        InventoryMovement::create([
            'branch_id' => $validated['branch_id'],
            'product_id' => $validated['product_id'],
            'user_id' => auth()->id(),
            'type' => 'addition',
            'quantity_change' => $validated['quantity'],
            'reference_id' => $inventory->id,
            'reference_type' => Inventory::class,
            'notes' => 'Initial stock added via inventory creation',
        ]);

        $routePrefix = $isAdmin ? 'admin' : 'manager';
        return redirect()->route($routePrefix . '.inventories.index')
            ->with('success', 'Inventory created successfully.');
    }

    public function update(Request $request, Inventory $inventory)
    {
        $user = auth()->user();
        $isAdmin = $user->role && $user->role->slug === 'admin';
        
        // Get user's managed branches if manager
        $managedBranchIds = [];
        if (!$isAdmin && $user->role && $user->role->slug === 'manager') {
            $managedBranchIds = $user->managedBranches()->pluck('id')->toArray();
        }
        
        // Check if manager can manage this branch
        if (!$isAdmin && !in_array($inventory->branch_id, $managedBranchIds)) {
            return back()->withErrors(['branch_id' => 'You can only manage your own branch inventory.']);
        }
        
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0',
        ]);

        $oldQuantity = $inventory->quantity;
        $quantityChange = $validated['quantity'] - $oldQuantity;
        
        $inventory->update($validated);

        // Create inventory movement for the change
        if ($quantityChange != 0) {
            InventoryMovement::create([
                'branch_id' => $validated['branch_id'],
                'product_id' => $validated['product_id'],
                'user_id' => auth()->id(),
                'type' => $quantityChange > 0 ? 'addition' : 'reduction',
                'quantity_change' => abs($quantityChange),
                'reference_id' => $inventory->id,
                'reference_type' => Inventory::class,
                'notes' => 'Stock ' . ($quantityChange > 0 ? 'added' : 'removed') . ' via inventory update',
            ]);
        }

        $routePrefix = $isAdmin ? 'admin' : 'manager';
        return redirect()->route($routePrefix . '.inventories.index')
            ->with('success', 'Inventory updated successfully.');
    }

    public function destroy(Inventory $inventory)
    {
        $user = auth()->user();
        $isAdmin = $user->role && $user->role->slug === 'admin';
        
        // Get user's managed branches if manager
        $managedBranchIds = [];
        if (!$isAdmin && $user->role && $user->role->slug === 'manager') {
            $managedBranchIds = $user->managedBranches()->pluck('id')->toArray();
        }
        
        // Check if manager can manage this branch
        if (!$isAdmin && !in_array($inventory->branch_id, $managedBranchIds)) {
            return back()->withErrors(['branch_id' => 'You can only manage your own branch inventory.']);
        }
        
        // Create inventory movement before deletion
        InventoryMovement::create([
            'branch_id' => $inventory->branch_id,
            'product_id' => $inventory->product_id,
            'user_id' => auth()->id(),
            'type' => 'reduction',
            'quantity_change' => $inventory->quantity,
            'notes' => 'Inventory deleted',
        ]);
        
        $inventory->delete();

        $routePrefix = $isAdmin ? 'admin' : 'manager';
        return redirect()->route($routePrefix . '.inventories.index')
            ->with('success', 'Inventory deleted successfully.');
    }

    public function addStock(Request $request, Inventory $inventory)
    {
        $user = auth()->user();
        $isAdmin = $user->role && $user->role->slug === 'admin';
        
        // Get user's managed branches if manager
        $managedBranchIds = [];
        if (!$isAdmin && $user->role && $user->role->slug === 'manager') {
            $managedBranchIds = $user->managedBranches()->pluck('id')->toArray();
        }
        
        // Check if manager can manage this branch
        if (!$isAdmin && !in_array($inventory->branch_id, $managedBranchIds)) {
            return back()->withErrors(['branch_id' => 'You can only manage your own branch inventory.']);
        }
        
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Get the quantity to add
        $quantityToAdd = $validated['quantity'];
        
        // Update inventory quantity
        $inventory->quantity = $inventory->quantity + $quantityToAdd;
        $inventory->save();

        // Create inventory movement - quantity is always positive for addition type
        InventoryMovement::create([
            'branch_id' => $inventory->branch_id,
            'product_id' => $inventory->product_id,
            'user_id' => auth()->id(),
            'type' => 'addition',
            'quantity_change' => $quantityToAdd,
            'reference_id' => $inventory->id,
            'reference_type' => Inventory::class,
            'notes' => 'Stock added via add stock functionality',
        ]);

        $routePrefix = $isAdmin ? 'admin' : 'manager';
        return redirect()->route($routePrefix . '.inventories.index')
            ->with('success', 'Stock added successfully.');
    }
}
