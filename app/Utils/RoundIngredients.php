<?php

namespace App\Utils;

use App\Models\Enums\Unit;

class RoundIngredients
{
    public static function round(array $item): array
    {
        if (
            $item['unit'] === Unit::Grams ||
            $item['unit'] === Unit::Milliliters
        ) {
            if ($item['quantity'] > 1000) {
                $item['quantity'] /= 1000;
                $item['quantity'] = round($item['quantity'], 1);

                if ($item['unit'] === Unit::Grams) {
                    $item['unit'] = Unit::Kilos;
                }
                if ($item['unit'] === Unit::Milliliters) {
                    $item['unit'] = Unit::Liters;
                }
            } elseif ($item['quantity'] > 100) {
                $item['quantity'] = round($item['quantity'], -1);
            }
        }

        if ($item['unit'] === Unit::Pieces) {
            if ($item['quantity'] > 5) {
                $item['quantity'] = ceil($item['quantity'] * 2) / 2;
            } else {
                $item['quantity'] = round($item['quantity'], 1);
            }
        }

        return $item;
    }
}
