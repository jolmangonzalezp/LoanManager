<?php

declare(strict_types=1);

namespace App\LoanBC\Application\UseCase;

use App\CustomerBC\Application\Exceptions\CustomerNotFoundException;
use App\LoanBC\Application\DTO\LoanResponse;
use App\LoanBC\Domain\Repository\CustomerNameProvider;
use App\LoanBC\Domain\Repository\LoanFinderById;
use App\LoanBC\Domain\ValueObject\LoanIdVO;
use App\LoanBC\Infrastructure\Persistence\Model\LoanModel;

final class GetLoanByIdUseCase
{
    public function __construct(
        private readonly LoanFinderById $finder,
        private readonly CustomerNameProvider $customerNameProvider
    ) {}

    public function execute(string $id): LoanResponse
    {
        $loan = $this->finder->findById(LoanIdVO::fromString($id));

        if ($loan === null) {
            throw new CustomerNotFoundException($id);
        }

        $loanModel = LoanModel::where('id', $id)->first();
        $response = LoanResponse::fromEntity($loan);

        if ($loanModel) {
            $response->loanNumber = $loanModel->loan_number;

            $namesMap = $this->customerNameProvider->getNamesMap([$loanModel->customer_id]);
            if (isset($namesMap[$loanModel->customer_id])) {
                $response->customerName = $namesMap[$loanModel->customer_id];
            }
        }

        return $response;
    }
}
