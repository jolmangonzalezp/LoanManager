<?php

declare(strict_types=1);

namespace App\UserBC\Domain\Repository;

use App\UserBC\Domain\Aggregate\User;

interface UserFinderAll
{
    /** @return User[] */
    public function findAll(): array;
}
