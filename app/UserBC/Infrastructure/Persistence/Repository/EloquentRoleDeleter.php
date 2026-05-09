<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Persistence\Repository;

use App\UserBC\Domain\Repository\RoleDeleter;
use App\UserBC\Domain\ValueObject\RoleIdVO;
use App\UserBC\Infrastructure\Persistence\Model\RoleModel;

final class EloquentRoleDeleter implements RoleDeleter
{
    public function delete(RoleIdVO $id): void
    {
        RoleModel::where('id', $id->getValue())->delete();
    }
}
