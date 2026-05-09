<?php

declare(strict_types=1);

namespace App\PaymentBC\Domain\Ports;

use App\PaymentBC\Domain\DTO\PaymentResult;
use App\PaymentBC\Domain\ValueObject\LoanIdVO;
use App\SharedKernel\Domain\ValueObject\MoneyVO;

interface LoanPaymentProcessor
{
    public function processPayment(LoanIdVO $loanId, MoneyVO $amount): PaymentResult;

    public function reprocessPayment(LoanIdVO $loanId, MoneyVO $newAmount, MoneyVO $oldInterestPortion, MoneyVO $oldCapitalPortion): PaymentResult;
}
