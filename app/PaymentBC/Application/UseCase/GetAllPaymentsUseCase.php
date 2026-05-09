<?php

declare(strict_types=1);

namespace App\PaymentBC\Application\UseCase;

use App\PaymentBC\Application\DTO\PaymentResponse;
use App\PaymentBC\Domain\Ports\LoanDataProvider;
use App\PaymentBC\Domain\Repository\PaymentFinderAll;

final class GetAllPaymentsUseCase
{
    public function __construct(
        private readonly PaymentFinderAll $finder,
        private readonly LoanDataProvider $loanDataProvider
    ) {}

    public function execute(): array
    {
        $payments = $this->finder->findAll();

        if (empty($payments)) {
            return [];
        }

        return array_map(function ($payment) {
            $loanId = $payment->getLoanId()->getValue();

            $response = PaymentResponse::fromEntity($payment);
            $data = $response->toArray();
            unset($data['customer_name']);

            $data['loan'] = [
                'id' => $loanId,
                'loan_number' => $this->loanDataProvider->getLoanNumber($loanId) ?? '',
                'remaining_debt' => $this->loanDataProvider->getRemainingDebt($loanId) ?? 0,
            ];
            $data['loan_number'] = $data['loan']['loan_number'];

            return $data;
        }, $payments);
    }
}