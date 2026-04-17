<?php

declare(strict_types=1);

namespace App\UserBC\Domain\Repository;

use App\UserBC\Domain\Aggregate\User;
use App\UserBC\Domain\ValueObject\UserIdVO;

interface UserFinderById
{
    public function findById(UserIdVO $id): ?User;
}
