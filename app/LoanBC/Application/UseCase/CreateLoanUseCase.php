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
    private ?array $response = null;

    public function __construct(
        private LoanCreator $creator,
        private CustomerFinderById $customerFinder,
        private LoanNumberGenerator $loanNumberGenerator
    ) {}

    public function getResponse(): ?array
    {
        return $this->response;
    }

    public function execute(CreateLoanCommand $command): bool
    {
        $customer = $this->customerFinder->findById($command->customerId);

        if ($customer === null) {
            throw new CustomerNotFoundException($command->customerId->getValue());
        }

        $dueDate = $command->startDate->addMonths($command->term);

        $loan = Loan::create(
            $command->customerId,
            $command->capital,
            $command->interestRate,
            $command->startDate,
            $dueDate
        );

        $this->creator->create($loan);

        $dto = LoanResponse::fromEntity($loan);
        $dto->setLoanNumber($this->loanNumberGenerator->generate());
        $this->response = $dto->toArray($loan->getCustomerId()->getValue());

        return true;
    }
}
