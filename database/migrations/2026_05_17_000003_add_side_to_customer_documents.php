<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $hasFk = !empty(DB::select(
            "SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
             WHERE TABLE_NAME = 'customer_documents'
             AND CONSTRAINT_TYPE = 'FOREIGN KEY'
             AND CONSTRAINT_NAME LIKE '%customer_id%'"
        ));

        $hasUnique = !empty(DB::select(
            "SELECT INDEX_NAME FROM INFORMATION_SCHEMA.STATISTICS
             WHERE TABLE_NAME = 'customer_documents'
             AND INDEX_NAME = 'customer_documents_customer_id_type_unique'"
        ));

        Schema::table('customer_documents', function (Blueprint $table) use ($hasFk, $hasUnique) {
            if ($hasFk) {
                $table->dropForeign(['customer_id']);
            }

            if ($hasUnique) {
                $table->dropUnique(['customer_id', 'type']);
            }

            if (!Schema::hasColumn('customer_documents', 'side')) {
                $table->string('side', 10)->nullable()->after('type');
            }

            $table->unique(['customer_id', 'type', 'side']);
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        $hasFk = !empty(DB::select(
            "SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
             WHERE TABLE_NAME = 'customer_documents'
             AND CONSTRAINT_TYPE = 'FOREIGN KEY'
             AND CONSTRAINT_NAME LIKE '%customer_id%'"
        ));

        $hasNewUnique = !empty(DB::select(
            "SELECT INDEX_NAME FROM INFORMATION_SCHEMA.STATISTICS
             WHERE TABLE_NAME = 'customer_documents'
             AND INDEX_NAME = 'customer_documents_customer_id_type_side_unique'"
        ));

        Schema::table('customer_documents', function (Blueprint $table) use ($hasFk, $hasNewUnique) {
            if ($hasFk) {
                $table->dropForeign(['customer_id']);
            }

            if ($hasNewUnique) {
                $table->dropUnique(['customer_id', 'type', 'side']);
            }

            if (Schema::hasColumn('customer_documents', 'side')) {
                $table->dropColumn('side');
            }

            $table->unique(['customer_id', 'type']);
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }
};
