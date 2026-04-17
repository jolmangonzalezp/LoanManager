<?php

declare(strict_types=1);

namespace App\UserBC\Domain\Repository;

use App\SharedKernel\Domain\ValueObject\EmailVO;
use App\UserBC\Domain\Aggregate\User;

interface UserFinderByEmail
{
    public function findByEmail(EmailVO $email): ?User;
}
