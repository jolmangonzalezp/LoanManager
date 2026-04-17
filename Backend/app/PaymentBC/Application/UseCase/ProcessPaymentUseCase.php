<?php

declare(strict_types=1);

namespace App\PaymentBC\Application\UseCase;

use App\CustomerBC\Domain\Repository\CustomerFinderById;
use App\LoanBC\Domain\Repository\LoanFinderById;
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
        private readonly CustomerFinderById $customerFinder
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

        $monthlyInterest = $loan->calculateCurrentInterest();

        $interestPortion = $payment->getAmount()->getAmount() >= $monthlyInterest->getAmount()
            ? $monthlyInterest
            : $payment->getAmount();

        $remainingAfterInterest = $payment->getAmount()->subtract($interestPortion);

        $capitalPortion = $remainingAfterInterest->getAmount() > 0
            ? $remainingAfterInterest
            : MoneyVO::zero();

        $payment->apply($interestPortion, $capitalPortion);

        $this->paymentCreator->create($payment);

        $this->updateLoanFromPayment($command->loanId->getValue(), $capitalPortion, $interestPortion);

        $response = PaymentResponse::fromEntity($payment);

        $customer = $this->customerFinder->findById($loan->getCustomerId());
        if ($customer !== null) {
            $response->customerName = $customer->getPersonalData()->getName()->getShortName();
        }

        return $response;
    }

    private function updateLoanFromPayment(string $loanId, MoneyVO $capitalPortion, MoneyVO $interestPortion): void
    {
        $loanModel = LoanModel::where('id', $loanId)->first();

        if ($loanModel === null) {
            return;
        }

        $loanModel->paid_interest = $loanModel->paid_interest + $interestPortion->getAmount();

        if ($capitalPortion->getAmount() > 0) {
            $loanModel->paid_capital = $loanModel->paid_capital + $capitalPortion->getAmount();
            $loanModel->remaining_debt = $loanModel->capital - $loanModel->paid_capital;
            $loanModel->capital = $loanModel->remaining_debt;
        }

        $loanModel->save();
    }
}
