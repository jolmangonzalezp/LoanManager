<?php

declare(strict_types=1);

namespace App\LoanBC\Domain\Repositories;

use App\CustomerBC\Domain\ValueObjects\CustomerIdVO;

interface LoanFinderByCustomerId
{
    public function findByCustomerId(CustomerIdVO $customerId): array;
}
