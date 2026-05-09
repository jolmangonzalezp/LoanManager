<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Persistence\Repository;

use App\UserBC\Domain\Aggregate\User;
use App\UserBC\Domain\Repository\UserUpdater;
use App\UserBC\Infrastructure\Mapper\UserMapper;
use App\UserBC\Infrastructure\Persistence\Model\UserModel;

final class EloquentUserUpdater implements UserUpdater
{
    public function __construct(
        private readonly UserMapper $mapper
    ) {}

    public function update(User $user): void
    {
        $data = $this->mapper->toPersistence($user);

        UserModel::where('id', $user->getId()->getValue())->update($data);
    }
}
