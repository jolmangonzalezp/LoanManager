<?php

declare(strict_types=1);

namespace App\LoanBC\Application\Exception;

use App\SharedKernel\Application\Exception\ApplicationException;

final class LoanNotFoundException extends ApplicationException
{
    public function __construct(string $id)
    {
        parent::__construct("Loan not found: {$id}");
    }
}
