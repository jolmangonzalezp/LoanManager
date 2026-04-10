<?php

declare(strict_types=1);

namespace App\LoanBC\Application\UseCases;

use App\CustomerBC\Application\Exceptions\CustomerNotFoundException;
use App\LoanBC\Application\DTOs\LoanResponse;
use App\LoanBC\Domain\Repositories\LoanFinderById;
use App\LoanBC\Domain\ValueObjects\LoanIdVO;

final class GetLoanByIdUseCase
{
    public function __construct(
        private readonly LoanFinderById $finder
    ) {}

    public function execute(string $id): LoanResponse
    {
        $loan = $this->finder->findById(LoanIdVO::fromString($id));

        if ($loan === null) {
            throw new CustomerNotFoundException($id);
        }

        return LoanResponse::fromEntity($loan);
    }
}
