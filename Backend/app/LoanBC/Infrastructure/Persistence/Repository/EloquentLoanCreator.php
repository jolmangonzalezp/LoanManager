<?php

namespace App\LoanBC\Infrastructure\Persistence\Repository;

use App\LoanBC\Domain\Aggregate\Loan;
use App\LoanBC\Domain\Repository\LoanCreator;
use App\LoanBC\Domain\Services\LoanNumberGenerator;
use App\LoanBC\Infrastructure\Mapper\LoanMapper;
use App\LoanBC\Infrastructure\Persistence\Model\LoanModel;

final class EloquentLoanCreator implements LoanCreator
{
    public function __construct(
        private readonly LoanMapper $mapper,
        private readonly LoanNumberGenerator $loanNumberGenerator
    ) {}

    public function create(Loan $loan): void
    {
        $loanNumber = $this->loanNumberGenerator->generate();
        $this->mapper->setCurrentLoanNumber($loanNumber);

        $data = $this->mapper->toPersistence($loan);

        LoanModel::create($data);
    }
}
