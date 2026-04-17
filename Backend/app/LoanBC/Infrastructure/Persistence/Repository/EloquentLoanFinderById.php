<?php

declare(strict_types=1);

namespace App\LoanBC\Infrastructure\Persistence\Repository;

use App\LoanBC\Domain\Aggregate\Loan;
use App\LoanBC\Domain\Repository\LoanFinderById;
use App\LoanBC\Domain\ValueObject\LoanIdVO;
use App\LoanBC\Infrastructure\Persistence\Model\LoanModel;
use App\LoanBC\Infrastructure\Mapper\LoanMapper;

final class EloquentLoanFinderById implements LoanFinderById
{
    public function __construct(
        private readonly LoanMapper $mapper
    ) {}

    public function findById(LoanIdVO $id): ?Loan
    {
        $model = LoanModel::find($id->getValue());

        return $model ? $this->mapper->toDomain($model) : null;
    }
}
