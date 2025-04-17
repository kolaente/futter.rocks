<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ingredient_recipe', function (Blueprint $table) {
            $table->unsignedSmallInteger('unit');
        });

        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropColumn('unit');
        });
    }

    public function down(): void
    {
    }
};
