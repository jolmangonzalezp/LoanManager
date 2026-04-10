<?php

declare(strict_types=1);

namespace App\LoanBC\Infrastructure\Repository;

use App\LoanBC\Domain\Entities\Loan;
use App\LoanBC\Domain\Repositories\LoanFinderById;
use App\LoanBC\Domain\ValueObjects\LoanIdVO;
use App\LoanBC\Infrastructure\Models\LoanModel;
use App\LoanBC\Infrastructure\Persistence\LoanMapper;

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
