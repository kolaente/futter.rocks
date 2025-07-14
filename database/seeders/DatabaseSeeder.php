<?php

namespace Database\Seeders;

use App\Models\Enums\IngredientCategory;
use App\Models\Enums\Unit;
use App\Models\Event;
use App\Models\Ingredient;
use App\Models\Meal;
use App\Models\ParticipantGroup;
use App\Models\Recipe;
use App\Models\Scopes\CurrentTeam;
use App\Models\ShoppingTour;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $user = User::first();
        if ($user === null) {
            $user = User::factory()->withPersonalTeam()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        $user->switchTeam($user->currentTeam);

        $this->createRecipes($user);
        $this->createParticipantGroups($user);
        $event = $this->createEvent($user);
        $this->createMealsForEvent($user, $event);
        $this->createShoppingTours($user, $event);
        $this->linkParticipantGroupsToEvent($user, $event);
    }

    private function createRecipe(User $user, string $title, array $ingredients): void
    {
        $recipe = Recipe::withoutGlobalScope(CurrentTeam::class)->firstOrCreate(
            [
                'title' => $title,
                'team_id' => $user->currentTeam->id,
            ]
        );

        // $ingredients is an array looking like this:
        // [
        //     'title' => 'Ingredient 1',
        //     'quantity' => 1,
        //     'unit' => Unit::Pieces,
        //     'category' => IngredientCategory::FRUIT_VEGETABLES,
        // ]
        foreach ($ingredients as $ingredientData) {
            $ingredient = Ingredient::withoutGlobalScope(CurrentTeam::class)->firstOrCreate([
                'title' => $ingredientData['title'],
                'category' => $ingredientData['category'] ?? IngredientCategory::OTHER,
            ]);

            // Use updateOrInsert to avoid duplicate pivot entries if seeder runs multiple times
            $recipe->ingredients()->updateExistingPivot($ingredient->id, [
                'quantity' => $ingredientData['quantity'],
                'unit' => $ingredientData['unit'],
            ]) || $recipe->ingredients()->attach($ingredient->id, [
                'quantity' => $ingredientData['quantity'],
                'unit' => $ingredientData['unit'],
            ]);
        }
    }

    private function createRecipes(User $user): void
    {
        $this->createRecipe($user, 'Chili Sin Carne', [
            ['title' => 'Zwiebel', 'quantity' => 25, 'unit' => Unit::Grams, 'category' => IngredientCategory::FRUIT_VEGETABLES],
            ['title' => 'Tomaten, stückig, Dose', 'quantity' => 100, 'unit' => Unit::Grams, 'category' => IngredientCategory::CANNED_GOODS],
            ['title' => 'Kidneybohnen, Dose', 'quantity' => 65, 'unit' => Unit::Grams, 'category' => IngredientCategory::CANNED_GOODS],
            ['title' => 'Mais, Dose', 'quantity' => 65, 'unit' => Unit::Grams, 'category' => IngredientCategory::CANNED_GOODS],
            ['title' => 'Paprika', 'quantity' => 0.4, 'unit' => Unit::Pieces, 'category' => IngredientCategory::FRUIT_VEGETABLES],
            ['title' => 'Tomatenmark', 'quantity' => 15, 'unit' => Unit::Grams, 'category' => IngredientCategory::CONDIMENTS],
            ['title' => 'Sonnenblumenöl', 'quantity' => 10, 'unit' => Unit::Milliliters, 'category' => IngredientCategory::OTHER],
        ]);

        $this->createRecipe($user, 'Reis als Beilage', [
            ['title' => 'Reis', 'quantity' => 50, 'unit' => Unit::Grams, 'category' => IngredientCategory::GRAINS_CEREALS],
        ]);

        $this->createRecipe($user, 'Chai', [
            ['title' => 'Fruchtsaft', 'quantity' => 75, 'unit' => Unit::Milliliters, 'category' => IngredientCategory::BEVERAGES],
            ['title' => 'Früchtetee oder Punschmischung', 'quantity' => 75, 'unit' => Unit::Milliliters, 'category' => IngredientCategory::BEVERAGES],
            ['title' => 'Rosinen', 'quantity' => 10, 'unit' => Unit::Grams, 'category' => IngredientCategory::FRUIT_VEGETABLES],
        ]);

        $this->createRecipe($user, 'Brotzeit herzhaft', [
            ['title' => 'Brot', 'quantity' => 75, 'unit' => Unit::Grams, 'category' => IngredientCategory::BAKERY],
            ['title' => 'Butter / Magarine', 'quantity' => 10, 'unit' => Unit::Grams, 'category' => IngredientCategory::DAIRY_EGGS],
            ['title' => 'Käseaufschnitt', 'quantity' => 18, 'unit' => Unit::Grams, 'category' => IngredientCategory::DAIRY_EGGS],
            ['title' => 'veganer Brotaufstrich', 'quantity' => 10, 'unit' => Unit::Grams, 'category' => IngredientCategory::SPREAD],
            ['title' => 'Wurstaufschnitt', 'quantity' => 10, 'unit' => Unit::Grams, 'category' => IngredientCategory::MEAT_SEAFOOD],
        ]);

        $this->createRecipe($user, 'Müsli', [
            ['title' => 'Müsli aller Art', 'quantity' => 90, 'unit' => Unit::Grams, 'category' => IngredientCategory::GRAINS_CEREALS],
        ]);

        $this->createRecipe($user, 'Kekse', [
            ['title' => 'Kekse', 'quantity' => 50, 'unit' => Unit::Grams, 'category' => IngredientCategory::SNACKS],
        ]);

        $this->createRecipe($user, 'Baguette als Beilage', [
            ['title' => 'Baguette', 'quantity' => 0.3, 'unit' => Unit::Pieces, 'category' => IngredientCategory::BAKERY],
        ]);

        $this->createRecipe($user, 'Rohkost Obst', [
            ['title' => 'Apfel', 'quantity' => 0.35, 'unit' => Unit::Pieces, 'category' => IngredientCategory::FRUIT_VEGETABLES],
            ['title' => 'Mandarinen', 'quantity' => 1, 'unit' => Unit::Pieces, 'category' => IngredientCategory::FRUIT_VEGETABLES],
        ]);

        $this->createRecipe($user, 'Rohkost herzhaft', [
            ['title' => 'Gurke', 'quantity' => 0.1, 'unit' => Unit::Pieces, 'category' => IngredientCategory::FRUIT_VEGETABLES],
            ['title' => 'Möhre', 'quantity' => 0.4, 'unit' => Unit::Pieces, 'category' => IngredientCategory::FRUIT_VEGETABLES],
        ]);

        $this->createRecipe($user, 'Chips', [
            ['title' => 'Chips', 'quantity' => 20, 'unit' => Unit::Grams, 'category' => IngredientCategory::SNACKS],
        ]);

        $this->createRecipe($user, 'Kürbispuffer', [
            ['title' => 'Kürbisfleisch ich nehme Hokkaido', 'quantity' => 150, 'unit' => Unit::Grams, 'category' => IngredientCategory::FRUIT_VEGETABLES],
            ['title' => 'Eier', 'quantity' => 0.4, 'unit' => Unit::Pieces, 'category' => IngredientCategory::DAIRY_EGGS],
            ['title' => 'Eigelb', 'quantity' => 0.2, 'unit' => Unit::Pieces, 'category' => IngredientCategory::DAIRY_EGGS],
            ['title' => 'Bergkäse , fein geraffelt', 'quantity' => 44, 'unit' => Unit::Grams, 'category' => IngredientCategory::DAIRY_EGGS],
            ['title' => 'Basilikum , frisch gehackt', 'quantity' => 9, 'unit' => Unit::Milliliters, 'category' => IngredientCategory::SPICES],
            ['title' => 'Knoblauchzehen', 'quantity' => 0.6, 'unit' => Unit::Pieces, 'category' => IngredientCategory::FRUIT_VEGETABLES],
            ['title' => 'Mehl , glatt', 'quantity' => 18, 'unit' => Unit::Grams, 'category' => IngredientCategory::BAKING],
            ['title' => 'Pck. Backpulver', 'quantity' => 0.1, 'unit' => Unit::Pieces, 'category' => IngredientCategory::BAKING],
            ['title' => 'Haferflocken (Zart)', 'quantity' => 6, 'unit' => Unit::Grams, 'category' => IngredientCategory::GRAINS_CEREALS],
        ]);

        $this->createRecipe($user, 'Käsige Lauchnudeln', [
            ['title' => 'Knoblauchzehen', 'quantity' => 0.25, 'unit' => Unit::Pieces, 'category' => IngredientCategory::FRUIT_VEGETABLES],
            ['title' => 'Sonnenblumenöl', 'quantity' => 15, 'unit' => Unit::Milliliters, 'category' => IngredientCategory::OTHER],
            ['title' => 'Lauch', 'quantity' => 250, 'unit' => Unit::Grams, 'category' => IngredientCategory::FRUIT_VEGETABLES],
            ['title' => 'Schmand', 'quantity' => 50, 'unit' => Unit::Grams, 'category' => IngredientCategory::DAIRY_EGGS],
            ['title' => 'Hartkäse (vegetarisch, ohne Lab!)', 'quantity' => 20, 'unit' => Unit::Grams, 'category' => IngredientCategory::DAIRY_EGGS],
            ['title' => 'Nudeln', 'quantity' => 120, 'unit' => Unit::Grams, 'category' => IngredientCategory::GRAINS_CEREALS],
        ]);

        $this->createRecipe($user, 'Lunchpakete aufwerten', [
            ['title' => 'Gurke', 'quantity' => 0.175, 'unit' => Unit::Pieces, 'category' => IngredientCategory::FRUIT_VEGETABLES],
            ['title' => 'Apfel', 'quantity' => 50, 'unit' => Unit::Grams, 'category' => IngredientCategory::FRUIT_VEGETABLES],
            ['title' => 'Karotte', 'quantity' => 25, 'unit' => Unit::Grams, 'category' => IngredientCategory::FRUIT_VEGETABLES],
        ]);

        $this->createRecipe($user, 'Gekochtes Ei', [
            ['title' => 'Ei', 'quantity' => 1, 'unit' => Unit::Pieces, 'category' => IngredientCategory::DAIRY_EGGS],
        ]);

        $this->createRecipe($user, 'Wassermelone', [
            ['title' => 'Wassermelone', 'quantity' => 0.1, 'unit' => Unit::Pieces, 'category' => IngredientCategory::FRUIT_VEGETABLES], // Recipe ID 18
        ]);

        $this->createRecipe($user, 'Leitungskekse', [
            ['title' => '1 Kasten Leitungskekse', 'quantity' => 0.01, 'unit' => Unit::Pieces, 'category' => IngredientCategory::OTHER],
        ]);

        $this->createRecipe($user, 'Kumpir-Kartoffeln', [
            ['title' => 'große Kartoffeln', 'quantity' => 300, 'unit' => Unit::Grams, 'category' => IngredientCategory::FRUIT_VEGETABLES],
            ['title' => 'Butter', 'quantity' => 10, 'unit' => Unit::Grams, 'category' => IngredientCategory::DAIRY_EGGS],
            ['title' => 'Geriebener Käse', 'quantity' => 25, 'unit' => Unit::Grams, 'category' => IngredientCategory::DAIRY_EGGS],
            ['title' => 'Mais, Dose', 'quantity' => 25, 'unit' => Unit::Grams, 'category' => IngredientCategory::CANNED_GOODS],
            ['title' => 'Erbsen, Dose', 'quantity' => 20, 'unit' => Unit::Grams, 'category' => IngredientCategory::CANNED_GOODS],
            ['title' => 'Kidneybohnen, Dose', 'quantity' => 20, 'unit' => Unit::Grams, 'category' => IngredientCategory::CANNED_GOODS],
            ['title' => 'Tomaten, getrocknet', 'quantity' => 10, 'unit' => Unit::Grams, 'category' => IngredientCategory::FRUIT_VEGETABLES],
            ['title' => 'Zwiebeln', 'quantity' => 20, 'unit' => Unit::Grams, 'category' => IngredientCategory::FRUIT_VEGETABLES],
            ['title' => 'Frühlingszwiebeln', 'quantity' => 20, 'unit' => Unit::Grams, 'category' => IngredientCategory::FRUIT_VEGETABLES],
            ['title' => 'Quark', 'quantity' => 70, 'unit' => Unit::Grams, 'category' => IngredientCategory::DAIRY_EGGS],
            ['title' => 'Rote Beete', 'quantity' => 10, 'unit' => Unit::Grams, 'category' => IngredientCategory::FRUIT_VEGETABLES],
            ['title' => 'Oliven', 'quantity' => 5, 'unit' => Unit::Grams, 'category' => IngredientCategory::FRUIT_VEGETABLES],
        ]);

        $this->createRecipe($user, 'Bratwürstchen', [
            ['title' => 'Bratwurst', 'quantity' => 1.5, 'unit' => Unit::Pieces, 'category' => IngredientCategory::MEAT_SEAFOOD],
        ]);

        $this->createRecipe($user, 'Couscous-Salat, lecker würzig', [
            ['title' => 'Couscous', 'quantity' => 62.5, 'unit' => Unit::Grams, 'category' => IngredientCategory::GRAINS_CEREALS],
            ['title' => 'Gemüsebrühe oder Gemüsefond', 'quantity' => 62.5, 'unit' => Unit::Milliliters, 'category' => IngredientCategory::CONDIMENTS],
            ['title' => 'Tomatenmark', 'quantity' => 3.75, 'unit' => Unit::Milliliters, 'category' => IngredientCategory::CONDIMENTS], // Original unit was not mapped, assuming ml based on context
            ['title' => 'Paprikaschoten, rote', 'quantity' => 0.25, 'unit' => Unit::Pieces, 'category' => IngredientCategory::FRUIT_VEGETABLES],
            ['title' => 'Paprikaschoten, gelbe', 'quantity' => 0.25, 'unit' => Unit::Pieces, 'category' => IngredientCategory::FRUIT_VEGETABLES],
            ['title' => 'Dosen Mais', 'quantity' => 0.25, 'unit' => Unit::Pieces, 'category' => IngredientCategory::CANNED_GOODS], // Assuming pcs for 'Dosen Mais'
            ['title' => 'Reisessig', 'quantity' => 7.5, 'unit' => Unit::Milliliters, 'category' => IngredientCategory::CONDIMENTS],
            ['title' => 'Sojasauce', 'quantity' => 3.75, 'unit' => Unit::Milliliters, 'category' => IngredientCategory::CONDIMENTS],
            ['title' => 'Frühlingszwiebeln', 'quantity' => 0.25, 'unit' => Unit::Grams, 'category' => IngredientCategory::FRUIT_VEGETABLES], // Note: ID 71 used for Frühlingszwiebeln, keeping gr unit as per SQL
            ['title' => 'Sonnenblumenöl', 'quantity' => 5, 'unit' => Unit::Milliliters, 'category' => IngredientCategory::OTHER],
        ]);

        $this->createRecipe($user, 'Apfelkuchen aus dem Dutch Oven', [
            ['title' => 'Zucker', 'quantity' => 37.5, 'unit' => Unit::Grams, 'category' => IngredientCategory::BAKING],
            ['title' => 'Margarine', 'quantity' => 37.5, 'unit' => Unit::Grams, 'category' => IngredientCategory::SPREAD],
            ['title' => 'Eier', 'quantity' => 0.5, 'unit' => Unit::Pieces, 'category' => IngredientCategory::DAIRY_EGGS],
            ['title' => 'Pck. Vanillezucker', 'quantity' => 0.25, 'unit' => Unit::Pieces, 'category' => IngredientCategory::BAKING],
            ['title' => 'Mehl', 'quantity' => 85, 'unit' => Unit::Grams, 'category' => IngredientCategory::BAKING],
            ['title' => 'Pck. Backpulver', 'quantity' => 0.1875, 'unit' => Unit::Pieces, 'category' => IngredientCategory::BAKING],
            ['title' => 'Apfel', 'quantity' => 0.75, 'unit' => Unit::Pieces, 'category' => IngredientCategory::FRUIT_VEGETABLES], // ID 19 Apfel
        ]);

        $this->createRecipe($user, 'Käsespätzle', [
            ['title' => 'Bergkäse , fein geraffelt', 'quantity' => 50, 'unit' => Unit::Grams, 'category' => IngredientCategory::DAIRY_EGGS], // ID 51 Bergkäse
            ['title' => 'Zwiebeln', 'quantity' => 75, 'unit' => Unit::Grams, 'category' => IngredientCategory::FRUIT_VEGETABLES], // ID 25 Zwiebeln
            ['title' => 'Spätzle, trocken', 'quantity' => 120, 'unit' => Unit::Grams, 'category' => IngredientCategory::GRAINS_CEREALS],
        ]);

        $this->createRecipe($user, 'Salat mit Essig-Öl Dressing', [
            ['title' => 'Olivenöl', 'quantity' => 10, 'unit' => Unit::Milliliters, 'category' => IngredientCategory::CONDIMENTS], // ID 90 Olivenöl
            ['title' => 'Zucker', 'quantity' => 5, 'unit' => Unit::Grams, 'category' => IngredientCategory::BAKING],
            ['title' => 'Weißweinessig', 'quantity' => 15, 'unit' => Unit::Milliliters, 'category' => IngredientCategory::CONDIMENTS],
            ['title' => 'Eisbergsalat', 'quantity' => 80, 'unit' => Unit::Grams, 'category' => IngredientCategory::FRUIT_VEGETABLES],
        ]);

        $this->createRecipe($user, 'Gemüsepfanne', [
            ['title' => 'Tomate, frisch', 'quantity' => 40, 'unit' => Unit::Grams, 'category' => IngredientCategory::FRUIT_VEGETABLES],
            ['title' => 'Mais, Dose', 'quantity' => 40, 'unit' => Unit::Grams, 'category' => IngredientCategory::CANNED_GOODS],
            ['title' => 'Zucchini', 'quantity' => 60, 'unit' => Unit::Grams, 'category' => IngredientCategory::FRUIT_VEGETABLES],
            ['title' => 'Zwiebel', 'quantity' => 30, 'unit' => Unit::Grams, 'category' => IngredientCategory::FRUIT_VEGETABLES], // ID 1 Zwiebel
            ['title' => 'Karotte', 'quantity' => 40, 'unit' => Unit::Grams, 'category' => IngredientCategory::FRUIT_VEGETABLES], // ID 63 Karotte
            ['title' => 'Paprika', 'quantity' => 50, 'unit' => Unit::Grams, 'category' => IngredientCategory::FRUIT_VEGETABLES], // ID 99 Paprika
        ]);

        $this->createRecipe($user, 'Wrap mit Tzatziki', [
            ['title' => 'Wraps', 'quantity' => 2.25, 'unit' => Unit::Pieces, 'category' => IngredientCategory::BAKERY],
            ['title' => 'Tomaten', 'quantity' => 110, 'unit' => Unit::Grams, 'category' => IngredientCategory::FRUIT_VEGETABLES], // ID 97 Tomaten
            ['title' => 'Paprika', 'quantity' => 40, 'unit' => Unit::Grams, 'category' => IngredientCategory::FRUIT_VEGETABLES], // ID 99 Paprika
            ['title' => 'Eisbergsalat', 'quantity' => 30, 'unit' => Unit::Grams, 'category' => IngredientCategory::FRUIT_VEGETABLES],
            ['title' => 'Feta oder Hirtenkäse', 'quantity' => 55, 'unit' => Unit::Grams, 'category' => IngredientCategory::DAIRY_EGGS],
            ['title' => 'Naturjoghurt, 3,5% Fett', 'quantity' => 80, 'unit' => Unit::Grams, 'category' => IngredientCategory::DAIRY_EGGS],
            ['title' => 'Weißweinessig', 'quantity' => 5, 'unit' => Unit::Milliliters, 'category' => IngredientCategory::CONDIMENTS],
            ['title' => 'Sonnenblumenöl', 'quantity' => 10, 'unit' => Unit::Milliliters, 'category' => IngredientCategory::OTHER],
            ['title' => 'Zwiebel', 'quantity' => 35, 'unit' => Unit::Grams, 'category' => IngredientCategory::FRUIT_VEGETABLES], // ID 1 Zwiebel
            ['title' => 'Gurke', 'quantity' => 0.2, 'unit' => Unit::Pieces, 'category' => IngredientCategory::FRUIT_VEGETABLES], // ID 21 Gurke
        ]);

        $this->createRecipe($user, 'Obstsalat', [
            ['title' => 'Bananen', 'quantity' => 0.125, 'unit' => Unit::Pieces, 'category' => IngredientCategory::FRUIT_VEGETABLES],
            ['title' => 'Kiwis', 'quantity' => 0.25, 'unit' => Unit::Pieces, 'category' => IngredientCategory::FRUIT_VEGETABLES],
            ['title' => 'Apfel', 'quantity' => 0.35, 'unit' => Unit::Pieces, 'category' => IngredientCategory::FRUIT_VEGETABLES], // ID 19 Apfel
            ['title' => 'Nektarinen', 'quantity' => 0.25, 'unit' => Unit::Pieces, 'category' => IngredientCategory::FRUIT_VEGETABLES],
            ['title' => 'Wassermelone', 'quantity' => 0.1, 'unit' => Unit::Pieces, 'category' => IngredientCategory::FRUIT_VEGETABLES], // ID 107 Wassermelone
            ['title' => 'Zitronen, den Saft davon', 'quantity' => 0.125, 'unit' => Unit::Pieces, 'category' => IngredientCategory::FRUIT_VEGETABLES],
            ['title' => 'Orangen, filetiert', 'quantity' => 0.25, 'unit' => Unit::Pieces, 'category' => IngredientCategory::FRUIT_VEGETABLES],
            ['title' => 'Beeren TK, gemischte, nach Wahl', 'quantity' => 25, 'unit' => Unit::Grams, 'category' => IngredientCategory::FROZEN],
        ]);

        $this->createRecipe($user, 'Stockbrot', [
            ['title' => 'Wasser, lauwarm', 'quantity' => 57, 'unit' => Unit::Milliliters, 'category' => IngredientCategory::BEVERAGES],
            ['title' => 'Salz, Prise', 'quantity' => 1, 'unit' => Unit::Pieces, 'category' => IngredientCategory::SPICES],
            ['title' => 'Mehl', 'quantity' => 100, 'unit' => Unit::Grams, 'category' => IngredientCategory::BAKING], // ID 30 Mehl
            ['title' => 'Trockenhefe', 'quantity' => 2, 'unit' => Unit::Grams, 'category' => IngredientCategory::BAKING],
            ['title' => 'Zucker', 'quantity' => 3, 'unit' => Unit::Grams, 'category' => IngredientCategory::BAKING],
            ['title' => 'Sonnenblumenöl', 'quantity' => 10, 'unit' => Unit::Milliliters, 'category' => IngredientCategory::OTHER],
        ]);

        $this->createRecipe($user, 'Früchtequark', [
            ['title' => 'Quark', 'quantity' => 100, 'unit' => Unit::Grams, 'category' => IngredientCategory::DAIRY_EGGS], // ID 120 Quark
            ['title' => 'Pck. Vanillezucker', 'quantity' => 0.5, 'unit' => Unit::Pieces, 'category' => IngredientCategory::BAKING],
            ['title' => 'Honig', 'quantity' => 7.5, 'unit' => Unit::Milliliters, 'category' => IngredientCategory::OTHER],
            ['title' => 'Apfel', 'quantity' => 0.25, 'unit' => Unit::Pieces, 'category' => IngredientCategory::FRUIT_VEGETABLES], // ID 19 Apfel
            ['title' => 'Birnen', 'quantity' => 0.25, 'unit' => Unit::Pieces, 'category' => IngredientCategory::FRUIT_VEGETABLES],
            ['title' => 'Bananen', 'quantity' => 0.25, 'unit' => Unit::Pieces, 'category' => IngredientCategory::FRUIT_VEGETABLES],
            ['title' => 'Zitronen, der Saft davon', 'quantity' => 0.25, 'unit' => Unit::Pieces, 'category' => IngredientCategory::FRUIT_VEGETABLES],
            ['title' => 'Orangen', 'quantity' => 0.5, 'unit' => Unit::Pieces, 'category' => IngredientCategory::FRUIT_VEGETABLES], // ID 127 Orangen
        ]);

        $this->createRecipe($user, 'Frühstück mit Brot', [
            ['title' => 'Brot', 'quantity' => 125, 'unit' => Unit::Grams, 'category' => IngredientCategory::BAKERY],
            ['title' => 'Marmelade', 'quantity' => 15, 'unit' => Unit::Grams, 'category' => IngredientCategory::SPREAD],
            ['title' => 'Käseaufschnitt', 'quantity' => 25, 'unit' => Unit::Grams, 'category' => IngredientCategory::DAIRY_EGGS],
            ['title' => 'Wurstaufschnitt', 'quantity' => 20, 'unit' => Unit::Grams, 'category' => IngredientCategory::MEAT_SEAFOOD],
            ['title' => 'veganer Brotaufstrich', 'quantity' => 30, 'unit' => Unit::Grams, 'category' => IngredientCategory::SPREAD],
            ['title' => 'Magarine', 'quantity' => 15, 'unit' => Unit::Grams, 'category' => IngredientCategory::SPREAD], // ID 130 Magarine
        ]);

        $this->createRecipe($user, 'Porridge', [
            ['title' => 'Milch', 'quantity' => 120, 'unit' => Unit::Milliliters, 'category' => IngredientCategory::DAIRY_EGGS],
            ['title' => 'Haferflocken (Zart)', 'quantity' => 50, 'unit' => Unit::Grams, 'category' => IngredientCategory::GRAINS_CEREALS],
            ['title' => 'Zucker', 'quantity' => 10, 'unit' => Unit::Grams, 'category' => IngredientCategory::BAKING],
            ['title' => 'Zimt', 'quantity' => 0.5, 'unit' => Unit::Grams, 'category' => IngredientCategory::SPICES],
            ['title' => 'Apfel', 'quantity' => 37.5, 'unit' => Unit::Grams, 'category' => IngredientCategory::FRUIT_VEGETABLES], // ID 62 Apfel (assuming this one)
            ['title' => 'Rosinen', 'quantity' => 5, 'unit' => Unit::Grams, 'category' => IngredientCategory::FRUIT_VEGETABLES],
        ]);

        $this->createRecipe($user, 'Rührei', [
            ['title' => 'Ei', 'quantity' => 1.5, 'unit' => Unit::Pieces, 'category' => IngredientCategory::DAIRY_EGGS], // ID 133 Ei
        ]);

        // Add other recipes here following the same pattern
        // Note: Recipe "Reis mit Scheiß" (ID 35) and "Kürbissuppe" (ID 36) were in the SQL but had no ingredients listed in ingredient_recipe table.
        // They will be created with empty ingredients lists if needed:
        $this->createRecipe($user, 'Reis mit Scheiß', []);
        $this->createRecipe($user, 'Kürbissuppe ', []);
    }

    private function createParticipantGroups(User $user): void
    {
        $groups = [
            ['title' => 'Helfende / Leitungen', 'food_factor' => 1.1],
            ['title' => 'Wölflinge', 'food_factor' => 0.75],
            ['title' => 'Jufis', 'food_factor' => 1.0],
            ['title' => 'Pfadis', 'food_factor' => 1.2],
            ['title' => 'Rover', 'food_factor' => 1.2],
        ];

        foreach ($groups as $groupData) {
            ParticipantGroup::withoutGlobalScope(CurrentTeam::class)->updateOrCreate(
                [
                    'title' => $groupData['title'],
                    'team_id' => $user->currentTeam->id,
                ],
                [
                    'food_factor' => $groupData['food_factor'],
                ]
            );
        }
    }

    private function createEvent(User $user): Event
    {
        return Event::withoutGlobalScope(CurrentTeam::class)->updateOrCreate(
            [
                'title' => 'Sommerlager',
                'team_id' => $user->currentTeam->id,
            ],
            [
                'date_from' => '2024-08-14', // Adjusted to match meal dates
                'date_to' => '2024-08-23', // Adjusted to match meal dates
                'created_by_id' => $user->id,
                'share_id' => Str::uuid(),
            ]
        );
    }

    private function createMealsForEvent(User $user, Event $event): void
    {
        // Structure: [ 'title' => Meal Title, 'date' => Meal Date, 'recipes' => [Recipe Title 1, Recipe Title 2, ... ] ]
        $mealPlan = [
            // 2024-08-14
            ['title' => 'Mittag', 'date' => '2024-08-14', 'recipes' => ['Lunchpakete aufwerten', 'Gekochtes Ei', 'Wassermelone']], // Meal ID 9 -> Recipe IDs 16, 17, 18
            ['title' => 'Abendessen', 'date' => '2024-08-14', 'recipes' => ['Käsige Lauchnudeln', 'Leitungskekse', 'Früchtequark']], // Meal ID 10 -> Recipe IDs 15, 19, 31
            // 2024-08-15
            ['title' => 'Frühstück', 'date' => '2024-08-15', 'recipes' => ['Frühstück mit Brot', 'Müsli']], // Meal ID 11 -> Recipe IDs 32, 5
            ['title' => 'Mittag', 'date' => '2024-08-15', 'recipes' => ['Brotzeit herzhaft', 'Rohkost herzhaft']], // Meal ID 12 -> Recipe IDs 4, 9
            ['title' => 'Abendessen', 'date' => '2024-08-15', 'recipes' => ['Chili Sin Carne']], // Meal ID 13 -> Recipe ID 1
            ['title' => 'Feuer', 'date' => '2024-08-15', 'recipes' => ['Stockbrot']], // Meal ID 14 -> Recipe ID 30
            // 2024-08-16
            ['title' => 'Frühstück', 'date' => '2024-08-16', 'recipes' => ['Frühstück mit Brot', 'Porridge']], // Meal ID 15 -> Recipe IDs 32, 33
            ['title' => 'Mittag', 'date' => '2024-08-16', 'recipes' => ['Brotzeit herzhaft', 'Rohkost Obst']], // Meal ID 16 -> Recipe IDs 4, 8
            ['title' => 'Abendessen', 'date' => '2024-08-16', 'recipes' => ['Kumpir-Kartoffeln']], // Meal ID 17 -> Recipe ID 20
            // 2024-08-17
            ['title' => 'Frühstück', 'date' => '2024-08-17', 'recipes' => ['Frühstück mit Brot', 'Rührei']], // Meal ID 18 -> Recipe IDs 32, 34
            ['title' => 'Mittag', 'date' => '2024-08-17', 'recipes' => ['Rohkost Obst', 'Rohkost herzhaft']], // Meal ID 20 -> Recipe IDs 8, 9
            // 2024-08-18
            ['title' => 'Frühstück', 'date' => '2024-08-18', 'recipes' => ['Frühstück mit Brot', 'Müsli']], // Meal ID 19 -> Recipe IDs 32, 5
            ['title' => 'Abendessen', 'date' => '2024-08-18', 'recipes' => ['Bratwürstchen', 'Couscous-Salat, lecker würzig', 'Apfelkuchen aus dem Dutch Oven']], // Meal ID 21 -> Recipe IDs 21, 22, 23
            ['title' => 'Lagerfeuer', 'date' => '2024-08-18', 'recipes' => ['Stockbrot']], // Meal ID 31 -> Recipe ID 30
            // 2024-08-19
            ['title' => 'Frühstück', 'date' => '2024-08-19', 'recipes' => ['Frühstück mit Brot', 'Rührei']], // Meal ID 22 -> Recipe IDs 32, 34
            ['title' => 'Abendessen', 'date' => '2024-08-19', 'recipes' => ['Reis als Beilage', 'Reis mit Scheiß']], // Meal ID 23 -> Recipe IDs 2, 35
            ['title' => 'Lagerfeuer', 'date' => '2024-08-19', 'recipes' => ['Chai']], // Meal ID 32 -> Recipe ID 3
            // 2024-08-20
            ['title' => 'Frühstück', 'date' => '2024-08-20', 'recipes' => ['Frühstück mit Brot', 'Müsli']], // Meal ID 24 -> Recipe IDs 32, 5
            ['title' => 'Mittag', 'date' => '2024-08-20', 'recipes' => ['Brotzeit herzhaft', 'Rohkost Obst']], // Meal ID 25 -> Recipe IDs 4, 8
            ['title' => 'Abendessen', 'date' => '2024-08-20', 'recipes' => ['Käsespätzle']], // Meal ID 26 -> Recipe ID 24
            // 2024-08-21
            ['title' => 'Frühstück', 'date' => '2024-08-21', 'recipes' => ['Frühstück mit Brot', 'Porridge']], // Meal ID 27 -> Recipe IDs 32, 33
            ['title' => 'Mittag', 'date' => '2024-08-21', 'recipes' => ['Brotzeit herzhaft', 'Rohkost herzhaft']], // Meal ID 28 -> Recipe IDs 4, 9
            ['title' => 'Abendessen', 'date' => '2024-08-21', 'recipes' => ['Gemüsepfanne', 'Stockbrot']], // Meal ID 29 -> Recipe IDs 26, 30
            ['title' => 'Lagerfeuer', 'date' => '2024-08-21', 'recipes' => ['Chai']], // Meal ID 30 -> Recipe ID 3
            // 2024-08-22
            ['title' => 'Frühstück', 'date' => '2024-08-22', 'recipes' => ['Rührei']], // Meal ID 33 -> Recipe ID 34
            ['title' => 'Mittag', 'date' => '2024-08-22', 'recipes' => ['Brotzeit herzhaft', 'Rohkost herzhaft']], // Meal ID 34 -> Recipe IDs 4, 9
            ['title' => 'Abendessen', 'date' => '2024-08-22', 'recipes' => ['Wrap mit Tzatziki', 'Obstsalat']], // Meal ID 35 -> Recipe IDs 28, 29
            // 2024-08-23
            ['title' => 'Frühstück', 'date' => '2024-08-23', 'recipes' => ['Frühstück mit Brot', 'Rohkost herzhaft']], // Meal ID 36 -> Recipe IDs 32, 9
        ];

        foreach ($mealPlan as $mealData) {
            $meal = Meal::withoutGlobalScope(CurrentTeam::class)->updateOrCreate(
                [
                    'event_id' => $event->id,
                    'date' => $mealData['date'],
                    'title' => $mealData['title'],
                ]
            );

            if (! empty($mealData['recipes'])) {
                $recipeIds = Recipe::withoutGlobalScope(CurrentTeam::class)
                    ->where('team_id', $user->currentTeam->id)
                    ->whereIn('title', $mealData['recipes'])
                    ->get()
                    ->pluck('id');
                $meal->recipes()->syncWithoutDetaching($recipeIds);
            }
        }
    }

    private function createShoppingTours(User $user, Event $event): void
    {
        $tourDates = [
            '2024-08-17',
            '2024-08-20',
        ];

        foreach ($tourDates as $date) {
            ShoppingTour::withoutGlobalScope(CurrentTeam::class)->updateOrCreate(
                [
                    'event_id' => $event->id,
                    'date' => $date,
                ]
            );
        }
    }

    private function linkParticipantGroupsToEvent(User $user, Event $event): void
    {
        $groupCounts = [
            'Helfende / Leitungen' => 8,
            'Wölflinge' => 7,
            'Jufis' => 11,
            'Pfadis' => 4,
            'Rover' => 4,
        ];

        $groupTitles = array_keys($groupCounts);
        $groups = ParticipantGroup::withoutGlobalScope(CurrentTeam::class)
            ->where('team_id', $user->currentTeam->id)
            ->whereIn('title', $groupTitles)
            ->get();

        $groupsToSync = [];
        foreach ($groups as $group) {
            if (isset($groupCounts[$group->title])) {
                $groupsToSync[$group->id] = ['quantity' => $groupCounts[$group->title]];
            }
        }

        // Assuming the relationship is named participantGroups and pivot table has a 'count' column
        if (! empty($groupsToSync)) {
            $event->participantGroups()->syncWithoutDetaching($groupsToSync);
        }
    }
}
