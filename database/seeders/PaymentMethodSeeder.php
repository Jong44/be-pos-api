<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $outletId = DB::table('outlets')->first()->id;
        DB::table('payment_methods')->insert([
            ['id' => Str::uuid(), 'outlet_id' => $outletId,'name' => 'Cash', 'created_at' => now()],
            ['id' => Str::uuid(), 'outlet_id' => $outletId,'name' => 'QRIS', 'created_at' => now()],
            ['id' => Str::uuid(), 'outlet_id' => $outletId,'name' => 'Debit Card', 'created_at' => now()],
        ]);
    }
}
