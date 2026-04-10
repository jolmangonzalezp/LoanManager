<?php

declare(strict_types=1);

namespace App\CustomerBC\Domain\Repositories;

use App\CustomerBC\Domain\Entities\Customer;

interface CustomerCreator
{
    public function create(Customer $customer): void;
}
