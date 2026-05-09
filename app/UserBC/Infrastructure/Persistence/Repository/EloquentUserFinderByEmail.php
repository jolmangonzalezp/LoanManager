<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Persistence\Repository;

use App\SharedKernel\Domain\ValueObject\EmailVO;
use App\UserBC\Domain\Aggregate\User;
use App\UserBC\Domain\Repository\UserFinderByEmail;
use App\UserBC\Infrastructure\Mapper\UserMapper;
use App\UserBC\Infrastructure\Persistence\Model\UserModel;

final class EloquentUserFinderByEmail implements UserFinderByEmail
{
    public function __construct(
        private readonly UserMapper $mapper
    ) {}

    public function findByEmail(EmailVO $email): ?User
    {
        $model = UserModel::where('email', $email->getValue())->first();

        return $model ? $this->mapper->toDomain($model) : null;
    }
}
