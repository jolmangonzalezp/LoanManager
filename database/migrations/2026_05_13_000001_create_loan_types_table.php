<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->uuid('loan_type_id')->nullable()->after('customer_id');
            $table->foreign('loan_type_id')->references('id')->on('loan_types')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropForeign(['loan_type_id']);
            $table->dropColumn('loan_type_id');
        });

        Schema::dropIfExists('loan_types');
    }
};
