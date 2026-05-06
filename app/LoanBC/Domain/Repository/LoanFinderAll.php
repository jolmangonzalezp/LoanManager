<?php

declare(strict_types=1);

namespace App\LoanBC\Domain\Repository;

use App\LoanBC\Domain\ValueObject\LoanStatus;

interface LoanFinderAll
{
    public function findAll(): array;

    public function findAllByStatus(LoanStatus $status): array;
}
