<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Repositories;

use App\SharedKernel\Domain\ValueObjects\EmailVO;
use App\UserBC\Domain\Entities\User;
use App\UserBC\Domain\Repositories\UserFinderByEmail;
use App\UserBC\Infrastructure\Models\UserModel;

final class EloquentUserFinderByEmail implements UserFinderByEmail
{
    public function findByEmail(EmailVO $email): ?User
    {
        $mapper = new UserMapper;
        $model = UserModel::where('email', (string) $email)->first();

        return $model ? $mapper->toDomain($model) : null;
    }
}
