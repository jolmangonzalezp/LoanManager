<?php

declare(strict_types=1);

namespace App\UserBC\Application\Commands;

use App\SharedKernel\Domain\ValueObjects\EmailVO;

final class LoginCommand
{
    public function __construct(
        public readonly EmailVO $email,
        public readonly string $password
    ) {}
}
