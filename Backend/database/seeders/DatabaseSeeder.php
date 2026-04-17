<?php

namespace Database\Seeders;

use App\UserBC\Infrastructure\Persistence\Model\UserModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        UserModel::updateOrCreate(
            ['email' => 'admin@loanmanager.com'],
            [
                'id' => '790601c2-7990-4ebf-9e49-3e18a407c439',
                'name' => 'Admin Usuario Principal',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
                'enabled' => true,
            ]
        );
    }
}
