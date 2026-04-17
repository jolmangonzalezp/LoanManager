<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Persistence\Repository;

use App\UserBC\Domain\Aggregate\User;
use App\UserBC\Domain\Repository\UserFinderById as UserFinderByIdInterface;
use App\UserBC\Domain\ValueObject\UserIdVO;
use App\UserBC\Infrastructure\Persistence\Model\UserModel;
use App\UserBC\Infrastructure\Mapper\UserMapper;

final class EloquentUserFinderById implements UserFinderByIdInterface
{
    public function __construct(
        private readonly UserMapper $mapper
    ) {}

    public function findById(UserIdVO $id): ?User
    {
        $model = UserModel::find($id->getValue());

        return $model ? $this->mapper->toDomain($model) : null;
    }
}
