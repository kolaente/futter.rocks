<?php

namespace App\Models\Enums;

enum Unit: string
{
    case Grams = 'gr';
    case Kilos = 'kg';
    case Milliliters = 'ml';
    case Liters = 'l';
    case Pieces = 'pcs';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Grams => __('Grams'),
            self::Milliliters => __('Milliliters'),
            self::Pieces => __('Pieces'),
            self::Kilos => __('Kilos'),
            self::Liters => __('Liters'),
        };
    }

    public function getShortLabel(): ?string
    {
        return match ($this) {
            self::Grams => __('g'),
            self::Kilos => __('kg'),
            self::Milliliters => __('ml'),
            self::Liters => __('l'),
            self::Pieces => __('pcs'),
        };
    }

    public static function fromString(string $input): ?self
    {
        $input = strtolower($input);

        return match ($input) {
            'g', 'grams', 'gram', 'gr' => self::Grams,
            'ml', 'milliliter', 'millilitre', 'milliliters', 'millilitres' => self::Milliliters,
            'pc', 'piece', 'pieces' => self::Pieces,
            default => null,
        };
    }
}
