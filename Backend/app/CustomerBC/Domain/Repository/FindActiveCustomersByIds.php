<?php

declare(strict_types=1);

namespace App\CustomerBC\Domain\Repository;

interface FindActiveCustomersByIds 
{
    /**
     * @param array<string> $ids
     * @return array
     */
    public function findActiveByIds(array $ids): array;
}
