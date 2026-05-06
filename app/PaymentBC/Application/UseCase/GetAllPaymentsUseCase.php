<?php

declare(strict_types=1);

namespace App\PaymentBC\Application\UseCase;

use App\LoanBC\Infrastructure\Persistence\Model\LoanModel;
use App\PaymentBC\Application\DTO\PaymentResponse;
use App\PaymentBC\Domain\Repository\PaymentFinderAll;

final class GetAllPaymentsUseCase
{
    public function __construct(
        private readonly PaymentFinderAll $finder
    ) {}

    public function execute(): array
    {
        $payments = $this->finder->findAll();

        if (empty($payments)) {
            return [];
        }

        $loanIds = array_unique(array_map(fn ($p) => $p->getLoanId()->getValue(), $payments));

        $loansMap = LoanModel::whereIn('id', $loanIds)
            ->get()
            ->keyBy('id')
            ->toArray();

        return array_map(function ($payment) use ($loansMap) {
            $loanId = $payment->getLoanId()->getValue();
            $loan = $loansMap[$loanId] ?? null;

            $response = PaymentResponse::fromEntity($payment);
            $data = $response->toArray();
            unset($data['customer_name']);

            $data['loan'] = [
                'id' => $loanId,
                'loan_number' => $loan['loan_number'] ?? '',
                'remaining_debt' => $loan['remaining_debt'] ?? 0,
            ];
            $data['loan_number'] = $loan['loan_number'] ?? '';

            return $data;
        }, $payments);
    }
}