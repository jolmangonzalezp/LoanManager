<?php

declare(strict_types=1);

namespace App\LoanBC\Infrastructure\Persistence\Repository;

use App\LoanBC\Domain\Repository\LoanFinderAll;
use App\LoanBC\Infrastructure\Mapper\LoanMapper;
use App\LoanBC\Infrastructure\Persistence\Model\LoanModel;

final class EloquentLoanFinderAll implements LoanFinderAll
{
    public function __construct(
        private readonly LoanMapper $mapper
    ) {}

    public function findAll(): array
    {
        $models = LoanModel::orderBy('updated_at', 'desc')->get();

        return $models->map(fn ($m) => $this->mapper->toDomain($m))->all();
    }
}
