<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['branch', 'user', 'items.product'])->paginate(10);
        $branches = Branch::select('id', 'name')->get();
        
        // Get products with their quantities for each branch
        $branchInventories = [];
        $allInventories = Inventory::with('product')->get();
        
        foreach ($allInventories as $inventory) {
            if (!isset($branchInventories[$inventory->branch_id])) {
                $branchInventories[$inventory->branch_id] = [];
            }
            $branchInventories[$inventory->branch_id][] = [
                'id' => $inventory->product_id,
                'product_id' => $inventory->product_id,
                'name' => $inventory->product->name,
                'sku' => $inventory->product->sku,
                'price' => $inventory->product->price,
                'tax' => $inventory->product->tax,
                'quantity' => $inventory->quantity,
            ];
        }

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
            'branches' => $branches,
            'branchInventories' => $branchInventories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'status' => 'required|in:pending,processing,completed,cancelled',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Validate quantity doesn't exceed available stock
        foreach ($validated['items'] as $item) {
            $inventory = Inventory::where('branch_id', $validated['branch_id'])
                ->where('product_id', $item['product_id'])
                ->first();
            
            if (!$inventory || $inventory->quantity < $item['quantity']) {
                $product = Product::find($item['product_id']);
                return back()->withErrors([
                    'items' => "Insufficient stock for product: " . $product->name . ". Available: " . ($inventory ? $inventory->quantity : 0)
                ]);
            }
        }

        // Calculate totals from items
        $totalAmount = 0;
        $itemsData = [];

        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            $subtotal = $product->price * $item['quantity'];
            $taxAmount = $subtotal * ($product->tax / 100);
            $totalAmount += $subtotal + $taxAmount;

            $itemsData[] = [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $product->price,
            ];
        }

        $taxAmount = 0;
        $taxPercentage = 0;
        $grandTotal = $totalAmount;

        $order = Order::create([
            'branch_id' => $validated['branch_id'],
            'user_id' => auth()->id(), // Use currently logged in user
            'total_amount' => $totalAmount - $taxAmount,
            'tax_amount' => $taxAmount,
            'tax_percentage' => $taxPercentage,
            'grand_total' => $grandTotal,
            'status' => $validated['status'] ?? 'completed', // Default to completed
        ]);

        // Create order items and decrease inventory
        foreach ($itemsData as $itemData) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $itemData['product_id'],
                'quantity' => $itemData['quantity'],
                'price' => $itemData['price'],
            ]);
            
            // Decrease inventory quantity
            $inventory = Inventory::where('branch_id', $validated['branch_id'])
                ->where('product_id', $itemData['product_id'])
                ->first();
            if ($inventory) {
                $inventory->decrement('quantity', $itemData['quantity']);
                
                // Create inventory movement for the order
                InventoryMovement::create([
                    'branch_id' => $validated['branch_id'],
                    'product_id' => $itemData['product_id'],
                    'user_id' => auth()->id(),
                    'type' => 'reduction',
                    'quantity_change' => $itemData['quantity'],
                    'reference_id' => $order->id,
                    'reference_type' => Order::class,
                    'notes' => 'Stock reduced due to order #' . $order->id,
                ]);
            }
        }

        return redirect()->route('orders.index')
            ->with('success', 'Order created successfully.');
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'status' => 'required|in:pending,processing,completed,cancelled',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Validate quantity doesn't exceed available stock
        foreach ($validated['items'] as $item) {
            $inventory = Inventory::where('branch_id', $validated['branch_id'])
                ->where('product_id', $item['product_id'])
                ->first();
            
            if (!$inventory || $inventory->quantity < $item['quantity']) {
                $product = Product::find($item['product_id']);
                return back()->withErrors([
                    'items' => "Insufficient stock for product: " . $product->name . ". Available: " . ($inventory ? $inventory->quantity : 0)
                ]);
            }
        }

        // Calculate totals from items
        $totalAmount = 0;
        $itemsData = [];

        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            $subtotal = $product->price * $item['quantity'];
            $taxAmount = $subtotal * ($product->tax / 100);
            $totalAmount += $subtotal + $taxAmount;

            $itemsData[] = [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $product->price,
            ];
        }

        $taxAmount = 0;
        $taxPercentage = 0;
        $grandTotal = $totalAmount;

        // Store old items for inventory adjustment
        $oldItems = $order->items->keyBy('product_id');

        $order->update([
            'branch_id' => $validated['branch_id'],
            'user_id' => auth()->id(),
            'total_amount' => $totalAmount - $taxAmount,
            'tax_amount' => $taxAmount,
            'tax_percentage' => $taxPercentage,
            'grand_total' => $grandTotal,
            'status' => $validated['status'] ?? 'completed',
        ]);

        // Delete old items and create new ones
        $order->items()->delete();
        foreach ($itemsData as $itemData) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $itemData['product_id'],
                'quantity' => $itemData['quantity'],
                'price' => $itemData['price'],
            ]);
            
            // Adjust inventory based on difference
            $inventory = Inventory::where('branch_id', $validated['branch_id'])
                ->where('product_id', $itemData['product_id'])
                ->first();
            
            if ($inventory) {
                $oldQuantity = isset($oldItems[$itemData['product_id']]) ? $oldItems[$itemData['product_id']]->quantity : 0;
                $quantityDiff = $itemData['quantity'] - $oldQuantity;
                
                if ($quantityDiff != 0) {
                    $inventory->decrement('quantity', $quantityDiff);
                    
                    // Create inventory movement for the change
                    InventoryMovement::create([
                        'branch_id' => $validated['branch_id'],
                        'product_id' => $itemData['product_id'],
                        'user_id' => auth()->id(),
                        'type' => $quantityDiff > 0 ? 'reduction' : 'addition',
                        'quantity_change' => abs($quantityDiff),
                        'reference_id' => $order->id,
                        'reference_type' => Order::class,
                        'notes' => 'Stock adjusted due to order #' . $order->id . ' update',
                    ]);
                }
            }
        }

        return redirect()->route('orders.index')
            ->with('success', 'Order updated successfully.');
    }

    public function destroy(Order $order)
    {
        // Restore inventory before deletion
        foreach ($order->items as $item) {
            $inventory = Inventory::where('branch_id', $order->branch_id)
                ->where('product_id', $item->product_id)
                ->first();
            
            if ($inventory) {
                $inventory->increment('quantity', $item->quantity);
                
                // Create inventory movement for the restoration
                InventoryMovement::create([
                    'branch_id' => $order->branch_id,
                    'product_id' => $item->product_id,
                    'user_id' => auth()->id(),
                    'type' => 'addition',
                    'quantity_change' => $item->quantity,
                    'reference_id' => $order->id,
                    'reference_type' => Order::class,
                    'notes' => 'Stock restored due to order #' . $order->id . ' deletion',
                ]);
            }
        }
        
        $order->delete();

        return redirect()->route('orders.index')
            ->with('success', 'Order deleted successfully.');
    }
}
