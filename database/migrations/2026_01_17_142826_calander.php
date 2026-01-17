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
        Schema::create('opening_rules', function (Blueprint $table) {
            $table->id();
            $table->string('day_of_week'); // MON, TUE, etc.
            $table->time('opens_at');
            $table->time('closes_at');
            $table->integer('slot_duration_minutes')->default(30);
            $table->integer('buffer_before')->default(0);
            $table->integer('buffer_after')->default(0);
            $table->timestamps();
        });

        Schema::create('blocked_periods', function (Blueprint $table) {
            $table->id();
            $table->timestamp('starts_at'); // UTC
            $table->timestamp('ends_at');   // UTC
            $table->string('reason')->nullable(); // e.g., “Lunch”, “Maintenance”
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opening_rules');
        Schema::dropIfExists('blocked_periods');
    }
};
