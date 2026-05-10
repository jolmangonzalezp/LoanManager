<?php

declare(strict_types=1);

namespace App\LoanBC\Application\UseCase;

use App\LoanBC\Application\CQRS\Command\MakePaymentCommand;
use App\LoanBC\Application\DTO\LoanResponse;
use App\LoanBC\Application\Exception\LoanNotFoundException;
use App\LoanBC\Domain\Repository\LoanFinderById;
use App\LoanBC\Domain\Repository\LoanUpdater;

final class MakePaymentUseCase
{
    private ?array $response = null;

    public function __construct(
        private readonly LoanFinderById $finder,
        private readonly LoanUpdater $updater
    ) {}

    public function getResponse(): ?array
    {
        return $this->response;
    }

    public function execute(MakePaymentCommand $command): bool
    {
        $loan = $this->finder->findById($command->loanId);

        if ($loan === null) {
            throw new LoanNotFoundException($command->loanId->getValue());
        }

        $updatedLoan = $loan->makePayment($command->amount);

        $this->updater->update($updatedLoan);

        $dto = LoanResponse::fromEntity($updatedLoan);
        $this->response = $dto->toArray($updatedLoan->getCustomerId()->getValue());

        return true;
    }
}
