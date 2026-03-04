<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Check if user is admin or manager
        $isAdmin = $user->role && $user->role->slug === 'admin';
        $isManager = $user->role && $user->role->slug === 'manager';
        
        // Get user's branch if manager
        $userBranchId = $isManager ? $user->branch_id : null;

        // Today's stats
        $today = now()->toDateString();
        $todaySale = Order::whereDate('created_at', $today)
            ->when($userBranchId, fn($q) => $q->where('branch_id', $userBranchId))
            ->sum('grand_total');

        // Monthly stats
        $startOfMonth = now()->startOfMonth()->toDateString();
        $monthlySale = Order::whereDate('created_at', '>=', $startOfMonth)
            ->when($userBranchId, fn($q) => $q->where('branch_id', $userBranchId))
            ->sum('grand_total');

        // Total orders
        $totalOrders = Order::when($userBranchId, fn($q) => $q->where('branch_id', $userBranchId))->count();

        // Low stock items (quantity < 10)
        $lowStockItems = Inventory::with(['branch', 'product'])
            ->where('quantity', '<', 10)
            ->when($userBranchId, fn($q) => $q->where('branch_id', $userBranchId))
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product' => [
                        'name' => $item->product->name ?? 'N/A',
                        'sku' => $item->product->sku ?? 'N/A',
                    ],
                    'branch' => [
                        'name' => $item->branch->name ?? 'N/A',
                    ],
                    'quantity' => $item->quantity,
                ];
            });

        // Sales Trend - Last 7 days with product details (line graph data)
        $salesTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $dayName = now()->subDays($i)->format('D');
            $dayDate = now()->subDays($i)->format('M d');
            
            // Get orders for this day with their items
            $orders = Order::whereDate('created_at', $date)
                ->when($userBranchId, fn($q) => $q->where('branch_id', $userBranchId))
                ->with(['items.product'])
                ->get();
            
            $totalSales = $orders->sum('grand_total');
            
            // Get product details for this day
            $products = [];
            foreach ($orders as $order) {
                foreach ($order->items as $item) {
                    $productName = $item->product->name ?? 'Unknown';
                    if (!isset($products[$productName])) {
                        $products[$productName] = 0;
                    }
                    $products[$productName] += $item->quantity;
                }
            }
            
            // Sort by quantity and get top products
            arsort($products);
            $topProducts = array_slice($products, 0, 3, true);
            
            $salesTrend[] = [
                'day' => $dayName,
                'date' => $dayDate,
                'total_sales' => floatval($totalSales),
                'products' => array_map(function($qty, $name) {
                    return [
                        'name' => $name,
                        'quantity' => $qty
                    ];
                }, $topProducts, array_keys($topProducts)),
            ];
        }

        // Branch details for admin
        $branchDetails = [];
        if ($isAdmin) {
            $branchDetails = Branch::withCount('orders')
                ->withSum('orders', 'grand_total')
                ->get()
                ->map(function ($branch) {
                    return [
                        'id' => $branch->id,
                        'name' => $branch->name,
                        'address' => $branch->address,
                        'is_active' => $branch->is_active,
                        'orders_count' => $branch->orders_count ?? 0,
                        'total_sales' => floatval($branch->orders_sum_grand_total ?? 0),
                    ];
                });
        }

        // Top 5 selling products
        $topSellingProducts = OrderItem::select('product_id')
            ->selectRaw('SUM(quantity) as total_sold')
            ->selectRaw('SUM(quantity * price) as total_revenue')
            ->with('product')
            ->when($userBranchId, fn($q) => $q->whereHas('order', function ($orderQuery) use ($userBranchId) {
                $orderQuery->where('branch_id', $userBranchId);
            }))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->product_id,
                    'name' => $item->product->name ?? 'N/A',
                    'sku' => $item->product->sku ?? 'N/A',
                    'total_sold' => intval($item->total_sold),
                    'total_revenue' => floatval($item->total_revenue),
                ];
            });

        // Branch info for manager
        $myBranch = null;
        if ($isManager && $userBranchId) {
            $myBranch = Branch::with(['orders'])
                ->where('id', $userBranchId)
                ->first();
        }

        // Total branches (for non-admin)
        $totalBranches = Branch::count();
        $activeBranches = Branch::where('is_active', true)->count();
        $totalProducts = Product::count();

        // Build stats array
        $stats = [
            'isAdmin' => $isAdmin,
            'isManager' => $isManager,
            'todaySale' => floatval($todaySale),
            'monthlySale' => floatval($monthlySale),
            'totalOrders' => $totalOrders,
            'lowStockItems' => $lowStockItems,
            'salesTrend' => $salesTrend,
            'branchDetails' => $branchDetails,
            'topSellingProducts' => $topSellingProducts,
            'myBranch' => $myBranch ? [
                'id' => $myBranch->id,
                'name' => $myBranch->name,
                'address' => $myBranch->address,
                'is_active' => $myBranch->is_active,
                'orders' => $myBranch->orders,
            ] : null,
            'totalBranches' => $totalBranches,
            'activeBranches' => $activeBranches,
            'totalProducts' => $totalProducts,
        ];

        return Inertia::render('Dashboard', [
            'user' => $user,
            'stats' => $stats,
        ]);
    }
}
