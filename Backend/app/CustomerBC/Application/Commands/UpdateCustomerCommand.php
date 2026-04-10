<?php

declare(strict_types=1);

namespace App\CustomerBC\Application\Commands;

use App\SharedKernel\Domain\ValueObjects\PersonVO;

final class UpdateCustomerCommand
{
    public function __construct(
        public readonly string $id,
        public readonly PersonVO $personalData
    ) {}
}
