<?php

namespace Database\Seeders;

use App\SharedKernel\Domain\ValueObject\DateVO;
use App\SharedKernel\Domain\ValueObject\EmailVO;
use App\SharedKernel\Domain\ValueObject\NameVO;
use App\SharedKernel\Domain\ValueObject\PhoneVO;
use App\UserBC\Domain\Aggregate\User;
use App\UserBC\Domain\ValueObject\UserIdVO;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $name = NameVO::create('Admin', 'Strator', 'System');
        $email = EmailVO::create('admin@loanmanager.com');
        $phone = PhoneVO::create('3000000000', '+57');

        $user = User::create(
            username: 'admin',
            password: 'admin123',
            name: $name,
            email: $email,
            phone: $phone,
        );

        $mapper = new \App\UserBC\Infrastructure\Mapper\UserMapper();
        $data = $mapper->toPersistence($user);

        \App\UserBC\Infrastructure\Persistence\Model\UserModel::create($data);
    }
}
