<?php

declare(strict_types=1);

namespace App\CustomerBC\Domain\Repository;

use App\CustomerBC\Domain\Aggregate\Customer;

interface CustomerCreator
{
    public function create(Customer $customer): void;
}
