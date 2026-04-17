<?php

declare(strict_types=1);

namespace App\CustomerBC\Domain\Repository;

interface FindActiveCustomers {
    public function findActive(): array;
}
