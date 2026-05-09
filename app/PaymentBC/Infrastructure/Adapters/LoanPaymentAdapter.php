<?php

declare(strict_types=1);

namespace App\PaymentBC\Infrastructure\Adapters;

use App\LoanBC\Domain\Repository\LoanFinderById;
use App\LoanBC\Domain\Repository\LoanUpdater;
use App\LoanBC\Domain\ValueObject\LoanIdVO as LoanBcLoanIdVO;
use App\PaymentBC\Domain\DTO\PaymentResult;
use App\PaymentBC\Domain\Ports\LoanPaymentProcessor;
use App\PaymentBC\Domain\ValueObject\LoanIdVO as PaymentLoanIdVO;
use App\SharedKernel\Domain\ValueObject\MoneyVO;
use Illuminate\Support\Facades\Log;

final readonly class LoanPaymentAdapter implements LoanPaymentProcessor
{
    public function __construct(
        private LoanFinderById $loanFinder,
        private LoanUpdater $loanUpdater
    ) {}

    public function processPayment(PaymentLoanIdVO $loanId, MoneyVO $amount): PaymentResult
    {
        $loanBcLoanId = LoanBcLoanIdVO::fromString($loanId->getValue());

        $loan = $this->loanFinder->findById($loanBcLoanId);

        if ($loan === null) {
            throw new \RuntimeException('Loan not found');
        }

        $distribution = $loan->makePayment($amount);

        $this->loanUpdater->update($distribution->loan);

        return new PaymentResult(
            interestPortion: $distribution->interestPortion,
            capitalPortion: $distribution->capitalPortion,
            remainingDebt: $distribution->loan->getRemainingDebt()
        );
    }

    public function reprocessPayment(PaymentLoanIdVO $loanId, MoneyVO $newAmount, MoneyVO $oldInterestPortion, MoneyVO $oldCapitalPortion): PaymentResult
    {
        $loanBcLoanId = LoanBcLoanIdVO::fromString($loanId->getValue());

        Log::info('[LoanPaymentAdapter] reprocessPayment start', [
            'loan_id' => $loanBcLoanId->getValue(),
            'new_amount' => $newAmount->getAmount(),
            'old_interest' => $oldInterestPortion->getAmount(),
            'old_capital' => $oldCapitalPortion->getAmount(),
        ]);

        $loan = $this->loanFinder->findById($loanBcLoanId);

        if ($loan === null) {
            Log::error('[LoanPaymentAdapter] Loan not found', ['loan_id' => $loanBcLoanId->getValue()]);
            throw new \RuntimeException('Loan not found');
        }

        Log::info('[LoanPaymentAdapter] Current loan state', [
            'paid_interest' => $loan->getPaidInterest()->getAmount(),
            'paid_capital' => $loan->getPaidCapital()->getAmount(),
            'remaining_debt' => $loan->getRemainingDebt()->getAmount(),
            'status' => $loan->getStatus()->value,
        ]);

        $reversedLoan = $loan->reversePayment($oldInterestPortion, $oldCapitalPortion);

        Log::info('[LoanPaymentAdapter] After reverse', [
            'paid_interest' => $reversedLoan->getPaidInterest()->getAmount(),
            'paid_capital' => $reversedLoan->getPaidCapital()->getAmount(),
            'remaining_debt' => $reversedLoan->getRemainingDebt()->getAmount(),
        ]);

        $distribution = $reversedLoan->makePayment($newAmount);

        Log::info('[LoanPaymentAdapter] After new payment', [
            'interest_portion' => $distribution->interestPortion->getAmount(),
            'capital_portion' => $distribution->capitalPortion->getAmount(),
            'remaining_debt' => $distribution->loan->getRemainingDebt()->getAmount(),
            'paid_interest' => $distribution->loan->getPaidInterest()->getAmount(),
            'paid_capital' => $distribution->loan->getPaidCapital()->getAmount(),
        ]);

        $this->loanUpdater->update($distribution->loan);

        Log::info('[LoanPaymentAdapter] reprocessPayment complete');

        return new PaymentResult(
            interestPortion: $distribution->interestPortion,
            capitalPortion: $distribution->capitalPortion,
            remainingDebt: $distribution->loan->getRemainingDebt()
        );
    }
}
