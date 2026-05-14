<?php

declare(strict_types=1);

namespace App\LoanBC\Application\UseCase;

use App\CustomerBC\Application\Exceptions\CustomerNotFoundException;
use App\LoanBC\Application\DTO\LoanResponse;
use App\LoanBC\Domain\Repository\CustomerNameProvider;
use App\LoanBC\Domain\Repository\LoanFinderById;
use App\LoanBC\Domain\ValueObject\LoanIdVO;
use App\LoanBC\Infrastructure\Persistence\Model\LoanModel;
use App\LoanBC\Infrastructure\Persistence\Model\LoanTypeModel;

final readonly class GetLoanByIdUseCase
{
    public function __construct(
        private LoanFinderById $finder,
        private CustomerNameProvider $customerNameProvider
    ) {}

    public function execute(string $id): array
    {
        $loan = $this->finder->findById(LoanIdVO::fromString($id));

        if ($loan === null) {
            throw new CustomerNotFoundException($id);
        }

        $loanTypeId = $loan->getLoanTypeId()?->getValue();
        $loanTypeName = $loanTypeId
            ? LoanTypeModel::where('id', $loanTypeId)->value('name')
            : null;

        $response = LoanResponse::fromEntity($loan, $loanTypeName);

        $loanNumber = LoanModel::where('id', $id)->value('loan_number');
        if ($loanNumber) {
            $response->setLoanNumber($loanNumber);
        }

        $customerId = $loan->getCustomerId()->getValue();
        $namesMap = $this->customerNameProvider->getNamesMap([$customerId]);
        if (isset($namesMap[$customerId])) {
            $response->setCustomerName($namesMap[$customerId]);
        }

        return $response->toArray($loan->getCustomerId()->getValue());
    }
}
