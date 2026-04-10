<?php

declare(strict_types=1);

namespace App\LoanBC\Domain\Repositories;

use App\LoanBC\Domain\Entities\Loan;

interface LoanCreator
{
    public function create(Loan $loan): void;
}
