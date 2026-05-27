<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\School;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 🔹 Ensure SYS001 school exists
        $school = School::firstOrCreate(
            ['code' => 'SYS001'],
            [
                'name'       => 'System Default School',
                'address'    => 'Head Office',
                'email'      => 'system@school.com',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 🔹 Create Super Admin user
        User::firstOrCreate(
            ['email' => 'superadmin@system.com'],
            [
                'school_code' => $school->code,
                'name'        => 'Super Admin',
                'password'    => Hash::make('password123'), // default password
                'role'        => 'SuperAdmin',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]
        );
    }
}
