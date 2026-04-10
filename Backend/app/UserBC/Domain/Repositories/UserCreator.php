<?php

declare(strict_types=1);

namespace App\UserBC\Domain\Repositories;

use App\UserBC\Domain\Entities\User;

interface UserCreator
{
    public function create(User $user): void;
}
