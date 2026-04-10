<?php

declare(strict_types=1);

namespace App\LoanBC\Application\UseCases;

use App\LoanBC\Application\DTOs\LoanResponse;
use App\LoanBC\Domain\Repositories\LoanFinderAll;

final class GetAllLoansUseCase
{
    public function __construct(
        private readonly LoanFinderAll $finder
    ) {}

    public function execute(): array
    {
        $loans = $this->finder->findAll();

        return array_map(
            fn ($loan) => LoanResponse::fromEntity($loan),
            $loans
        );
    }
}
