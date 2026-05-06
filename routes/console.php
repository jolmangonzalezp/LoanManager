<?php

use App\LoanBC\Application\Jobs\CheckOverdueLoans;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule the overdue check job to run daily at midnight
Schedule::job(new CheckOverdueLoans())->daily();
