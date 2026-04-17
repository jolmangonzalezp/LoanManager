<?php

namespace App\LoanBC\Domain\Services;

use App\LoanBC\Infrastructure\Persistence\Model\LoanModel;

final class LoanNumberGenerator
{
    public function generate(): string
    {
        $year = date('Y');
        $count = LoanModel::whereYear('created_at', $year)->count();
        $nextNumber = $count + 1;

        return sprintf('L-%s-%04d', $year, $nextNumber);
    }
}
