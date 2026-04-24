<?php

namespace Database\Seeders;

use App\CustomerBC\Infrastructure\Persistence\Model\CustomerModel;
use App\UserBC\Infrastructure\Persistence\Model\UserModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
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

        $customerId = '019d9def-1111-1111-1111-111111111111';
        CustomerModel::updateOrCreate(
            ['id' => $customerId],
            [
                'first_name' => Crypt::encryptString('Maria'),
                'last_name' => Crypt::encryptString('Lopez'),
                'second_last_name' => Crypt::encryptString('Garcia'),
                'middle_name' => Crypt::encryptString('Eugenia'),
                'dni_number' => Crypt::encryptString('98765432'),
                'dni_hash' => md5('98765432'),
                'dni_type' => 'CC',
                'phone_number' => Crypt::encryptString('3109876543'),
                'phone_country_code' => '+57',
                'address' => Crypt::encryptString('Carrera 45 #12-34 Bogota'),
                'email' => Crypt::encryptString('maria@test.com'),
                'enabled' => true,
            ]
        );
    }
}
