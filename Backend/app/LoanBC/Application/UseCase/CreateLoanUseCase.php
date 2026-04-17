<?php

namespace App\LoanBC\Application\UseCase;

use App\CustomerBC\Application\Exceptions\CustomerNotFoundException;
use App\CustomerBC\Domain\Repository\CustomerFinderById;
use App\LoanBC\Application\CQRS\Command\CreateLoanCommand;
use App\LoanBC\Application\DTO\LoanResponse;
use App\LoanBC\Domain\Aggregate\Loan;
use App\LoanBC\Domain\Repository\LoanCreator;
use App\LoanBC\Domain\Services\LoanNumberGenerator;

final class CreateLoanUseCase
{
    public function __construct(
        private readonly LoanCreator $creator,
        private readonly CustomerFinderById $customerFinder,
        private readonly LoanNumberGenerator $loanNumberGenerator
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

        $response = LoanResponse::fromEntity($loan);
        $response->loanNumber = $this->loanNumberGenerator->generate();

        return $response;
    }
}
