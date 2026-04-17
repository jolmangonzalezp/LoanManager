<?php

declare(strict_types=1);

namespace App\LoanBC\Domain\Repository;

use App\LoanBC\Domain\Aggregate\Loan;

interface LoanCreator
{
    public function create(Loan $loan): void;
}
