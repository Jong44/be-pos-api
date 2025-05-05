<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OutletsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $userId = DB::table('users')->where('email', 'superadmin@pos.test')->first()->id;

        DB::table('outlets')->insert([
            [
                'id' => Str::uuid(),
                'outlet_name' => 'Outlet A',
                'address' => 'Jl. Merdeka No.1',
                'tax' => 0.1,
                'phone_number' => '08123456789',
                'created_at' => now(),
            ],
        ]);
    }
}
