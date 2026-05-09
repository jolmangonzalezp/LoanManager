<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('id');
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('name')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->dropColumn('email_verified_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
            $table->dropColumn('phone');
            $table->string('name')->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
            $table->timestamp('email_verified_at')->nullable()->after('email');
        });
    }
};
