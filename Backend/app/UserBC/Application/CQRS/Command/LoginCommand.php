<?php

declare(strict_types=1);

namespace App\UserBC\Application\CQRS\Command;

use App\SharedKernel\Domain\ValueObject\EmailVO;

final class LoginCommand
{
    public function __construct(
        public readonly EmailVO $email,
        public readonly string $password
    ) {}
}
