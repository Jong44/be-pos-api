<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Buat semua permission
        $permissions = [
            // User Management
            "view users", "create users", "update users", "delete users", "assign roles",
            "view roles", "create roles", "update roles", "delete roles",

            // Produk & Kategori
            "view products", "create products", "update products", "delete products",
            "view categories", "create categories", "update categories", "delete categories",

            // Transaksi
            "create transaction", "view transactions", "view transaction detail", "delete transactions",

            // Open Bill
            "create open bill", "view open bills", "delete open bills",

            // Voucher
            "view vouchers", "create vouchers", "update vouchers", "delete vouchers",

            // Stok
            "view stock", "update stock",

            // Laporan
            "view sales report", "view product report", "view cashier report", "export report pdf",

            // Absensi
            "checkin attendance", "checkout attendance", "view attendance",

            // Log Aktivitas
            "view activity logs"
        ];

        // guard nya pakai api

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(["name" => $permission]);
        }

        // Buat role superadmin dan beri semua permission
        $superAdminRole = Role::firstOrCreate(["name" => "superadmin"]);
        $superAdminRole->syncPermissions(Permission::all());

        // Buat user superadmin
        $user = User::firstOrCreate(
            ["email" => "superadmin@pos.test"],
            [
                "username" => "superadmin",
                "password" => Hash::make("superadmin123"),
                "outlet_id" => null,
                "remember_token" => Str::random(10)
            ]
        );

        $user->assignRole("superadmin");
    }
}
