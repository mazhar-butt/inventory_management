<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\InventoryMovement;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Run RoleSeeder first
        $this->call([
            RoleSeeder::class,
        ]);

        // Get the sales role for default users
        $salesRole = Role::where('slug', 'sales')->first();
        $adminRole = Role::where('slug', 'admin')->first();
        $managerRole = Role::where('slug', 'manager')->first();

        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
        ]);

        // Create manager user
        $manager = User::create([
            'name' => 'John Manager',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'role_id' => $managerRole->id,
        ]);

        // Create branches
        // Create demo user (sales role)
        $user = User::create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => Hash::make('password'),
            'role_id' => $salesRole->id,
        ]);

        // Create branches
        $branch1 = Branch::create([
            'name' => 'Main Branch',
            'address' => '123 Main Street, New York, NY 10001',
            'is_active' => true,
            'created_by' => $admin->id,
            'manager_id' => $manager->id,
        ]);

        $branch2 = Branch::create([
            'name' => 'Secondary Branch',
            'address' => '456 Oak Avenue, Los Angeles, CA 90001',
            'is_active' => true,
            'created_by' => $admin->id,
            'manager_id' => $manager->id,
        ]);

        $branch3 = Branch::create([
            'name' => 'Inactive Branch',
            'address' => '789 Pine Road, Chicago, IL 60601',
            'is_active' => false,
            'created_by' => $user->id,
            'manager_id' => null,
        ]);

        // Create products
        $product1 = Product::create([
            'name' => 'Laptop Pro 15',
            'sku' => 'LAP-001',
            'price' => 1299.99,
            'tax' => 10,
            'status' => 'active',
        ]);

        $product2 = Product::create([
            'name' => 'Wireless Mouse',
            'sku' => 'MOU-001',
            'price' => 29.99,
            'tax' => 5,
            'status' => 'active',
        ]);

        $product3 = Product::create([
            'name' => 'USB-C Cable',
            'sku' => 'CAB-001',
            'price' => 15.99,
            'tax' => 5,
            'status' => 'active',
        ]);

        $product4 = Product::create([
            'name' => 'Monitor 27"',
            'sku' => 'MON-001',
            'price' => 399.99,
            'tax' => 8,
            'status' => 'active',
        ]);

        $product5 = Product::create([
            'name' => 'Keyboard Mechanical',
            'sku' => 'KEY-001',
            'price' => 149.99,
            'tax' => 5,
            'status' => 'inactive',
        ]);

        // Create inventories
        Inventory::create([
            'branch_id' => $branch1->id,
            'product_id' => $product1->id,
            'quantity' => 50,
        ]);

        Inventory::create([
            'branch_id' => $branch1->id,
            'product_id' => $product2->id,
            'quantity' => 200,
        ]);

        Inventory::create([
            'branch_id' => $branch2->id,
            'product_id' => $product1->id,
            'quantity' => 30,
        ]);

        Inventory::create([
            'branch_id' => $branch2->id,
            'product_id' => $product3->id,
            'quantity' => 500,
        ]);

        Inventory::create([
            'branch_id' => $branch1->id,
            'product_id' => $product4->id,
            'quantity' => 25,
        ]);

        // Create orders
        $order1 = Order::create([
            'branch_id' => $branch1->id,
            'user_id' => $user->id,
            'total_amount' => 1329.98,
            'tax_amount' => 132.99,
            'tax_percentage' => 10,
            'grand_total' => 1462.97,
            'status' => 'completed',
        ]);

        $order2 = Order::create([
            'branch_id' => $branch2->id,
            'user_id' => $user->id,
            'total_amount' => 45.98,
            'tax_amount' => 2.30,
            'tax_percentage' => 5,
            'grand_total' => 48.28,
            'status' => 'pending',
        ]);

        // Create order items
        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $product1->id,
            'quantity' => 1,
            'price' => 1299.99,
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $product2->id,
            'quantity' => 1,
            'price' => 29.99,
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $product2->id,
            'quantity' => 1,
            'price' => 29.99,
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $product3->id,
            'quantity' => 1,
            'price' => 15.99,
        ]);

        // Create inventory movements
        InventoryMovement::create([
            'branch_id' => $branch1->id,
            'product_id' => $product1->id,
            'user_id' => $user->id,
            'type' => 'in',
            'quantity_change' => 50,
            'notes' => 'Initial stock',
        ]);

        InventoryMovement::create([
            'branch_id' => $branch1->id,
            'product_id' => $product2->id,
            'user_id' => $user->id,
            'type' => 'in',
            'quantity_change' => 200,
            'notes' => 'Restocked',
        ]);

        InventoryMovement::create([
            'branch_id' => $branch1->id,
            'product_id' => $product1->id,
            'user_id' => $user->id,
            'type' => 'out',
            'quantity_change' => -1,
            'reference_id' => $order1->id,
            'reference_type' => 'App\\Models\\Order',
            'notes' => 'Order sale',
        ]);
    }
}
