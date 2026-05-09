<?php

declare(strict_types=1);

namespace App\UserBC\Application\CQRS\Command;

use App\SharedKernel\Domain\ValueObject\EmailVO;
use App\SharedKernel\Domain\ValueObject\NameVO;
use App\SharedKernel\Domain\ValueObject\PhoneVO;

final readonly class CreateUserCommand
{
    public function __construct(
        public string $username,
        public string $password,
        public ?NameVO $name = null,
        public ?EmailVO $email = null,
        public ?PhoneVO $phone = null,
    ) {}
}
