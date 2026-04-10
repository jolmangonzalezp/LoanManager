<?php

declare(strict_types=1);

namespace App\LoanBC\Domain\Repositories;

interface LoanFinderAll
{
    public function findAll(): array;
}
