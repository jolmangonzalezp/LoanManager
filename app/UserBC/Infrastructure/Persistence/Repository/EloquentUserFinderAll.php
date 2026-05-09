<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Persistence\Repository;

use App\UserBC\Domain\Aggregate\User;
use App\UserBC\Domain\Repository\UserFinderAll;
use App\UserBC\Infrastructure\Mapper\UserMapper;
use App\UserBC\Infrastructure\Persistence\Model\UserModel;

final class EloquentUserFinderAll implements UserFinderAll
{
    public function __construct(
        private readonly UserMapper $mapper
    ) {}

    public function findAll(): array
    {
        return UserModel::orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($m) => $this->mapper->toDomain($m))
            ->all();
    }
}
