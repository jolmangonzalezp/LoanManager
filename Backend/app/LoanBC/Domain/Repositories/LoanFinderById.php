<?php

declare(strict_types=1);

namespace App\LoanBC\Domain\Repositories;

use App\LoanBC\Domain\Entities\Loan;
use App\LoanBC\Domain\ValueObjects\LoanIdVO;

interface LoanFinderById
{
    public function findById(LoanIdVO $id): ?Loan;
}
