<?php

declare(strict_types=1);

namespace App\PaymentBC\Application\UseCases;

use App\LoanBC\Domain\Repositories\LoanFinderById;
use App\LoanBC\Domain\Repositories\LoanUpdater;
use App\PaymentBC\Application\Commands\ProcessPaymentCommand;
use App\PaymentBC\Application\DTOs\PaymentResponse;
use App\PaymentBC\Domain\Entities\Payment;
use App\PaymentBC\Domain\Repositories\PaymentCreator;
use App\SharedKernel\Application\Exceptions\NotFoundException;
use App\SharedKernel\Domain\ValueObjects\DateVO;
use App\SharedKernel\Domain\ValueObjects\MoneyVO;

final class ProcessPaymentUseCase
{
    public function __construct(
        private readonly PaymentCreator $paymentCreator,
        private readonly LoanFinderById $loanFinder,
        private readonly LoanUpdater $loanUpdater
    ) {}

    public function execute(ProcessPaymentCommand $command): PaymentResponse
    {
        $loan = $this->loanFinder->findById($command->loanId);

        if ($loan === null) {
            throw new NotFoundException('Préstamo no encontrado');
        }

        $payment = Payment::create(
            $command->loanId,
            $command->amount,
            $command->paymentDate ? DateVO::create($command->paymentDate) : null
        );

        $payment->validate();

        $monthlyInterest = $loan->calculateCurrentInterest();

        $interestPortion = $payment->getAmount()->getAmount() >= $monthlyInterest->getAmount()
            ? $monthlyInterest
            : $payment->getAmount();

        $remainingAfterInterest = $payment->getAmount()->subtract($interestPortion);

        $capitalPortion = $remainingAfterInterest->getAmount() > 0
            ? $remainingAfterInterest
            : MoneyVO::zero();

        $payment->apply($interestPortion, $capitalPortion);

        if ($capitalPortion->getAmount() > 0) {
            $updatedLoan = $loan->makePayment($command->amount);
            $this->loanUpdater->update($updatedLoan);
        }

        $this->paymentCreator->create($payment);

        return PaymentResponse::fromEntity($payment);
    }
}
