<?php

declare(strict_types=1);

namespace App\UserBC\Domain\Repositories;

use App\SharedKernel\Domain\ValueObjects\EmailVO;
use App\UserBC\Domain\Entities\User;

interface UserFinderByEmail
{
    public function findByEmail(EmailVO $email): ?User;
}
