<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@antigaspi-ci.com'],
            [
                'nom'      => 'Admin',
                'prenom'   => 'AntiGaspiCI',
                'password' => Hash::make('Admin@1234'),
                'role'     => 'admin',
                'statut'   => 'actif',
            ]
        );

        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
            if (!$admin->hasRole('admin')) {
                $admin->assignRole($role);
            }
        }
    }
}
