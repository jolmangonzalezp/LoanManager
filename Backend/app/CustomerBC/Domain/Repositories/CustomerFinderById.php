<?php

declare(strict_types=1);

namespace App\CustomerBC\Domain\Repositories;

use App\CustomerBC\Domain\Entities\Customer;
use App\CustomerBC\Domain\ValueObjects\CustomerIdVO;

interface CustomerFinderById
{
    public function findById(CustomerIdVO $id): ?Customer;
}
