<?php

declare(strict_types=1);

namespace App\PaymentBC\Application\UseCase;

use App\LoanBC\Infrastructure\Persistence\Model\LoanModel;
use App\PaymentBC\Application\DTO\PaymentResponse;
use App\PaymentBC\Domain\Repository\PaymentFinderById;
use App\PaymentBC\Domain\ValueObject\PaymentIdVO;
use App\SharedKernel\Application\Exception\NotFoundException;

final class GetPaymentByIdUseCase
{
    public function __construct(
        private readonly PaymentFinderById $finder
    ) {}

    public function execute(string $id): array
    {
        $payment = $this->finder->findById(PaymentIdVO::fromString($id));

        if ($payment === null) {
            throw new NotFoundException('Pago no encontrado');
        }

        $loanId = $payment->getLoanId()->getValue();
        $loan = LoanModel::where('id', $loanId)->first();

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
    }
}