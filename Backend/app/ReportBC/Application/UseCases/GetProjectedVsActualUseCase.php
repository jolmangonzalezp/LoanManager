<?php

declare(strict_types=1);

namespace App\ReportBC\Application\UseCases;

use App\CustomerBC\Domain\Repositories\CustomerFinderById;
use App\LoanBC\Domain\Repositories\LoanFinderAll;
use App\PaymentBC\Domain\Repositories\PaymentFinderByLoanId;
use App\ReportBC\Application\DTOs\ProjectedVsActualResponse;

final class GetProjectedVsActualUseCase
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

        foreach ($loans as $loan) {
            $customer = $this->customerFinder->findById($loan->getCustomerId());
            if ($customer === null) {
                continue;
            }

            $customerName = $customer->getPersonalData()->getName()->getFullName();
            $payments = $this->paymentFinder->findByLoanId($loan->getId());

            $previousBalance = $loan->getOriginalCapital()->getAmount();
            $monthlyInterest = $loan->calculateCurrentInterest()->getAmount();
            $totalPayment = 0;
            $interestPaid = 0;
            $capitalPaid = 0;

            foreach ($payments as $payment) {
                $totalPayment += $payment->getAmount()->getAmount();
                $interestPaid += $payment->getInterestPaid()?->getAmount() ?? 0;
                $capitalPaid += $payment->getCapitalPaid()?->getAmount() ?? 0;
            }

            $newBalance = $loan->getCapital()->getAmount();
            $projectedBalance = $previousBalance + $monthlyInterest - $totalPayment;
            $difference = $newBalance - $projectedBalance;

            $responses[] = new ProjectedVsActualResponse(
                loanId: $loan->getId()->getValue(),
                customerId: $loan->getCustomerId()->getValue(),
                customerName: (string) $customerName,
                previousBalance: $previousBalance,
                monthlyInterest: $monthlyInterest,
                totalPayment: $totalPayment,
                newBalance: $newBalance,
                interestPaid: $interestPaid,
                capitalPaid: $capitalPaid,
                difference: $difference
            );
        }

        return $responses;
    }
}
