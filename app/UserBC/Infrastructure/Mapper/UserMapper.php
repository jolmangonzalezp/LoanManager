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
        $name = $model->name
            ? $this->parseName($model->name)
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
        return [
            'id' => $user->getId()->getValue(),
            'username' => $user->getUsername(),
            'name' => $user->getName()?->getFullName(),
            'email' => $user->getEmail()?->getValue(),
            'phone' => $user->getPhone()?->getNumber(),
            'password' => $user->getPassword(),
            'remember_token' => $user->getRememberToken(),
            'enabled' => $user->isEnabled(),
        ];
    }

    private function parseName(string $fullName): NameVO
    {
        $parts = array_filter(explode(' ', trim($fullName)));
        $parts = array_values($parts);
        $count = count($parts);

        if ($count === 0) {
            return NameVO::create('Unknown', 'Unknown', '');
        }
        if ($count === 1) {
            return NameVO::create($parts[0], 'Unknown', '');
        }
        if ($count === 2) {
            return NameVO::create($parts[0], $parts[1], '');
        }
        if ($count === 3) {
            return NameVO::create($parts[0], $parts[1], $parts[2]);
        }

        return NameVO::create(
            $parts[0],
            $parts[$count - 2],
            $parts[$count - 1],
            $parts[1],
        );
    }
}
