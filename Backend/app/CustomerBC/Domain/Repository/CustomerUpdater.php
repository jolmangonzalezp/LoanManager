<?php

declare(strict_types=1);

namespace App\CustomerBC\Domain\Repository;

use App\CustomerBC\Domain\Aggregate\Customer;

interface CustomerUpdater
{
    public function update(Customer $customer): void;
}
