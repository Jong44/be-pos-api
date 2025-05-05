<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            SuperAdminSeeder::class,
            // Tambahkan seeder lain di sini jika perlu
            OutletsSeeder::class,
            CategoriesSeeder::class,
            ProductsSeeder::class,
            PaymentMethodSeeder::class,
            VouchersSeeder::class,
        ]);
    }
}
