<?php

declare(strict_types=1);

namespace App\CustomerBC\Domain\Repositories;

interface CustomerFinderAll
{
    public function findAll(): array;
}
