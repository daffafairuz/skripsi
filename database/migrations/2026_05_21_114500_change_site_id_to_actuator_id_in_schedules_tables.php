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
        Schema::table('feed_schedules', function (Blueprint $table) {
            $table->dropForeign(['site_id']);
            $table->dropColumn('site_id');
            $table->foreignId('actuator_id')->after('id')->constrained('actuators')->onDelete('cascade');
        });

        Schema::table('grow_light_schedules', function (Blueprint $table) {
            $table->dropForeign(['site_id']);
            $table->dropColumn('site_id');
            $table->foreignId('actuator_id')->after('id')->constrained('actuators')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feed_schedules', function (Blueprint $table) {
            $table->dropForeign(['actuator_id']);
            $table->dropColumn('actuator_id');
            $table->foreignId('site_id')->after('id')->constrained('sites')->onDelete('cascade');
        });

        Schema::table('grow_light_schedules', function (Blueprint $table) {
            $table->dropForeign(['actuator_id']);
            $table->dropColumn('actuator_id');
            $table->foreignId('site_id')->after('id')->constrained('sites')->onDelete('cascade');
        });
    }
};
