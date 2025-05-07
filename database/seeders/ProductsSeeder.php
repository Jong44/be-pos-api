<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $outlet = DB::table('outlets')->first();
        $category = DB::table('categories')->where('name', 'Beverages')->first();

        DB::table('products')->insert([
            [
                'id' => Str::uuid(),
                'outlet_id' => $outlet->id,
                'category_id' => $category->id,
                'name' => 'Coca Cola 1L',
                'stock' => 100,
                'is_non_stock' => false,
                'initial_price' => 8000,
                'selling_price' => 10000,
                'unit' => 'botol',
                'created_at' => now(),
            ],

            [
                'id' => Str::uuid(),
                'outlet_id' => $outlet->id,
                'category_id' => $category->id,
                'name' => 'Sprite 1L',
                'stock' => 100,
                'is_non_stock' => false,
                'initial_price' => 8000,
                'selling_price' => 10000,
                'unit' => 'botol',
                'created_at' => now(),
            ],

            [
                'id' => Str::uuid(),
                'outlet_id' => $outlet->id,
                'category_id' => $category->id,
                'name' => 'Fanta 1L',
                'stock' => 100,
                'is_non_stock' => false,
                'initial_price' => 8000,
                'selling_price' => 10000,
                'unit' => 'botol',
                'created_at' => now(),
            ]
        ]);
    }
}
