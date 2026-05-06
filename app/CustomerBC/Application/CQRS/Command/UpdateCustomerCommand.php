<?php

declare(strict_types=1);

namespace App\CustomerBC\Application\CQRS\Command;

use App\CustomerBC\Domain\ValueObject\CustomerIdVO;
use App\SharedKernel\Domain\ValueObject\PersonVO;

final class UpdateCustomerCommand
{
    public function __construct(
        public readonly CustomerIdVO $id,
        public readonly PersonVO $personalData
    ) {}
}
