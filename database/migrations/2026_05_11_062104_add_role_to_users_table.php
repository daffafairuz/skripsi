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

            if (!Schema::hasColumn('users', 'phone_number')) {
                $table->string('phone_number')
                    ->nullable()
                    ->after('email');
            }

            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'user'])
                    ->default('user')
                    ->after('phone_number');
            }

            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['active', 'inactive'])
                    ->default('active')
                    ->after('role');
            }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn([
                'phone_number',
                'role',
                'status'
            ]);

        });
    }
};