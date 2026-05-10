<?php

declare(strict_types=1);

namespace App\LoanBC\Application\Jobs;

use App\LoanBC\Domain\Repository\LoanFinderAll;
use App\LoanBC\Domain\Repository\LoanUpdater;
use App\LoanBC\Domain\ValueObject\LoanStatus;
use App\SharedKernel\Domain\ValueObject\DateVO;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class CheckOverdueLoans implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue;

    public function __construct() {}

    public function handle(
        LoanFinderAll $loanFinder,
        LoanUpdater $loanUpdater
    ): void {
        $today = DateVO::now();
        $overdueCount = 0;

        // Get all active loans
        $activeLoans = $loanFinder->findAllByStatus(LoanStatus::ACTIVE);

        foreach ($activeLoans as $loan) {
            $dueDate = $loan->getDueDate();

            // Check if due date is past
            if ($dueDate->isBefore($today) || $dueDate->isPast()) {
                $updatedLoan = $loan->markAsDefaulted();

                $loanUpdater->update($updatedLoan);
                $overdueCount++;
            }
        }

        Log::info("CheckOverdueLoans: {$overdueCount} loans marked as defaulted");
    }
}
