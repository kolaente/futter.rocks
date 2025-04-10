<?php

namespace App\Utils;

use App\Models\Enums\Unit;

class RoundIngredients
{
    public static function round(array $item): array
    {
        if (
            $item['ingredient']->unit === Unit::Grams ||
            $item['ingredient']->unit === Unit::Milliliters
        ) {
            if ($item['quantity'] > 1000) {
                $item['quantity'] /= 1000;
                $item['quantity'] = round($item['quantity'], 1);

                if ($item['ingredient']->unit === Unit::Grams) {
                    $item['ingredient']->unit = Unit::Kilos;
                }
                if ($item['ingredient']->unit === Unit::Milliliters) {
                    $item['ingredient']->unit = Unit::Liters;
                }
            } else if ($item['quantity'] > 100) {
                $item['quantity'] = round($item['quantity'], -1);
            }
        }

        if ($item['ingredient']->unit === Unit::Pieces) {
            if($item['quantity'] > 5) {
                $item['quantity'] = ceil($item['quantity'] * 2) / 2;
            } else {
                $item['quantity'] = round($item['quantity'], 1);
            }
        }

        return $item;
    }
}
