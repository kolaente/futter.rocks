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
        Schema::create('participant_groups', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->double('food_factor')->default(1);
            $table->foreignId('team')->constrained();
            $table->timestamps();
        });
        Schema::create('event_participant_group', function (Blueprint $table) {
            $table->foreignId('participant_group_id')->constrained();
            $table->foreignId('event_id')->constrained();
            $table->integer('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participant_groups');
    }
};
