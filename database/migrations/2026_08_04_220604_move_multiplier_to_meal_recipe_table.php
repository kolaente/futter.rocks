<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Recreate the pivot instead of altering it: it needs a primary key
        // for the repeater relationship and SQLite can't add one via ALTER.
        Schema::rename('meal_recipe', 'meal_recipe_old');

        Schema::create('meal_recipe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meal_id')->constrained();
            $table->foreignId('recipe_id')->constrained();
            $table->decimal('multiplier', 8, 2)->nullable();
        });

        DB::statement('
            insert into meal_recipe (meal_id, recipe_id, multiplier)
            select mr.meal_id, mr.recipe_id, m.multiplier
            from meal_recipe_old mr
            join meals m on m.id = mr.meal_id
        ');

        Schema::drop('meal_recipe_old');

        Schema::table('meals', function (Blueprint $table) {
            $table->dropColumn('multiplier');
        });
    }
};
