<?php

namespace Database\Seeders;

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

         // Buat permission default
    $permissions = [
        'view users',
        'create users',
        'edit users',
        'delete users',
    ];

    foreach ($permissions as $permission) {
        Permission::firstOrCreate(['name' => $permission]);
    }

    // Kasih semua permission ke admin
    $adminRole->syncPermissions(Permission::all());

        // Buat admin
        $admin = User::updateOrCreate(
            ['email' => 'isroq@studentflow.com'],
            [
                'name' => 'Isroq',
                'password' => bcrypt('isroq2510'), // ganti dengan password aman
            ]
        );
        $admin->assignRole($adminRole);

        // Daftar staff
        $staffs = [
            ['name' => 'Staff', 'email' => 'staff@studentflow.com'],
            ['name' => 'Mr. Mantro', 'email' => 'mantro@studentflow.staff'],
            ['name' => 'Ms. Mega',   'email' => 'mega@studentflow.staff'],
            ['name' => 'Ms. Riska',  'email' => 'riska@studentflow.staff'],
            ['name' => 'Ms. Ulfa',   'email' => 'ulfa@studentflow.staff'],
            ['name' => 'Ms. Ratyh',  'email' => 'ratyh@studentflow.staff'],
            ['name' => 'Ms. Tya',    'email' => 'tya@studentflow.staff'],
            ['name' => 'Ms. Dwi',    'email' => 'dwi@studentflow.staff'],
            ['name' => 'Mr. Randy',  'email' => 'randy@studentflow.staff'],
        ];

        foreach ($staffs as $staffData) {
            $staff = User::firstOrCreate(
                ['email' => $staffData['email']],
                [
                    'name' => $staffData['name'],
                    'password' => bcrypt('12345'),
                ]
            );
            $staff->assignRole($staffRole);
        }
    }
}
