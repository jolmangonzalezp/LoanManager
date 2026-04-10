<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->unsignedBigInteger('original_capital');
            $table->unsignedBigInteger('capital');
            $table->unsignedBigInteger('remaining_debt');
            $table->unsignedBigInteger('paid_capital')->default(0);
            $table->unsignedBigInteger('paid_interest')->default(0);
            $table->decimal('interest_rate', 5, 4);
            $table->date('start_date');
            $table->date('due_date');
            $table->date('next_payment_date');
            $table->enum('status', ['active', 'paid', 'defaulted', 'cancelled'])->default('active');
            $table->timestamps();

            $table->index('customer_id');
            $table->index('status');
            $table->index('next_payment_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
