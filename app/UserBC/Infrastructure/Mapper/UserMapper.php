<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Mapper;

use App\SharedKernel\Domain\ValueObject\DateVO;
use App\SharedKernel\Domain\ValueObject\EmailVO;
use App\SharedKernel\Domain\ValueObject\NameVO;
use App\SharedKernel\Domain\ValueObject\PhoneVO;
use App\UserBC\Domain\Aggregate\User;
use App\UserBC\Domain\ValueObject\UserIdVO;
use App\UserBC\Infrastructure\Persistence\Model\UserModel;

final class UserMapper
{
    public function toDomain(UserModel $model): User
    {
        $name = $model->first_name
            ? NameVO::create(
                $model->first_name,
                $model->last_name ?? 'Unknown',
                $model->second_last_name ?? 'Unknown',
                $model->middle_name
            )
            : null;

        $email = $model->email
            ? EmailVO::create($model->email)
            : null;

        $phone = $model->phone
            ? PhoneVO::create($model->phone, '+57')
            : null;

        return User::reconstitute(
            id: UserIdVO::fromString($model->id),
            username: $model->username,
            password: $model->password,
            enabled: (bool) $model->enabled,
            createdAt: DateVO::fromDateTime($model->created_at),
            name: $name,
            email: $email,
            phone: $phone,
            rememberToken: $model->remember_token,
        );
    }

    public function toPersistence(User $user): array
    {
        $name = $user->getName();

        return [
            'id' => $user->getId()->getValue(),
            'username' => $user->getUsername(),
            'first_name' => $name?->getFirstName(),
            'middle_name' => $name?->getMiddleName(),
            'last_name' => $name?->getLastName(),
            'second_last_name' => $name?->getSecondLastName(),
            'email' => $user->getEmail()?->getValue(),
            'phone' => $user->getPhone()?->getNumber(),
            'password' => $user->getPassword(),
            'remember_token' => $user->getRememberToken(),
            'enabled' => $user->isEnabled(),
        ];
    }
}
