<?php

declare(strict_types=1);

namespace App\UserBC\Domain\Repositories;

use App\UserBC\Domain\Entities\User;
use App\UserBC\Domain\ValueObjects\UserIdVO;

interface UserFinderById
{
    public function findById(UserIdVO $id): ?User;
}
