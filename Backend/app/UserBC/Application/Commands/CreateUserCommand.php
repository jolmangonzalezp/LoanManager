<?php

declare(strict_types=1);

namespace App\UserBC\Application\Commands;

use App\SharedKernel\Domain\ValueObjects\PersonVO;

final class CreateUserCommand
{
    public function __construct(
        public readonly PersonVO $personalData,
        public readonly string $password
    ) {}
}
