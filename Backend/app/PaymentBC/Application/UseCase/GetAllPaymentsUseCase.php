<?php

declare(strict_types=1);

namespace App\PaymentBC\Application\UseCase;

use App\LoanBC\Domain\Repository\CustomerNameProvider;
use App\LoanBC\Infrastructure\Persistence\Model\LoanModel;
use App\PaymentBC\Application\DTO\PaymentResponse;
use App\PaymentBC\Domain\Repository\PaymentFinderAll;

final class GetAllPaymentsUseCase
{
    public function __construct(
        private readonly PaymentFinderAll $finder,
        private readonly CustomerNameProvider $customerNameProvider
    ) {}

    public function execute(): array
    {
        $payments = $this->finder->findAll();

        if (empty($payments)) {
            return [];
        }

        $loanIds = array_unique(array_map(fn ($p) => $p->getLoanId()->getValue(), $payments));

        $loanCustomerIds = LoanModel::whereIn('id', $loanIds)
            ->pluck('customer_id', 'id')
            ->toArray();

        $customerIds = array_unique(array_values($loanCustomerIds));
        $namesMap = $this->customerNameProvider->getNamesMap($customerIds);

        return array_map(function ($payment) use ($loanCustomerIds, $namesMap) {
            $response = PaymentResponse::fromEntity($payment);
            $loanId = $payment->getLoanId()->getValue();

            if (isset($loanCustomerIds[$loanId]) && isset($namesMap[$loanCustomerIds[$loanId]])) {
                $response->customerName = $namesMap[$loanCustomerIds[$loanId]];
            }

            return $response->toArray();
        }, $payments);
    }
}
