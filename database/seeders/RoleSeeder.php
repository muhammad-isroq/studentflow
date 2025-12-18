<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $staffRole = Role::firstOrCreate(['name' => 'staff']);
        $guruRole  = Role::firstOrCreate(['name' => 'guru']);
        $editorRole = Role::firstOrCreate(['name' => 'editor']);
        $superStaffRole = Role::firstOrCreate(['name' => 'super_staff']);

        $permissions = [
            'view users', 'create users', 'edit users', 'delete users',
            'view articles', 'create articles', 'edit articles', 'delete articles',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        
        $adminRole->syncPermissions(Permission::all());
        
        $admin = User::firstOrCreate(
            ['email' => 'isroq@studentflow.com'],
            [
                'name' => 'Isroq',
                'password' => bcrypt('isroq2510'),
                'email_verified_at' => now(),
            ]
        );
        
        // Cek dulu apakah user sudah punya role, jika belum baru assign
        if (!$admin->hasRole('admin')) {
            $admin->assignRole($adminRole);
        }
        $admin->assignRole($adminRole);

        
        $staffs = [
            
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