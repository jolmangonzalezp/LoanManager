<?php

declare(strict_types=1);

namespace App\LoanBC\Domain\Repositories;

use App\LoanBC\Domain\Entities\Loan;

interface LoanUpdater
{
    public function update(Loan $loan): void;
}
