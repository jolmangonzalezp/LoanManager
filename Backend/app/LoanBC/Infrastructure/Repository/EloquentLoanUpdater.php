<?php

declare(strict_types=1);

namespace App\LoanBC\Infrastructure\Repository;

use App\LoanBC\Domain\Entities\Loan;
use App\LoanBC\Domain\Repositories\LoanUpdater;
use App\LoanBC\Infrastructure\Models\LoanModel;
use App\LoanBC\Infrastructure\Persistence\LoanMapper;

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
