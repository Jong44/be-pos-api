<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSuperAdminSeeder extends Seeder
{
    public function run(): void
    {
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
