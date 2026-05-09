<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Persistence\Repository;

use App\UserBC\Domain\Aggregate\User;
use App\UserBC\Domain\Repository\UserFinderByUsername;
use App\UserBC\Infrastructure\Mapper\UserMapper;
use App\UserBC\Infrastructure\Persistence\Model\UserModel;

final class EloquentUserFinderByUsername implements UserFinderByUsername
{
    public function __construct(
        private readonly UserMapper $mapper
    ) {}

    public function findByUsername(string $username): ?User
    {
        $model = UserModel::where('username', $username)->first();

        return $model ? $this->mapper->toDomain($model) : null;
    }
}
