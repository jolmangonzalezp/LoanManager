<?php

namespace App\LoanBC\Application\UseCase;

use App\CustomerBC\Application\Exceptions\CustomerNotFoundException;
use App\CustomerBC\Domain\Repository\CustomerFinderById;
use App\LoanBC\Application\CQRS\Command\CreateLoanCommand;
use App\LoanBC\Application\DTO\LoanResponse;
use App\LoanBC\Domain\Aggregate\Loan;
use App\LoanBC\Domain\Repository\LoanCreator;
use App\LoanBC\Domain\Services\LoanNumberGenerator;
use App\LoanBC\Domain\ValueObject\LoanTypeIdVO;
use App\LoanBC\Infrastructure\Persistence\Model\LoanTypeModel;
use Ramsey\Uuid\Uuid;

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

        $loanTypeId = $command->loanTypeId;
        $resolvedLoanTypeName = null;
        if ($loanTypeId === null && $command->loanTypeName !== null) {
            $type = LoanTypeModel::findByName($command->loanTypeName);
            if ($type === null) {
                $type = LoanTypeModel::create([
                    'id' => Uuid::uuid7()->toString(),
                    'name' => $command->loanTypeName,
                ]);
            }
            $loanTypeId = LoanTypeIdVO::fromString($type->id);
            $resolvedLoanTypeName = $type->name;
        }

        $loan = Loan::create(
            $command->customerId,
            $loanTypeId,
            $command->capital,
            $command->interestRate,
            $command->startDate,
            $dueDate
        );

        $this->creator->create($loan);

        $dto = LoanResponse::fromEntity($loan, $resolvedLoanTypeName);
        $dto->setLoanNumber($this->loanNumberGenerator->generate());
        $this->response = $dto->toArray($loan->getCustomerId()->getValue());

        return true;
    }
}
