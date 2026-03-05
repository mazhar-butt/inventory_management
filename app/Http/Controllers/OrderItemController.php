<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderItemController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->role && $user->role->slug === 'admin';
        
        // Get user's managed branches if manager or sales
        $managedBranchIds = [];
        if (!$isAdmin && $user->role && ($user->role->slug === 'manager' || $user->role->slug === 'sales')) {
            $managedBranchIds = $user->managedBranches()->pluck('id')->toArray();
        }
        
        $query = OrderItem::with(['order', 'product'])
            ->whereHas('order', function($q) use ($isAdmin, $managedBranchIds) {
                if (!$isAdmin && count($managedBranchIds) > 0) {
                    $q->whereIn('branch_id', $managedBranchIds);
                }
            });

        if ($request->order_id) {
            $query->where('order_id', $request->order_id);
        }

        $items = $query->paginate(10);
        
        $ordersQuery = Order::with('branch')->select('id', 'branch_id');
        if (!$isAdmin && count($managedBranchIds) > 0) {
            $ordersQuery = $ordersQuery->whereIn('branch_id', $managedBranchIds);
        }
        $orders = $ordersQuery->get();
        
        $products = Product::select('id', 'name', 'sku')->get();
        return Inertia::render('OrderItems/Index', [
            'items' => $items,
            'order_id' => $request->order_id,
            'orders' => $orders,
            'products' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->role && $user->role->slug === 'admin';
        
        // Get user's managed branches if manager or sales
        $managedBranchIds = [];
        if (!$isAdmin && $user->role && ($user->role->slug === 'manager' || $user->role->slug === 'sales')) {
            $managedBranchIds = $user->managedBranches()->pluck('id')->toArray();
        }
        
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        // Check if order belongs to user's branch
        if (!$isAdmin && count($managedBranchIds) > 0) {
            $order = Order::find($validated['order_id']);
            if (!in_array($order->branch_id, $managedBranchIds)) {
                return back()->withErrors(['order_id' => 'You can only add items to orders from your assigned branch.']);
            }
        }

        OrderItem::create($validated);

        $routePrefix = $isAdmin ? 'admin' : ($user->role && $user->role->slug === 'manager' ? 'manager' : 'sales');
        return redirect()->route($routePrefix . '.order-items.index')
            ->with('success', 'Order item created successfully.');
    }

    public function update(Request $request, OrderItem $orderItem)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        $orderItem->update($validated);

        $routePrefix = $isAdmin ? 'admin' : ($user->role && $user->role->slug === 'manager' ? 'manager' : 'sales');
        return redirect()->route($routePrefix . '.order-items.index')
            ->with('success', 'Order item updated successfully.');
    }

    public function destroy(OrderItem $orderItem)
    {
        $user = auth()->user();
        $isAdmin = $user->role && $user->role->slug === 'admin';
        
        // Get user's managed branches if manager or sales
        $managedBranchIds = [];
        if (!$isAdmin && $user->role && ($user->role->slug === 'manager' || $user->role->slug === 'sales')) {
            $managedBranchIds = $user->managedBranches()->pluck('id')->toArray();
        }
        
        // Check if order belongs to user's branch
        if (!$isAdmin && count($managedBranchIds) > 0) {
            $order = $orderItem->order;
            if (!in_array($order->branch_id, $managedBranchIds)) {
                return back()->withErrors(['error' => 'You can only delete items from orders in your assigned branch.']);
            }
        }
        
        $orderItem->delete();

        $routePrefix = $isAdmin ? 'admin' : ($user->role && $user->role->slug === 'manager' ? 'manager' : 'sales');
        return redirect()->route($routePrefix . '.order-items.index')
            ->with('success', 'Order item deleted successfully.');
    }
}
