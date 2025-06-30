<?php

namespace App\Models\Enums;

enum Unit: int
{
    case Grams = 1;
    case Kilos = 2;
    case Milliliters = 3;
    case Liters = 4;
    case Pieces = 5;

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

    public static function getLocalizedOptionsArray(): array
    {
        $values = [];

        foreach (self::cases() as $case) {
            $values[$case->value] = $case->getLabel();
        }

        return $values;
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
