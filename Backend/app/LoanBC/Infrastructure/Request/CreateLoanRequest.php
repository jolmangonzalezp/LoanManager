<?php

declare(strict_types=1);

namespace App\LoanBC\Infrastructure\Request;

use App\CustomerBC\Domain\ValueObject\CustomerIdVO;
use App\LoanBC\Application\CQRS\Command\CreateLoanCommand;
use App\LoanBC\Domain\ValueObject\InterestRateVO;
use App\SharedKernel\Domain\ValueObject\DateVO;
use App\SharedKernel\Domain\ValueObject\MoneyVO;

final class CreateLoanRequest
{
    public static function fromArray(array $data): CreateLoanCommand
    {
        $capital = MoneyVO::create((int) ($data['capital'] ?? 0));

        $monthlyRate = (float) ($data['interest_rate'] ?? 0);
        $interestRate = InterestRateVO::createMonthly($monthlyRate);

        $startDateStr = $data['start_date'] ?? date('Y-m-d');
        $startDate = DateVO::fromString($startDateStr);

        $termMonths = $data['term'] ?? 24;
        $dueDate = date('Y-m-d', strtotime($startDateStr.' + '.$termMonths.' months'));
        $dueDateVO = DateVO::fromString($dueDate);

        $customerId = CustomerIdVO::fromString($data['customer_id']);

        return new CreateLoanCommand(
            $customerId,
            $capital,
            $interestRate,
            $startDate,
            $dueDateVO
        );
    }
}
