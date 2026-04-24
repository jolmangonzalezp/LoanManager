<?php

declare(strict_types=1);

namespace App\LoanBC\Application\UseCase;

use App\LoanBC\Application\DTO\LoanResponse;
use App\LoanBC\Domain\Repository\CustomerNameProvider;
use App\LoanBC\Domain\Repository\LoanFinderAll;
use App\LoanBC\Infrastructure\Persistence\Model\LoanModel;

final class GetAllLoansUseCase
{
    public function __construct(
        private readonly LoanFinderAll $loanFinder,
        private readonly CustomerNameProvider $customerProvider
    ) {}

    public function execute(): array
    {
        $loans = $this->loanFinder->findAll();

        if (empty($loans)) {
            return [];
        }

        $customerIds = array_unique(array_map(fn ($l) => $l->getCustomerId()->getValue(), $loans));

        $namesMap = $this->customerProvider->getNamesMap($customerIds);

        $loanNumbersMap = LoanModel::whereIn('id', array_map(fn ($l) => $l->getId()->getValue(), $loans))
            ->pluck('loan_number', 'id')
            ->toArray();

        return array_map(function ($loan) use ($namesMap, $loanNumbersMap) {
            $response = LoanResponse::fromEntity($loan);
            $cid = $loan->getCustomerId()->getValue();
            $loanId = $loan->getId()->getValue();

            if (isset($namesMap[$cid])) {
                $response->setCustomerName($namesMap[$cid]);
            }

            if (isset($loanNumbersMap[$loanId])) {
                $response->setLoanNumber($loanNumbersMap[$loanId]);
            }

            return $response->toArray($cid);
        }, $loans);
    }
}