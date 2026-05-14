<?php

declare(strict_types=1);

namespace App\LoanBC\Application\UseCase;

use App\LoanBC\Application\DTO\LoanResponse;
use App\LoanBC\Application\Exception\LoanNotFoundException;
use App\LoanBC\Domain\Repository\CustomerNameProvider;
use App\LoanBC\Domain\Repository\LoanFinderAll;
use App\LoanBC\Infrastructure\Persistence\Model\LoanModel;
use App\LoanBC\Infrastructure\Persistence\Model\LoanTypeModel;
use Illuminate\Support\Facades\Log;

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

        $loanTypeIds = array_unique(array_map(fn ($l) => $l->getLoanTypeId()?->getValue(), $loans));
        $loanTypesMap = LoanTypeModel::whereIn('id', array_filter($loanTypeIds))
            ->pluck('name', 'id')
            ->toArray();

        return array_map(function ($loan) use ($namesMap, $loanNumbersMap, $loanTypesMap) {
            $loanTypeName = $loanTypesMap[$loan->getLoanTypeId()?->getValue()] ?? null;
            $response = LoanResponse::fromEntity($loan, $loanTypeName);
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
