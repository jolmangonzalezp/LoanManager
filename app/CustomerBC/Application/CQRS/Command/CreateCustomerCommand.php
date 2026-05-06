<?php

declare(strict_types=1);

namespace App\CustomerBC\Application\CQRS\Command;

use App\SharedKernel\Domain\ValueObject\PersonVO;

final class CreateCustomerCommand
{
    public readonly PersonVO $personalData;
    public function __construct(
        PersonVO $personalData
    ) {
        $this->personalData = $personalData;
    }

    public function getPersonalData(): PersonVO
    {
        return $this->personalData;
    }
}
