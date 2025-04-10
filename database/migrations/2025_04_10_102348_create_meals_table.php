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
        Schema::create('meals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('date');
            $table->foreignId('event_id')->constrained();
            $table->timestamps();
        });

        Schema::create('meal_recipe', function (Blueprint $table) {
            $table->foreignId('meal_id')->constrained();
            $table->foreignId('recipe_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meals');
    }
};
