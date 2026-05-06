<?php

declare(strict_types=1);

namespace App\LoanBC\Application\UseCase;

use App\CustomerBC\Application\Exceptions\CustomerNotFoundException;
use App\CustomerBC\Domain\Repository\CustomerFinderById;
use App\LoanBC\Application\DTO\LoanResponse;
use App\LoanBC\Domain\Repository\CustomerNameProvider;
use App\LoanBC\Domain\Repository\LoanFinderById;
use App\LoanBC\Domain\Repository\LoanUpdater;
use App\LoanBC\Domain\ValueObject\LoanIdVO;
use App\LoanBC\Infrastructure\Mapper\LoanMapper;
use App\LoanBC\Infrastructure\Persistence\Model\LoanModel;

final class UpdateLoanUseCase
{
    public function __construct(
        private readonly LoanFinderById $finder,
        private readonly LoanUpdater $updater,
        private readonly LoanMapper $mapper,
        private readonly CustomerFinderById $customerFinder,
        private readonly CustomerNameProvider $customerNameProvider
    ) {}

    public function execute(string $id, array $data): LoanResponse
    {
        $loan = $this->finder->findById(LoanIdVO::fromString($id));

        if ($loan === null) {
            throw new CustomerNotFoundException($id);
        }

        $loanModel = LoanModel::where('id', $id)->first();
        if (! $loanModel) {
            throw new CustomerNotFoundException($id);
        }

        if (isset($data['capital'])) {
            $loanModel->capital = (int) $data['capital'];
            $loanModel->original_capital = (int) $data['capital'];
            $loanModel->remaining_debt = (int) $data['capital'];
        }

        if (isset($data['interest_rate'])) {
            $loanModel->interest_rate = (float) $data['interest_rate'];
        }

        if (isset($data['start_date'])) {
            $loanModel->start_date = $data['start_date'];
        }

        if (isset($data['status'])) {
            $loanModel->status = $data['status'];
        }

        $loanModel->save();

        $updatedLoan = $this->finder->findById(LoanIdVO::fromString($id));
        $response = LoanResponse::fromEntity($updatedLoan);

        $response->setLoanNumber($loanModel->loan_number);

        $namesMap = $this->customerNameProvider->getNamesMap([$loanModel->customer_id]);
        if (isset($namesMap[$loanModel->customer_id])) {
            $response->setCustomerName($namesMap[$loanModel->customer_id]);
        }

        return $response;
    }
}
