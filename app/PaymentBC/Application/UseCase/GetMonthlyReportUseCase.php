<?php

declare(strict_types=1);

namespace App\PaymentBC\Application\UseCase;

use App\PaymentBC\Application\DTO\MonthlyReportResponse;
use App\PaymentBC\Domain\Repository\PaymentFinderAll;

final class GetMonthlyReportUseCase
{
    public function __construct(
        private readonly PaymentFinderAll $finder
    ) {}

    public function execute(): MonthlyReportResponse
    {
        $payments = $this->finder->findAll();

        $now = new \DateTime;
        $currentMonth = (int) $now->format('n');
        $currentYear = (int) $now->format('Y');

        $capitalReturned = 0;
        $interestCollected = 0;
        $paymentsCount = 0;

        foreach ($payments as $payment) {
            $paymentDate = $payment->getPaymentDate()->getValue();
            $paymentMonth = (int) $paymentDate->format('n');
            $paymentYear = (int) $paymentDate->format('Y');

            if ($paymentMonth === $currentMonth && $paymentYear === $currentYear) {
                $capitalReturned += $payment->getCapitalPaid()?->getAmount() ?? 0;
                $interestCollected += $payment->getInterestPaid()?->getAmount() ?? 0;
                $paymentsCount++;
            }
        }

        return new MonthlyReportResponse(
            capitalReturned: $capitalReturned,
            interestCollected: $interestCollected,
            paymentsCount: $paymentsCount,
            month: $now->format('F'),
            year: (string) $currentYear
        );
    }
}
