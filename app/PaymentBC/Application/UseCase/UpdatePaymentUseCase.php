<?php

declare(strict_types=1);

namespace App\PaymentBC\Application\UseCase;

use App\PaymentBC\Application\CQRS\Command\UpdatePaymentCommand;
use App\PaymentBC\Application\DTO\PaymentResponse;
use App\PaymentBC\Application\PaymentNotFoundException;
use App\PaymentBC\Domain\Ports\LoanPaymentProcessor;
use App\PaymentBC\Domain\Repository\PaymentFinderById;
use App\PaymentBC\Domain\Repository\PaymentUpdater;
use App\SharedKernel\Domain\ValueObject\MoneyVO;
use Illuminate\Support\Facades\Log;

final class UpdatePaymentUseCase
{
    private ?PaymentResponse $response = null;

    public function __construct(
        private PaymentFinderById $finder,
        private PaymentUpdater $updater,
        private LoanPaymentProcessor $loanPaymentProcessor
    ) {}

    public function getResponse(): ?PaymentResponse
    {
        return $this->response;
    }

    public function execute(UpdatePaymentCommand $command): bool
    {
        Log::info('[UpdatePayment] Starting', [
            'payment_id' => $command->paymentId->getValue(),
            'new_loan_id' => $command->loanId->getValue(),
            'new_amount' => $command->amount->getAmount(),
        ]);

        $payment = $this->finder->findById($command->paymentId);

        if ($payment === null) {
            Log::warning('[UpdatePayment] Payment not found', ['payment_id' => $command->paymentId->getValue()]);
            throw new PaymentNotFoundException();
        }

        $oldInterestPortion = $payment->getInterestPaid() ?? MoneyVO::zero();
        $oldCapitalPortion = $payment->getCapitalPaid() ?? MoneyVO::zero();

        Log::info('[UpdatePayment] Old payment data', [
            'old_interest' => $oldInterestPortion->getAmount(),
            'old_capital' => $oldCapitalPortion->getAmount(),
            'old_status' => $payment->getStatus()->value,
        ]);

        $result = $this->loanPaymentProcessor->reprocessPayment(
            $command->loanId,
            $command->amount,
            $oldInterestPortion,
            $oldCapitalPortion
        );

        Log::info('[UpdatePayment] Reprocess result', [
            'new_interest_portion' => $result->interestPortion->getAmount(),
            'new_capital_portion' => $result->capitalPortion->getAmount(),
            'new_remaining_debt' => $result->remainingDebt->getAmount(),
        ]);

        $payment->update(
            $command->loanId,
            $command->amount,
            $command->paymentDate,
            $command->paymentMethod
        );

        $payment->apply($result->interestPortion, $result->capitalPortion);

        $this->updater->update($payment);

        Log::info('[UpdatePayment] Completed');

        $this->response = PaymentResponse::fromEntity($payment);
        $this->response = $this->response->withRemainingDebt($result->remainingDebt->getAmount());

        return true;
    }
}
