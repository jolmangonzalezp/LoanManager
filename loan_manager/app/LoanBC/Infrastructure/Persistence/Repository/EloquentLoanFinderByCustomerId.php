<?php

declare(strict_types=1);

namespace App\LoanBC\Infrastructure\Persistence\Repository;

use App\CustomerBC\Domain\ValueObject\CustomerIdVO;
use App\LoanBC\Domain\Repository\LoanFinderByCustomerId;
use App\LoanBC\Infrastructure\Persistence\Model\LoanModel;
use App\LoanBC\Infrastructure\Mapper\LoanMapper;

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
