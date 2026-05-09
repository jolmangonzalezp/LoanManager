<?php

declare(strict_types=1);

namespace App\UserBC\Application\CQRS\Command;

use App\SharedKernel\Domain\ValueObject\EmailVO;
use App\SharedKernel\Domain\ValueObject\NameVO;
use App\SharedKernel\Domain\ValueObject\PhoneVO;

final readonly class UpdateUserCommand
{
    public function __construct(
        public string $id,
        public string $username,
        public ?NameVO $name = null,
        public ?EmailVO $email = null,
        public ?PhoneVO $phone = null,
        public ?bool $enabled = null,
    ) {}
}
