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
    case SPREAD = 12;
    case GRAINS_CEREALS = 13;

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
            self::SPREAD => __('Spread'),
            self::GRAINS_CEREALS => __('Grains & Cereals'),
        };
    }

    public function getPromptHint(): string
    {
        return match ($this) {
            self::FRUIT_VEGETABLES => 'fresh fruit and vegetables (Apfel, Tomate, Karotte, salad, banana)',
            self::MEAT_SEAFOOD => 'fresh meat, poultry, fish, seafood (Hühnchen, Hackfleisch, Lachs, ground beef)',
            self::DAIRY_EGGS => 'milk, butter, cheese, yogurt, eggs, plant-based milk and yogurt (Milch, Joghurt, Käse, Eier, Hafermilch)',
            self::BAKERY => 'bread, rolls, pastries (Brot, Brötchen, Baguette)',
            self::FROZEN => 'frozen foods (TK-Erbsen, Tiefkühlpizza, ice cream, Eis)',
            self::BEVERAGES => 'water, juice, soda, coffee, tea (Wasser, Saft, Kaffee, Tee, Limo)',
            self::SNACKS => 'chips, crackers, sweets, chocolate (Schokolade, Kekse, Chips, Gummibärchen)',
            self::CONDIMENTS => 'cooking oils, vinegar, sauces, mustard, ketchup, soy sauce (Öl, Olivenöl, Essig, Senf, Sojasauce)',
            self::SPICES => 'dry spices and herbs (Salz, Pfeffer, Paprikapulver, Oregano, Zimt)',
            self::BAKING => 'sugar, flour, yeast, baking powder, cocoa, vanilla (Zucker, Puderzucker, Mehl, Hefe, Backpulver, Kakao)',
            self::CANNED_GOODS => 'canned vegetables, beans, fish, soup (Kichererbsen Dose, Tomaten Dose, Thunfisch Dose)',
            self::SPREAD => 'jam, honey, nut butters, chocolate spread (Marmelade, Honig, Nutella, Erdnussbutter)',
            self::GRAINS_CEREALS => 'rice, pasta, oats, couscous, quinoa, muesli (Reis, Nudeln, Haferflocken, Müsli, Couscous)',
            self::OTHER => 'use only when no other category clearly applies',
        };
    }
}
