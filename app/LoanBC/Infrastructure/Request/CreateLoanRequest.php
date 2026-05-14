<?php

declare(strict_types=1);

namespace App\LoanBC\Infrastructure\Request;

use App\CustomerBC\Domain\ValueObject\CustomerIdVO;
use App\LoanBC\Application\CQRS\Command\CreateLoanCommand;
use App\LoanBC\Domain\ValueObject\InterestRateVO;
use App\LoanBC\Domain\ValueObject\LoanTypeIdVO;
use App\SharedKernel\Domain\ValueObject\DateVO;
use App\SharedKernel\Domain\ValueObject\MoneyVO;
use Illuminate\Http\Request;

final class CreateLoanRequest
{
    public static function fromArray(Request $request): CreateLoanCommand
    {
        $capital = MoneyVO::create((int) ($request['capital'] ?? 0));
        $interestRate = InterestRateVO::createMonthly((float) ($request['interest_rate']));
        $loanTypeId = $request['loan_type_id'] ? LoanTypeIdVO::fromString($request['loan_type_id']) : null;

        return new CreateLoanCommand(
            CustomerIdVO::fromString((string) ($request['customer'] ?? $request['customer_id'])),
            $loanTypeId,
            $capital,
            $interestRate,
            DateVO::fromString($request['date_start'] ?? $request['start_date']),
            (int) ($request['term'] ?? 24),
            loanTypeName: $request['loan_type_name']
        );
    }
}
