<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Persistence\Repository;

use App\UserBC\Domain\Aggregate\User;
use App\UserBC\Domain\Repository\UserCreator;
use App\UserBC\Infrastructure\Persistence\Model\UserModel;
use App\UserBC\Infrastructure\Mapper\UserMapper;

final class EloquentUserCreator implements UserCreator
{
    public function create(User $user): void
    {
        $mapper = new UserMapper;
        $data = $mapper->toPersistence($user);

        UserModel::create($data);
    }
}
