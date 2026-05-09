<?php

declare(strict_types=1);

namespace App\LoanBC\Application\UseCase;

use App\CustomerBC\Domain\Repository\CustomerFinderById;
use App\LoanBC\Application\DTO\LoanResponse;
use App\LoanBC\Application\Exception\LoanNotFoundException;
use App\LoanBC\Domain\Repository\CustomerNameProvider;
use App\LoanBC\Domain\Repository\LoanFinderById;
use App\LoanBC\Domain\Repository\LoanUpdater;
use App\LoanBC\Domain\ValueObject\InterestRateVO;
use App\LoanBC\Domain\ValueObject\LoanIdVO;
use App\LoanBC\Domain\ValueObject\LoanStatus;
use App\LoanBC\Infrastructure\Mapper\LoanMapper;
use App\SharedKernel\Domain\ValueObject\DateVO;
use App\SharedKernel\Domain\ValueObject\MoneyVO;

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
            throw new LoanNotFoundException($id);
        }

        $originalCapital = isset($data['capital'])
            ? MoneyVO::create((int) $data['capital'])
            : $loan->getOriginalCapital();

        $interestRate = isset($data['interest_rate'])
            ? InterestRateVO::createAnnual((float) $data['interest_rate'])
            : $loan->getInterestRate();

        $startDate = isset($data['start_date'])
            ? DateVO::fromString($data['start_date'])
            : $loan->getStartDate();

        $dueDate = isset($data['due_date'])
            ? DateVO::fromString($data['due_date'])
            : $loan->getDueDate();

        $status = isset($data['status'])
            ? LoanStatus::from($data['status'])
            : null;

        $updatedLoan = $loan->update(
            $originalCapital,
            $interestRate,
            $startDate,
            $dueDate,
            $status
        );

        $this->updater->update($updatedLoan);

        $response = LoanResponse::fromEntity($updatedLoan);

        $namesMap = $this->customerNameProvider->getNamesMap([$loan->getCustomerId()->getValue()]);
        if (isset($namesMap[$loan->getCustomerId()->getValue()])) {
            $response->setCustomerName($namesMap[$loan->getCustomerId()->getValue()]);
        }

        return $response;
    }
}
