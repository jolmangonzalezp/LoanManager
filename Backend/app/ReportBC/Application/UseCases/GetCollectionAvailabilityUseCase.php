<?php

declare(strict_types=1);

namespace App\ReportBC\Application\UseCases;

use App\CustomerBC\Domain\Repositories\CustomerFinderById;
use App\LoanBC\Domain\Repositories\LoanFinderAll;
use App\LoanBC\Domain\ValueObjects\LoanStatus;
use App\PaymentBC\Domain\Repositories\PaymentFinderByLoanId;
use App\ReportBC\Application\DTOs\CollectionAvailabilityResponse;
use App\SharedKernel\Domain\ValueObjects\DateVO;

final class GetCollectionAvailabilityUseCase
{
    public function __construct(
        private readonly LoanFinderAll $loanFinder,
        private readonly CustomerFinderById $customerFinder,
        private readonly PaymentFinderByLoanId $paymentFinder
    ) {}

    public function execute(): array
    {
        $loans = $this->loanFinder->findAll();
        $responses = [];
        $today = DateVO::now();

        foreach ($loans as $loan) {
            if ($loan->getStatus() !== LoanStatus::ACTIVE) {
                continue;
            }

            $customer = $this->customerFinder->findById($loan->getCustomerId());
            if ($customer === null) {
                continue;
            }

            $payments = $this->paymentFinder->findByLoanId($loan->getId());

            $totalInterestPaid = 0;
            foreach ($payments as $payment) {
                $totalInterestPaid += $payment->getInterestPaid()?->getAmount() ?? 0;
            }

            $monthlyInterest = $loan->calculateCurrentInterest()->getAmount();
            $pendingInterest = $monthlyInterest - $totalInterestPaid;

            $nextPaymentDate = $loan->getNextPaymentDate();
            $isOverdue = $today->isAfter($nextPaymentDate);

            $status = match (true) {
                $pendingInterest <= 0 => 'current',
                $isOverdue => 'interest_default',
                $pendingInterest > 0 => 'interest_pending',
                default => 'unknown'
            };

            $responses[] = new CollectionAvailabilityResponse(
                loanId: $loan->getId()->getValue(),
                customerId: $loan->getCustomerId()->getValue(),
                customerName: $customer->getPersonalData()->getName()->getFullName(),
                interestDueDate: $nextPaymentDate->getFormatted(),
                currentDate: $today->getFormatted(),
                pendingInterest: $pendingInterest,
                status: $status
            );
        }

        return $responses;
    }
}
