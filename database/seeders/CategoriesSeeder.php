<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $outletId = DB::table('outlets')->first()->id;
        DB::table('categories')->insert([
            ['id' => Str::uuid(), 'outlet_id'=> $outletId, 'name' => 'Beverages', 'created_at' => now()],
            ['id' => Str::uuid(), 'outlet_id'=> $outletId, 'name' => 'Snacks', 'created_at' => now()],
            ['id' => Str::uuid(), 'outlet_id'=> $outletId, 'name' => 'Groceries', 'created_at' => now()],
        ]);
    }
}
