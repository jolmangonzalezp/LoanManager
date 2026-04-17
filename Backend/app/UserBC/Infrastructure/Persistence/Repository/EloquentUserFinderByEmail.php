<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Persistence\Repository;

use App\SharedKernel\Domain\ValueObject\EmailVO;
use App\UserBC\Domain\Aggregate\User;
use App\UserBC\Domain\Repository\UserFinderByEmail;
use App\UserBC\Infrastructure\Persistence\Model\UserModel;
use App\UserBC\Infrastructure\Mapper\UserMapper;

final class EloquentUserFinderByEmail implements UserFinderByEmail
{
    public function findByEmail(EmailVO $email): ?User
    {
        $mapper = new UserMapper;
        $model = UserModel::where('email', $email->getValue())->first();

        return $model ? $mapper->toDomain($model) : null;
    }
}
