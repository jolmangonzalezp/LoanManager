<?php

declare(strict_types=1);

namespace App\LoanBC\Application\UseCases;

use App\CustomerBC\Application\Exceptions\CustomerNotFoundException;
use App\CustomerBC\Domain\Repositories\CustomerFinderById;
use App\LoanBC\Application\Commands\CreateLoanCommand;
use App\LoanBC\Application\DTOs\LoanResponse;
use App\LoanBC\Domain\Entities\Loan;
use App\LoanBC\Domain\Repositories\LoanCreator;

final class CreateLoanUseCase
{
    public function __construct(
        private readonly LoanCreator $creator,
        private readonly CustomerFinderById $customerFinder
    ) {}

    public function execute(CreateLoanCommand $command): LoanResponse
    {
        $customer = $this->customerFinder->findById($command->customerId);

        if ($customer === null) {
            throw new CustomerNotFoundException($command->customerId->getValue());
        }

        $loan = Loan::create(
            $command->customerId,
            $command->capital,
            $command->interestRate,
            $command->startDate,
            $command->dueDate
        );

        $this->creator->create($loan);

        return LoanResponse::fromEntity($loan);
    }
}
