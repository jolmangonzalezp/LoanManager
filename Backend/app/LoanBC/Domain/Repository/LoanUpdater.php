<?php

declare(strict_types=1);

namespace App\LoanBC\Domain\Repository;

use App\LoanBC\Domain\Aggregate\Loan;

interface LoanUpdater
{
    public function update(Loan $loan): void;
}
