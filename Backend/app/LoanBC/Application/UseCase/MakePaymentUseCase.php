<?php

declare(strict_types=1);

namespace App\LoanBC\Application\UseCase;

use App\CustomerBC\Application\Exceptions\CustomerNotFoundException;
use App\LoanBC\Application\CQRS\Command\MakePaymentCommand;
use App\LoanBC\Application\DTO\LoanResponse;
use App\LoanBC\Domain\Repository\LoanFinderById;
use App\LoanBC\Domain\Repository\LoanUpdater;

final class MakePaymentUseCase
{
    public function __construct(
        private readonly LoanFinderById $finder,
        private readonly LoanUpdater $updater
    ) {}

    public function execute(MakePaymentCommand $command): LoanResponse
    {
        $loan = $this->finder->findById($command->loanId);

        if ($loan === null) {
            throw new CustomerNotFoundException($command->loanId->getValue());
        }

        $updatedLoan = $loan->makePayment($command->amount);

        $this->updater->update($updatedLoan);

        return LoanResponse::fromEntity($updatedLoan);
    }
}
