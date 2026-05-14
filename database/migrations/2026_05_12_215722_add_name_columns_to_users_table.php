<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('name');
            $table->string('middle_name')->nullable()->after('first_name');
            $table->string('last_name')->nullable()->after('middle_name');
            $table->string('second_last_name')->nullable()->after('last_name');
        });

        DB::table('users')->whereNotNull('name')->get()->each(function ($user) {
            $parts = array_values(array_filter(explode(' ', trim($user->name))));
            $count = count($parts);

            if ($count === 0) return;

            $firstName = $parts[0];
            $middleName = null;
            $lastName = $count >= 2 ? $parts[$count - 2] : null;
            $secondLastName = $count >= 2 ? $parts[$count - 1] : null;

            if ($count > 2) {
                $middleParts = array_slice($parts, 1, $count - 2);
                $middleName = !empty($middleParts) ? implode(' ', $middleParts) : null;
            }

            DB::table('users')->where('id', $user->id)->update([
                'first_name' => $firstName,
                'middle_name' => $middleName,
                'last_name' => $lastName,
                'second_last_name' => $secondLastName,
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
        });

        DB::table('users')->get()->each(function ($user) {
            $parts = array_filter([
                $user->first_name,
                $user->middle_name,
                $user->last_name,
                $user->second_last_name,
            ]);
            $fullName = implode(' ', $parts);
            DB::table('users')->where('id', $user->id)->update(['name' => $fullName ?: null]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'middle_name', 'last_name', 'second_last_name']);
        });
    }
};
