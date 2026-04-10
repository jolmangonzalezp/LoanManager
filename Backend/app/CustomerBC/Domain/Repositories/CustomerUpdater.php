<?php

declare(strict_types=1);

namespace App\CustomerBC\Domain\Repositories;

use App\CustomerBC\Domain\Entities\Customer;

interface CustomerUpdater
{
    public function update(Customer $customer): void;
}
