<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'user_id' => 'Dtir2administrator',
            'password' => Hash::make('dticpo@123'),
            'role' => 'admin',
            'counter_id' => null
        ]);

        User::create([
            'user_id' => 'Dticounter1',
            'password' => Hash::make('dticpo@123'),
            'role' => 'counter',
            'counter_id' => 1
        ]);
    }
}
