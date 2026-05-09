<?php

declare(strict_types=1);

namespace App\UserBC\Domain\Repository;

use App\UserBC\Domain\ValueObject\PermissionIdVO;

interface PermissionDeleter
{
    public function delete(PermissionIdVO $id): void;
}
