<?php

declare(strict_types=1);

namespace App\CustomerBC\Domain\Repository;

use App\CustomerBC\Domain\Aggregate\Customer;
use App\CustomerBC\Domain\ValueObject\CustomerIdVO;

interface CustomerFinderById
{
    public function findById(CustomerIdVO $id): ?Customer;
}
