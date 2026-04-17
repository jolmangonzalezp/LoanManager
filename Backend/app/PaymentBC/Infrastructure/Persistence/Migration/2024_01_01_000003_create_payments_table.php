<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('loan_id');
            $table->unsignedBigInteger('amount');
            $table->dateTime('payment_date');
            $table->unsignedBigInteger('interest_paid')->default(0);
            $table->unsignedBigInteger('capital_paid')->default(0);
            $table->enum('status', ['pending', 'validated', 'applied', 'rejected', 'refunded'])->default('pending');
            $table->timestamps();

            $table->index('loan_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
