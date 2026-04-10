<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Repositories;

use App\SharedKernel\Domain\ValueObjects\AddressVO;
use App\SharedKernel\Domain\ValueObjects\DateVO;
use App\SharedKernel\Domain\ValueObjects\DniType;
use App\SharedKernel\Domain\ValueObjects\DniVO;
use App\SharedKernel\Domain\ValueObjects\EmailVO;
use App\SharedKernel\Domain\ValueObjects\NameVO;
use App\SharedKernel\Domain\ValueObjects\PersonVO;
use App\SharedKernel\Domain\ValueObjects\PhoneVO;
use App\UserBC\Domain\Entities\User;
use App\UserBC\Domain\ValueObjects\UserIdVO;
use App\UserBC\Infrastructure\Models\UserModel;

final class UserMapper
{
    public function toDomain(UserModel $model): User
    {
        $name = NameVO::create(
            $model->name,
            '',
            '',
            null
        );
        $dni = DniVO::create('00000000', DniType::CC);
        $phone = PhoneVO::create('+570000000000', '57');
        $address = AddressVO::create('');
        $email = ! empty($model->email) ? EmailVO::create($model->email) : null;

        $personalData = new PersonVO(
            $name,
            $dni,
            $phone,
            $address,
            $email
        );

        $emailVerifiedAt = $model->email_verified_at
            ? DateVO::create($model->email_verified_at)
            : null;
        $createdAt = DateVO::create($model->created_at);

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
