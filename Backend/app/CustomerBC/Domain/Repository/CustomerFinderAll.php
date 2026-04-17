<?php

declare(strict_types=1);

namespace App\CustomerBC\Domain\Repository;

interface CustomerFinderAll
{
    public function findAll(): array;
}
