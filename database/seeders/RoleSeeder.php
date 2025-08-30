<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $staffRole = Role::firstOrCreate(['name' => 'staff']);

        $admin = User::firstOrCreate(
            ['email' => 'isroq@studentflow.com'],
            [
                'name' => 'Isroq',
                'password' => bcrypt('isroq2510'), // ganti dengan password aman
            ]
        );
        $admin->assignRole($adminRole);

        $staff = User::firstOrCreate(
            ['email' => 'staff@studentflow.com'],
            [
                'name' => 'Staff',
                'password' => bcrypt('staf23'),
            ]
        );
        $staff->assignRole($staffRole);
    }
}
