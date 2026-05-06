<?php

namespace Database\Seeders;

use App\SharedKernel\Domain\ValueObject\AddressVO;
use App\SharedKernel\Domain\ValueObject\DniType;
use App\SharedKernel\Domain\ValueObject\DniVO;
use App\SharedKernel\Domain\ValueObject\EmailVO;
use App\SharedKernel\Domain\ValueObject\NameVO;
use App\SharedKernel\Domain\ValueObject\PersonVO;
use App\SharedKernel\Domain\ValueObject\PhoneVO;
use App\SharedKernel\Domain\ValueObject\DateVO;
use App\UserBC\Domain\Aggregate\User;
use App\UserBC\Domain\ValueObject\UserIdVO;
use App\UserBC\Infrastructure\Persistence\Model\UserModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $name = NameVO::create('Admin', 'Strator', 'Admin', null);
        $dni = DniVO::create('00000000', DniType::CC);
        $phone = PhoneVO::create('3000000000', '+57');
        $address = AddressVO::create('Admin Address');
        $email = EmailVO::create('admin@loanmanager.com');

        $personalData = new PersonVO($name, $dni, $phone, $address, $email);

        $user = User::reconstitute(
            UserIdVO::generate(),
            $personalData,
            Hash::make('admin123'),
            null,
            DateVO::fromString('2026-05-04'),
            DateVO::now(),
            true
        );

        // Use raw data insertion to bypass EmailVO string conversion issue
        $mapper = new \App\UserBC\Infrastructure\Mapper\UserMapper();
        $data = $mapper->toPersistence($user);
        
        // Fix email: EmailVO cannot be cast to string, so we set it explicitly
        $data['email'] = 'admin@loanmanager.com';
        
        UserModel::create($data);
    }
}
