<?php

declare(strict_types=1);

namespace App\LoanBC\Domain\Repository;

interface LoanFinderAll
{
    public function findAll(): array;
}
