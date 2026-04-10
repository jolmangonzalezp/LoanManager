<?php

declare(strict_types=1);

namespace App\LoanBC\Infrastructure\Repository;

use App\CustomerBC\Domain\ValueObjects\CustomerIdVO;
use App\LoanBC\Domain\Repositories\LoanFinderByCustomerId;
use App\LoanBC\Infrastructure\Models\LoanModel;
use App\LoanBC\Infrastructure\Persistence\LoanMapper;

final class EloquentLoanFinderByCustomerId implements LoanFinderByCustomerId
{
    public function __construct(
        private readonly LoanMapper $mapper
    ) {}

    public function findByCustomerId(CustomerIdVO $customerId): array
    {
        $models = LoanModel::where('customer_id', $customerId->getValue())
            ->orderBy('created_at', 'desc')
            ->get();

        return $models->map(fn ($m) => $this->mapper->toDomain($m))->all();
    }
}
