<?php

declare(strict_types=1);

namespace App\LoanBC\Infrastructure\Persistence\Repository;

use App\LoanBC\Domain\Aggregate\Loan;
use App\LoanBC\Domain\Repository\LoanUpdater;
use App\LoanBC\Infrastructure\Persistence\Model\LoanModel;
use App\LoanBC\Infrastructure\Mapper\LoanMapper;

final class EloquentLoanUpdater implements LoanUpdater
{
    public function __construct(
        private readonly LoanMapper $mapper
    ) {}

    public function update(Loan $loan): void
    {
        $data = $this->mapper->toPersistence($loan);

        LoanModel::updateOrCreate(
            ['id' => $data['id']],
            $data
        );
    }
}
