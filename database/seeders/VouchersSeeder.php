<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VouchersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('vouchers')->insert([
            [
                'id' => Str::uuid(),
                'outlet_id' => DB::table('outlets')->first()->id,
                'name' => 'Diskon Lebaran',
                'code' => 'LEBARAN20',
                'type' => 'percentage',
                'nominal' => 20,
                'start_date' => now(),
                'expired_date' => now()->addDays(30),
                'minimum_buying' => 100000,
                'status' => 'active',
                'updated_at' => now(),
            ]
        ]);
    }
}
