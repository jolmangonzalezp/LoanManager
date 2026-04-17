<?php

declare(strict_types=1);

namespace App\ReportBC\Application\UseCase;

use App\CustomerBC\Domain\Repository\CustomerFinderById;
use App\LoanBC\Domain\Repository\LoanFinderAll;
use App\PaymentBC\Domain\Repository\PaymentFinderByLoanId;
use App\ReportBC\Application\DTO\ClientProfitabilityResponse;

final class GetClientProfitabilityUseCase
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

            $payments = $this->paymentFinder->findByLoanId($loan->getId());

            $initialCapital = $loan->getOriginalCapital()->getAmount();
            $totalInterestPaid = 0;
            $totalCapitalPaid = 0;
            $remainingBalance = $loan->getCapital()->getAmount();

            foreach ($payments as $payment) {
                $totalInterestPaid += $payment->getInterestPaid()?->getAmount() ?? 0;
                $totalCapitalPaid += $payment->getCapitalPaid()?->getAmount() ?? 0;
            }

            $roiPercentage = $initialCapital > 0
                ? round(($totalInterestPaid / $initialCapital) * 100, 2)
                : 0;

            $responses[] = new ClientProfitabilityResponse(
                customerId: $loan->getCustomerId()->getValue(),
                customerName: $customer->getPersonalData()->getName()->getFullName(),
                initialCapital: $initialCapital,
                totalInterestPaid: $totalInterestPaid,
                totalCapitalPaid: $totalCapitalPaid,
                remainingBalance: $remainingBalance,
                roiPercentage: $roiPercentage
            );
        }

        usort($responses, fn ($a, $b) => $b->roiPercentage <=> $a->roiPercentage);

        return $responses;
    }
}
