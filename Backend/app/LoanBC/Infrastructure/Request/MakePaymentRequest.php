<?php

declare(strict_types=1);

namespace App\LoanBC\Infrastructure\Request;

use App\LoanBC\Application\CQRS\Command\MakePaymentCommand;
use App\LoanBC\Domain\ValueObject\LoanIdVO;
use App\SharedKernel\Domain\ValueObject\MoneyVO;

final class MakePaymentRequest
{
    public static function fromArray(string $loanId, array $data): MakePaymentCommand
    {
        $amountCents = (int) (($data['amount'] ?? 0) * 100);
        $amount = MoneyVO::create($amountCents);
        $loanIdVO = LoanIdVO::fromString($loanId);

        return new MakePaymentCommand($loanIdVO, $amount);
    }
}
