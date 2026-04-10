<?php

declare(strict_types=1);

namespace App\CustomerBC\Application\Commands;

use App\SharedKernel\Domain\ValueObjects\PersonVO;

final class CreateCustomerCommand
{
    public function __construct(
        public readonly PersonVO $personalData
    ) {}
}
