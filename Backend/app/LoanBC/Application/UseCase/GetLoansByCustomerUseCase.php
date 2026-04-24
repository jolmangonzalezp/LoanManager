<?php

declare(strict_types=1);

namespace App\LoanBC\Application\UseCase;

use App\LoanBC\Application\DTO\LoanSummaryResponse;
use App\LoanBC\Domain\Repository\LoanFinderByCustomerId;
use App\LoanBC\Infrastructure\Persistence\Model\LoanModel;

final class GetLoansByCustomerUseCase
{
    public function __construct(
        private readonly LoanFinderByCustomerId $loanFinder
    ) {}

    public function execute(string $customerId): array
    {
        $customerIdVO = \App\CustomerBC\Domain\ValueObject\CustomerIdVO::fromString($customerId);
        $loans = $this->loanFinder->findByCustomerId($customerIdVO);

        if (empty($loans)) {
            return [];
        }

        $loanNumbersMap = LoanModel::where('customer_id', $customerId)
            ->pluck('loan_number', 'id')
            ->toArray();

        return array_map(function ($loan) use ($loanNumbersMap) {
            $loanId = $loan->getId()->getValue();
            $loanNumber = $loanNumbersMap[$loanId] ?? '';
            return LoanSummaryResponse::fromEntity($loan, $loanNumber)->toArray();
        }, $loans);
    }
}