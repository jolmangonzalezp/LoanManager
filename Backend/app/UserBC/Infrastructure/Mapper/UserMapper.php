<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Mapper;

use App\SharedKernel\Domain\ValueObject\AddressVO;
use App\SharedKernel\Domain\ValueObject\DateVO;
use App\SharedKernel\Domain\ValueObject\DniType;
use App\SharedKernel\Domain\ValueObject\DniVO;
use App\SharedKernel\Domain\ValueObject\EmailVO;
use App\SharedKernel\Domain\ValueObject\NameVO;
use App\SharedKernel\Domain\ValueObject\PersonVO;
use App\SharedKernel\Domain\ValueObject\PhoneVO;
use App\UserBC\Domain\Aggregate\User;
use App\UserBC\Domain\ValueObject\UserIdVO;
use App\UserBC\Infrastructure\Persistence\Model\UserModel;

final class UserMapper
{
    public function toDomain(UserModel $model): User
    {
        $nameParts = $this->parseName($model->name);
        
        $name = NameVO::create(
            $nameParts['firstName'],
            $nameParts['lastName'],
            $nameParts['secondLastName'],
            $nameParts['middleName']
        );
        
        $dni = DniVO::create('00000000', DniType::CC);
        $phone = PhoneVO::create('3000000000', '+57');
        $address = AddressVO::create('Calle 123, Bogota, Colombia');
        $email = ! empty($model->email) ? EmailVO::create($model->email) : null;

        $personalData = new PersonVO(
            $name,
            $dni,
            $phone,
            $address,
            $email
        );

        $emailVerifiedAt = $model->email_verified_at
            ? DateVO::fromDateTime($model->email_verified_at)
            : null;
        $createdAt = DateVO::fromDateTime($model->created_at);

        return User::reconstitute(
            UserIdVO::fromString($model->id),
            $personalData,
            $model->password,
            $model->remember_token,
            $emailVerifiedAt,
            $createdAt,
            (bool) $model->enabled
        );
    }

    private function parseName(string $fullName): array
    {
        $parts = array_filter(explode(' ', trim($fullName)));
        $parts = array_values($parts);
        $count = count($parts);

        if ($count === 0) {
            return ['firstName' => 'Unknown', 'lastName' => 'Unknown', 'secondLastName' => '', 'middleName' => null];
        }
        if ($count === 1) {
            return ['firstName' => $parts[0], 'lastName' => 'Unknown', 'secondLastName' => '', 'middleName' => null];
        }
        if ($count === 2) {
            return ['firstName' => $parts[0], 'lastName' => $parts[1], 'secondLastName' => '', 'middleName' => null];
        }
        if ($count === 3) {
            return ['firstName' => $parts[0], 'lastName' => $parts[1], 'secondLastName' => $parts[2], 'middleName' => null];
        }
        
        return [
            'firstName' => $parts[0],
            'middleName' => $parts[1],
            'lastName' => $parts[$count - 2],
            'secondLastName' => $parts[$count - 1],
        ];
    }

    public function toPersistence(User $user): array
    {
        $personalData = $user->getPersonalData();
        $name = $personalData->getName();
        $email = $personalData->getEmail();

        return [
            'id' => $user->getId()->getValue(),
            'name' => $name->getFullName(),
            'email' => $email ? (string) $email : null,
            'password' => $user->getPassword(),
            'remember_token' => $user->getRememberToken(),
            'email_verified_at' => $user->getEmailVerifiedAt()?->getFormatted(),
            'enabled' => $user->isEnabled(),
        ];
    }
}
