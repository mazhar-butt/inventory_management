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
        $query = OrderItem::with(['order', 'product']);

        if ($request->order_id) {
            $query->where('order_id', $request->order_id);
        }

        $items = $query->paginate(10);
        $orders = Order::with('branch')->select('id', 'branch_id')->get();
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
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        OrderItem::create($validated);

        return redirect()->route('order-items.index')
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

        return redirect()->route('order-items.index')
            ->with('success', 'Order item updated successfully.');
    }

    public function destroy(OrderItem $orderItem)
    {
        $orderItem->delete();

        return redirect()->route('order-items.index')
            ->with('success', 'Order item deleted successfully.');
    }
}
