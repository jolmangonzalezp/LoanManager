<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Repositories;

use App\UserBC\Domain\Entities\User;
use App\UserBC\Domain\Repositories\UserCreator;
use App\UserBC\Infrastructure\Models\UserModel;

final class EloquentUserCreator implements UserCreator
{
    public function create(User $user): void
    {
        $mapper = new UserMapper;
        $data = $mapper->toPersistence($user);

        UserModel::create($data);
    }
}
