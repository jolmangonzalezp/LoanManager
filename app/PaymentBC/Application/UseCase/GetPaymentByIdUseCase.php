<?php

declare(strict_types=1);

namespace App\PaymentBC\Application\UseCase;

use App\PaymentBC\Application\DTO\PaymentResponse;
use App\PaymentBC\Domain\Ports\LoanDataProvider;
use App\PaymentBC\Domain\Repository\PaymentFinderById;
use App\PaymentBC\Domain\ValueObject\PaymentIdVO;
use App\SharedKernel\Application\Exception\NotFoundException;

final class GetPaymentByIdUseCase
{
    public function __construct(
        private readonly PaymentFinderById $finder,
        private readonly LoanDataProvider $loanDataProvider
    ) {}

    public function execute(string $id): array
    {
        $payment = $this->finder->findById(PaymentIdVO::fromString($id));

        if ($payment === null) {
            throw new NotFoundException('Pago no encontrado');
        }

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
    }
}