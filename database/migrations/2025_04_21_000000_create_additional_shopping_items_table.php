<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('additional_shopping_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained();
            $table->foreignId('shopping_tour_id')->nullable()->constrained();
            $table->string('title');
            $table->double('quantity');
            $table->unsignedSmallInteger('unit');
            $table->unsignedSmallInteger('category')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('additional_shopping_items');
    }
};
