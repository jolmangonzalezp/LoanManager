<?php

declare(strict_types=1);

namespace App\PaymentBC\Application\UseCase;

use App\PaymentBC\Application\CQRS\Command\ProcessPaymentCommand;
use App\PaymentBC\Application\DTO\PaymentResponse;
use App\PaymentBC\Domain\Aggregate\Payment;
use App\PaymentBC\Domain\Ports\LoanPaymentProcessor;
use App\PaymentBC\Domain\Repository\PaymentCreator;

final class ProcessPaymentUseCase
{
    private ?PaymentResponse $response = null;

    public function __construct(
        private readonly PaymentCreator $paymentCreator,
        private readonly LoanPaymentProcessor $loanPaymentProcessor
    ) {}

    public function getResponse(): ?PaymentResponse
    {
        return $this->response;
    }

    public function execute(ProcessPaymentCommand $command): bool
    {
        $payment = Payment::create(
            $command->loanId,
            $command->amount,
            $command->paymentDate,
            $command->paymentMethod
        );

        $result = $this->loanPaymentProcessor->processPayment(
            $command->loanId,
            $command->amount
        );

        $payment->apply($result->interestPortion, $result->capitalPortion);

        $this->paymentCreator->create($payment);

        $this->response = PaymentResponse::fromEntity($payment);
        $this->response = $this->response->withRemainingDebt($result->remainingDebt->getAmount());

        return true;
    }
}
