<?php

declare(strict_types=1);

namespace App\LoanBC\Domain\Repository;

use App\LoanBC\Domain\Aggregate\Loan;
use App\LoanBC\Domain\ValueObject\LoanIdVO;

interface LoanFinderById
{
    public function findById(LoanIdVO $id): ?Loan;
}
