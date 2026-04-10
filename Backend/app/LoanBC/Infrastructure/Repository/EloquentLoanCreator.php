<?php

declare(strict_types=1);

namespace App\LoanBC\Infrastructure\Repository;

use App\LoanBC\Domain\Entities\Loan;
use App\LoanBC\Domain\Repositories\LoanCreator;
use App\LoanBC\Infrastructure\Models\LoanModel;
use App\LoanBC\Infrastructure\Persistence\LoanMapper;

final class EloquentLoanCreator implements LoanCreator
{
    public function __construct(
        private readonly LoanMapper $mapper
    ) {}

    public function create(Loan $loan): void
    {
        $data = $this->mapper->toPersistence($loan);

        LoanModel::create($data);
    }
}
