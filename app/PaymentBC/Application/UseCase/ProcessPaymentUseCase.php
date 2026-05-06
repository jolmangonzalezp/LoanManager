<?php

declare(strict_types=1);

namespace App\PaymentBC\Application\UseCase;

use App\LoanBC\Domain\Repository\LoanFinderById;
use App\LoanBC\Domain\Repository\LoanUpdater;
use App\LoanBC\Infrastructure\Persistence\Model\LoanModel;
use App\PaymentBC\Application\CQRS\Command\ProcessPaymentCommand;
use App\PaymentBC\Application\DTO\PaymentResponse;
use App\PaymentBC\Domain\Aggregate\Payment;
use App\PaymentBC\Domain\Repository\PaymentCreator;
use App\SharedKernel\Application\Exception\NotFoundException;
use App\SharedKernel\Domain\ValueObject\DateVO;
use App\SharedKernel\Domain\ValueObject\MoneyVO;

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
            $command->paymentDate ? DateVO::fromString($command->paymentDate) : null
        );

        $payment->validate();

        $distribution = $loan->makePayment($command->amount);

        $payment->apply($distribution->interestPortion, $distribution->capitalPortion);

        $this->paymentCreator->create($payment);

        $this->loanUpdater->update($distribution->loan);

        $response = PaymentResponse::fromEntity($payment);
        return $response->withRemainingDebt($distribution->loan->getRemainingDebt()->getAmount());
    }
}