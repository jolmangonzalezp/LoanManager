<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('second_last_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('dni_number')->nullable();
            $table->string('dni_hash')->nullable();
            $table->string('dni_type', 20)->nullable();
            $table->string('phone_number')->nullable();
            $table->string('phone_country_code', 10)->nullable();
            $table->text('address')->nullable();
            $table->string('email')->nullable();
            $table->boolean('enabled')->default(true);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('dni_type');
            $table->index('dni_hash');
            $table->index('email');
            $table->index('enabled');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
