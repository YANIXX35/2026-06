<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@antigasci-ci.com'],
            [
                'nom'      => 'Admin',
                'prenom'   => 'AntiGaspiCI',
                'password' => 'Admin@1234',
                'role'     => 'admin',
                'statut'   => 'actif',
            ]
        );

        User::updateOrCreate(
            ['email' => 'kyliyanisse@gmail.com'],
            [
                'nom'      => 'Kyliyanisse',
                'prenom'   => 'Admin',
                'password' => 'admin123',
                'role'     => 'admin',
                'statut'   => 'actif',
            ]
        );

        User::updateOrCreate(
            ['email' => 'yaoyanissekyliane@gmail.com'],
            [
                'nom'      => 'Yao',
                'prenom'   => 'Yanisse',
                'password' => '12345678',
                'role'     => 'fournisseur',
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
