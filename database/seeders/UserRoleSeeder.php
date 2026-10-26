<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'email' => 'admin@klambibali.com',
                'name' => 'Owner / Super Admin KlambiBali',
                'role' => User::ROLE_OWNER,
                'phone' => '081234567801',
                'password' => Hash::make('password'),
                'status' => 'active',
            ],
            [
                'email' => 'purchasing@klambibali.com',
                'name' => 'I Gede Artawan (Purchasing)',
                'role' => User::ROLE_PURCHASING,
                'phone' => '081234567802',
                'password' => Hash::make('password'),
                'status' => 'active',
            ],
            [
                'email' => 'produksi@klambibali.com',
                'name' => 'I Made Raka (Produksi & Potong)',
                'role' => User::ROLE_PRODUKSI,
                'phone' => '081234567803',
                'password' => Hash::make('password'),
                'status' => 'active',
            ],
            [
                'email' => 'qc@klambibali.com',
                'name' => 'Ni Kadek Ayu (Supervisor QC)',
                'role' => User::ROLE_PRODUKSI,
                'phone' => '081234567804',
                'password' => Hash::make('password'),
                'status' => 'active',
            ],
            [
                'email' => 'gudang@klambibali.com',
                'name' => 'I Ketut Sudira (Kepala Gudang)',
                'role' => User::ROLE_GUDANG,
                'phone' => '081234567805',
                'password' => Hash::make('password'),
                'status' => 'active',
            ],
            [
                'email' => 'sales@klambibali.com',
                'name' => 'Ni Putu Mirah (Sales & CS)',
                'role' => User::ROLE_SALES,
                'phone' => '081234567806',
                'password' => Hash::make('password'),
                'status' => 'active',
            ],
            [
                'email' => 'kasir@klambibali.com',
                'name' => 'Ni Wayan Sukerti (Kasir POS)',
                'role' => User::ROLE_KASIR,
                'phone' => '081234567807',
                'password' => Hash::make('password'),
                'status' => 'active',
            ],
            [
                'email' => 'finance@klambibali.com',
                'name' => 'I Made Dharmawan (Finance & Accounting)',
                'role' => User::ROLE_FINANCE,
                'phone' => '081234567808',
                'password' => Hash::make('password'),
                'status' => 'active',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
