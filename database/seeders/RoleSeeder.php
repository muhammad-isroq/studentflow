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
        // Gunakan firstOrCreate untuk semua role agar aman
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $staffRole = Role::firstOrCreate(['name' => 'staff']);
        $guruRole  = Role::firstOrCreate(['name' => 'guru']); 
        $editorRole = Role::firstOrCreate(['name' => 'editor']);

        // (Bagian permission Anda sudah aman dengan firstOrCreate)
        $permissions = [
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view articles',  
            'create articles',
            'edit articles',  
            'delete articles',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        $adminRole->syncPermissions(Permission::all());
        $editorRole->givePermissionTo([
            'view articles',
            'create articles',
            'edit articles',
            'delete articles',
        ]);

        // Gunakan firstOrCreate untuk admin agar password tidak selalu di-reset
        $admin = User::firstOrCreate(
            ['email' => 'isroq@studentflow.com'],
            [
                'name' => 'Isroq',
                'password' => bcrypt('isroq2510'),
            ]
        );
        $admin->assignRole($adminRole);

        // Daftar staff (bagian ini sudah aman)
        $staffs = [
            // ... (daftar staff Anda)
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