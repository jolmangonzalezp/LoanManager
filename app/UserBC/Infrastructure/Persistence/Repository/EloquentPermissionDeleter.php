<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Persistence\Repository;

use App\UserBC\Domain\Repository\PermissionDeleter;
use App\UserBC\Domain\ValueObject\PermissionIdVO;
use App\UserBC\Infrastructure\Persistence\Model\PermissionModel;

final class EloquentPermissionDeleter implements PermissionDeleter
{
    public function delete(PermissionIdVO $id): void
    {
        PermissionModel::where('id', $id->getValue())->delete();
    }
}
