<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Persistence\Repository;

use App\UserBC\Domain\Aggregate\User;
use App\UserBC\Domain\Repository\UserCreator;
use App\UserBC\Infrastructure\Mapper\UserMapper;
use App\UserBC\Infrastructure\Persistence\Model\UserModel;

final class EloquentUserCreator implements UserCreator
{
    public function __construct(
        private readonly UserMapper $mapper
    ) {}

    public function create(User $user): void
    {
        $data = $this->mapper->toPersistence($user);

        UserModel::create($data);
    }
}
