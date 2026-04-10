<?php

declare(strict_types=1);

namespace App\LoanBC\Infrastructure\Repository;

use App\LoanBC\Domain\Repositories\LoanFinderAll;
use App\LoanBC\Infrastructure\Models\LoanModel;
use App\LoanBC\Infrastructure\Persistence\LoanMapper;

final class EloquentLoanFinderAll implements LoanFinderAll
{
    public function __construct(
        private readonly LoanMapper $mapper
    ) {}

    public function findAll(): array
    {
        $models = LoanModel::orderBy('created_at', 'desc')->get();

        return $models->map(fn ($m) => $this->mapper->toDomain($m))->all();
    }
}
