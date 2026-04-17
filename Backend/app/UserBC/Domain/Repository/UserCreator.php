<?php

declare(strict_types=1);

namespace App\UserBC\Domain\Repository;

use App\UserBC\Domain\Aggregate\User;

interface UserCreator
{
    public function create(User $user): void;
}
