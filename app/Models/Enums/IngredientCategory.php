<?php

namespace App\Models\Enums;

enum IngredientCategory: int
{
    case OTHER = 0;
    case FRUIT_VEGETABLES = 1;
    case MEAT_SEAFOOD = 2;
    case DAIRY_EGGS = 3;
    case BAKERY = 4;
    case FROZEN = 5;
    case BEVERAGES = 6;
    case SNACKS = 7;
    case CONDIMENTS = 8;
    case SPICES = 9;
    case BAKING = 10;
    case CANNED_GOODS = 11;
    case INTERNATIONAL = 12;

    public function getLabel(): string
    {
        return match ($this) {
            self::OTHER => __('Other'),
            self::FRUIT_VEGETABLES => __('Fruits & Vegetables'),
            self::MEAT_SEAFOOD => __('Meat & Seafood'),
            self::DAIRY_EGGS => __('Dairy & Eggs'),
            self::BAKERY => __('Bakery'),
            self::FROZEN => __('Frozen Foods'),
            self::BEVERAGES => __('Beverages'),
            self::SNACKS => __('Snacks'),
            self::CONDIMENTS => __('Condiments'),
            self::SPICES => __('Spices & Herbs'),
            self::BAKING => __('Baking Supplies'),
            self::CANNED_GOODS => __('Canned Goods'),
            self::INTERNATIONAL => __('International Foods'),
        };
    }
}
