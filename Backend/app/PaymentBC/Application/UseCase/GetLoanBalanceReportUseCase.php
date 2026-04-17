<?php

declare(strict_types=1);

namespace App\PaymentBC\Application\UseCase;

use App\LoanBC\Domain\Repository\CustomerNameProvider;
use App\LoanBC\Domain\Repository\LoanFinderAll;
use App\LoanBC\Infrastructure\Persistence\Model\LoanModel;
use App\PaymentBC\Application\DTO\LoanBalanceReportResponse;
use App\PaymentBC\Domain\Repository\PaymentFinderAll;

final class GetLoanBalanceReportUseCase
{
    public function __construct(
        private readonly LoanFinderAll $loanFinder,
        private readonly PaymentFinderAll $paymentFinder,
        private readonly CustomerNameProvider $customerNameProvider
    ) {}

    public function execute(): array
    {
        $loans = $this->loanFinder->findAll();

        $loanIds = array_map(fn ($l) => $l->getId()->getValue(), $loans);
        $loanModels = LoanModel::whereIn('id', $loanIds)->get()->keyBy('id');

        $customerIds = array_unique(array_map(fn ($l) => $l->getCustomerId()->getValue(), $loans));
        $namesMap = $this->customerNameProvider->getNamesMap($customerIds);

        $responses = [];

        foreach ($loans as $loan) {
            $customerId = $loan->getCustomerId()->getValue();
            $loanId = $loan->getId()->getValue();

            $loanModel = $loanModels[$loanId] ?? null;
            $loanNumber = $loanModel?->loan_number ?? '-';
            $customerName = $namesMap[$customerId] ?? '-';

            $originalCapital = $loan->getOriginalCapital()->getAmount();
            $actualBalance = $loan->getRemainingDebt()->getAmount();
            $capitalPaid = $loan->getPaidCapital()->getAmount();
            $interestPaid = $loan->getPaidInterest()->getAmount();

            $projectedBalance = $originalCapital - $capitalPaid;
            $difference = $actualBalance - $projectedBalance;

            $responses[] = new LoanBalanceReportResponse(
                loanId: $loanId,
                loanNumber: $loanNumber,
                customerName: $customerName,
                originalCapital: $originalCapital,
                projectedBalance: $projectedBalance,
                actualBalance: $actualBalance,
                capitalPaid: $capitalPaid,
                interestPaid: $interestPaid,
                difference: $difference,
                status: $loan->getStatus()->value
            );
        }

        return $responses;
    }
}
