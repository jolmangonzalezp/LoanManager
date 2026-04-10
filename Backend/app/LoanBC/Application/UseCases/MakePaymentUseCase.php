<?php

declare(strict_types=1);

namespace App\LoanBC\Application\UseCases;

use App\CustomerBC\Application\Exceptions\CustomerNotFoundException;
use App\LoanBC\Application\Commands\MakePaymentCommand;
use App\LoanBC\Application\DTOs\LoanResponse;
use App\LoanBC\Domain\Repositories\LoanFinderById;
use App\LoanBC\Domain\Repositories\LoanUpdater;

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
