<?php

declare(strict_types=1);

namespace App\CustomerBC\Application\UseCase;

use App\CustomerBC\Application\DTO\CustomerReportResponse;
use App\CustomerBC\Domain\Repository\CustomerFinderAll;
use App\LoanBC\Domain\Ports\CustomerLoanStatistics;

final class GetCustomerReportUseCase
{
    public function __construct(
        private readonly CustomerFinderAll $finder,
        private readonly CustomerLoanStatistics $loanStatistics
    ) {}

    public function execute(): CustomerReportResponse
    {
        $customers = $this->finder->findAll();
        $totalCustomers = count($customers);
        $totalWithLoan = $this->loanStatistics->getTotalCustomersWithLoan();
        $totalWithoutLoan = $this->loanStatistics->getTotalCustomersWithoutLoan($totalCustomers);

        return new CustomerReportResponse(
            $totalCustomers,
            $totalWithLoan,
            $totalWithoutLoan
        );
    }
}
