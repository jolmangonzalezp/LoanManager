<?php

declare(strict_types=1);

namespace App\LoanBC\Domain\Repository;

use App\CustomerBC\Domain\ValueObject\CustomerIdVO;

interface LoanFinderByCustomerId
{
    public function findByCustomerId(CustomerIdVO $customerId): array;
}
