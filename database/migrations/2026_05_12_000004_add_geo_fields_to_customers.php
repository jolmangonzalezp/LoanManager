<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('enabled');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->uuid('zone_id')->nullable()->after('longitude');
            $table->uuid('route_id')->nullable()->after('zone_id');

            $table->foreign('zone_id')->references('id')->on('zones')->onDelete('set null');
            $table->foreign('route_id')->references('id')->on('routes')->onDelete('set null');

            $table->index('zone_id');
            $table->index('route_id');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['zone_id']);
            $table->dropForeign(['route_id']);
            $table->dropIndex(['zone_id']);
            $table->dropIndex(['route_id']);
            $table->dropColumn(['latitude', 'longitude', 'zone_id', 'route_id']);
        });
    }
};
