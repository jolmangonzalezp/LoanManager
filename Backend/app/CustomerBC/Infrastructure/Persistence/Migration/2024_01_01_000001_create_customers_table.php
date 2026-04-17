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
            $table->string('first_name');
            $table->string('last_name');
            $table->string('second_last_name');
            $table->string('middle_name')->nullable();
            $table->string('dni_number');
            $table->string('dni_hash');
            $table->string('dni_type', 20);
            $table->string('phone_number');
            $table->string('phone_country_code', 10)->default('+57');
            $table->text('address');
            $table->string('email')->nullable();
            $table->boolean('enabled')->default(true);
            $table->timestamps();

            $table->unique(['dni_hash', 'dni_type']);
            $table->index('email');
            $table->index('enabled');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};