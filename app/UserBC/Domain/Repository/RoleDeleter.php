<?php

declare(strict_types=1);

namespace App\UserBC\Domain\Repository;

use App\UserBC\Domain\ValueObject\RoleIdVO;

interface RoleDeleter
{
    public function delete(RoleIdVO $id): void;
}
