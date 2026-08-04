<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meals', function (Blueprint $table) {
            $table->unsignedInteger('position')->default(0);
        });

        // One-off copy of the old hardcoded meal-name sort, so existing plans keep their order.
        $nameOrder = [
            'Frühstück' => 1,
            'Mittag' => 2,
            'Mittagessen' => 2,
            'Abend' => 3,
            'Abendessen' => 3,
        ];

        DB::table('meals')
            ->get()
            ->groupBy(fn ($meal) => $meal->event_id.'|'.$meal->date)
            ->each(function ($meals) use ($nameOrder) {
                $meals
                    ->sortBy(fn ($meal) => [$nameOrder[$meal->title] ?? 4, $meal->title])
                    ->values()
                    ->each(function ($meal, $index) {
                        DB::table('meals')->where('id', $meal->id)->update(['position' => $index + 1]);
                    });
            });
    }

    public function down(): void
    {
        Schema::table('meals', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
};
