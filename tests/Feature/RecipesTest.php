<?php

use App\Livewire\Recipes\CreateEdit;
use App\Models\Enums\Unit;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Http;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;

describe('Create', function () {

    beforeEach(function () {
        $this->user = User::factory()
            ->withCurrentTeam()
            ->create();
        actingAs($this->user);
        Event::fake();
    });

    function assertIngredients(Recipe $recipe, array $ingredients)
    {
        foreach ($ingredients as $ingredient) {
            assertDatabaseHas('ingredients', [
                'title' => $ingredient['title'],
            ]);
            $ing = Ingredient::where('title', $ingredient['title'])
                ->first();
            assertDatabaseHas('ingredient_recipe', [
                'recipe_id' => $recipe->id,
                'ingredient_id' => $ing->id,
                'quantity' => $ingredient['quantity'],
                'unit' => $ingredient['unit'],
            ]);
        }

        assertDatabaseCount('ingredient_recipe', count($ingredients));
    }

    it('can create a new recipe using only new ingredients', function () {

        $testTitle = 'Käsebrot';

        livewire(CreateEdit::class)
            ->assertStatus(200)
            ->set('data', [
                'title' => $testTitle,
                'ingredients' => [
                    [
                        'ingredient' => 'Brot',
                        'unit' => Unit::Grams,
                        'quantity' => 50,
                    ],
                    [
                        'ingredient' => 'Käse',
                        'unit' => Unit::Grams,
                        'quantity' => 10,
                    ],
                ],
            ])
            ->call('store');

        assertDatabaseHas('recipes', [
            'title' => $testTitle,
            'team_id' => $this->user->current_team_id,
        ]);
        $recipe = Recipe::whereTitle($testTitle)
            ->with('ingredients')
            ->firstOrFail();
        assertCount(2, $recipe->ingredients);
        assertDatabaseHas('ingredients', [
            'title' => 'Brot',
        ]);
        assertDatabaseHas('ingredients', [
            'title' => 'Käse',
        ]);
    });

    it('can create a recipe with existing ingredients', function () {
        $testTitle = 'Käsebrot';

        $ingredients = Ingredient::factory(2)->create();

        livewire(CreateEdit::class)
            ->assertStatus(200)
            ->set('data', [
                'title' => $testTitle,
                'ingredients' => [
                    [
                        'ingredient' => $ingredients[0]->id,
                        'unit' => Unit::Grams,
                        'quantity' => 50,
                    ],
                    [
                        'ingredient' => $ingredients[1]->id,
                        'unit' => Unit::Grams,
                        'quantity' => 10,
                    ],
                ],
            ])
            ->call('store');

        assertDatabaseHas('recipes', [
            'title' => $testTitle,
            'team_id' => $this->user->current_team_id,
        ]);
        $recipe = Recipe::whereTitle($testTitle)
            ->with('ingredients')
            ->firstOrFail();
        assertDatabaseHas('ingredient_recipe', [
            'recipe_id' => $recipe->id,
            'ingredient_id' => $ingredients[0]->id,
            'unit' => Unit::Grams,
        ]);
        assertDatabaseHas('ingredient_recipe', [
            'recipe_id' => $recipe->id,
            'ingredient_id' => $ingredients[1]->id,
            'unit' => Unit::Grams,
        ]);
    });

    it('can create a recipe with partially existing ingredients', function () {
        $testTitle = 'Käsebrot';

        $ingredient = Ingredient::factory()->create();

        livewire(CreateEdit::class)
            ->assertStatus(200)
            ->set('data', [
                'title' => $testTitle,
                'ingredients' => [
                    [
                        'ingredient' => $ingredient->id,
                        'unit' => Unit::Grams,
                        'quantity' => 50,
                    ],
                    [
                        'ingredient' => 'Käse',
                        'unit' => Unit::Grams,
                        'quantity' => 10,
                    ],
                ],
            ])
            ->call('store');

        assertDatabaseHas('recipes', [
            'title' => $testTitle,
            'team_id' => $this->user->current_team_id,
        ]);
        $recipe = Recipe::whereTitle($testTitle)
            ->with('ingredients')
            ->firstOrFail();
        assertDatabaseHas('ingredient_recipe', [
            'recipe_id' => $recipe->id,
            'ingredient_id' => $ingredient->id,
            'unit' => Unit::Grams,
        ]);
    });

    it('redirects after creation', function () {
        Recipe::factory(5)->create();

        $testTitle = 'Käsebrot';

        livewire(CreateEdit::class)
            ->assertStatus(200)
            ->set('data', [
                'title' => $testTitle,
                'ingredients' => [
                    [
                        'ingredient' => 'Brot',
                        'unit' => Unit::Grams,
                        'quantity' => 50,
                    ],
                    [
                        'ingredient' => 'Käse',
                        'unit' => Unit::Grams,
                        'quantity' => 10,
                    ],
                ],
            ])
            ->call('store')
            ->assertRedirect(route('recipes.view', [
                'recipe' => Recipe::whereTitle($testTitle)->firstOrFail(),
            ]));
    });

    describe('Import from Text', function () {
        it('imports from text simple case', function () {
            $team = Team::factory()->create();

            $recipe = Recipe::create([
                'title' => 'Test',
                'team_id' => $team->id,
            ]);

            $recipe->addIngredientsFromText([
                '20g Kidneybohnen, Dose',
                '25g Mais, Dose',
                '25g Geriebener Käse',
                '10g Butter',
                '100ml Wasser',
            ]);

            assertIngredients($recipe, [
                [
                    'title' => 'Kidneybohnen, Dose',
                    'unit' => Unit::Grams,
                    'quantity' => 20,
                ],
                [
                    'title' => 'Mais, Dose',
                    'unit' => Unit::Grams,
                    'quantity' => 25,
                ],
                [
                    'title' => 'Geriebener Käse',
                    'unit' => Unit::Grams,
                    'quantity' => 25,
                ],
                [
                    'title' => 'Butter',
                    'unit' => Unit::Grams,
                    'quantity' => 10,
                ],
                [
                    'title' => 'Wasser',
                    'unit' => Unit::Milliliters,
                    'quantity' => 100,
                ],
            ]);
        });
        it('imports from text complex case', function () {
            $team = Team::factory()->create();

            $recipe = Recipe::create([
                'title' => 'Test',
                'team_id' => $team->id,
            ]);

            $recipe->addIngredientsFromText([
                '20 	g 	Kidneybohnen, Dose',
                '25 	g 	Mais, Dose',
                '25 	g 	Geriebener Käse',
                '10 	g 	Butter',
                '100 	ml 	Wasser',
            ]);

            assertIngredients($recipe, [
                [
                    'title' => 'Kidneybohnen, Dose',
                    'unit' => Unit::Grams,
                    'quantity' => 20,
                ],
                [
                    'title' => 'Mais, Dose',
                    'unit' => Unit::Grams,
                    'quantity' => 25,
                ],
                [
                    'title' => 'Geriebener Käse',
                    'unit' => Unit::Grams,
                    'quantity' => 25,
                ],
                [
                    'title' => 'Butter',
                    'unit' => Unit::Grams,
                    'quantity' => 10,
                ],
                [
                    'title' => 'Wasser',
                    'unit' => Unit::Milliliters,
                    'quantity' => 100,
                ],
            ]);
        });

        it('reports errors for non-parsable lines', function () {
            $team = Team::factory()->create();

            $recipe = Recipe::create([
                'title' => 'Test',
                'team_id' => $team->id,
            ]);

            $errors = $recipe->addIngredientsFromText([
                '20g Kidneybohnen, Dose',
                'something invalid',
                '25g Mais, Dose',
                '25g Geriebener Käse',
                '',
                'Butter',
                '0ml Wasser',
            ]);

            // Should have successfully imported 3 valid ingredients
            assertIngredients($recipe, [
                [
                    'title' => 'Kidneybohnen, Dose',
                    'unit' => Unit::Grams,
                    'quantity' => 20,
                ],
                [
                    'title' => 'Mais, Dose',
                    'unit' => Unit::Grams,
                    'quantity' => 25,
                ],
                [
                    'title' => 'Geriebener Käse',
                    'unit' => Unit::Grams,
                    'quantity' => 25,
                ],
            ]);

            // Should have 3 errors (empty line should be skipped, not cause error)
            expect($errors)->toHaveCount(3)
                ->and($errors[0])->toBe([
                    'line' => 2,
                    'content' => 'something invalid',
                    'error' => __('Unable to parse ingredient. Please use format: "quantity unit ingredient" (e.g., "200ml water" or "1 kg sugar")'),
                ])
                ->and($errors[1])->toBe([
                    'line' => 6,
                    'content' => 'Butter',
                    'error' => __('Unable to parse ingredient. Please use format: "quantity unit ingredient" (e.g., "200ml water" or "1 kg sugar")'),
                ])
                ->and($errors[2])->toBe([
                    'line' => 7,
                    'content' => '0ml Wasser',
                    'error' => __('Invalid quantity ":quantity". Please use a valid number (e.g., 1, 2.5, ¼, ½, ¾, or ⅓)', ['quantity' => 0]),
                ]);

            // Check first error (invalid line)

            // Check second error (another invalid)

            // Check third error (zero quantity)
        });

        it('validates parsing without saving when recipe is not persisted', function () {
            $team = Team::factory()->create();

            $recipe = new Recipe([
                'title' => 'Test',
                'team_id' => $team->id,
            ]);

            $errors = $recipe->addIngredientsFromText([
                '20g Kidneybohnen, Dose',
                'something invalid',
                '25g Mais, Dose',
            ]);

            // Should have 1 error
            expect($errors)->toHaveCount(1)
                ->and($errors[0])->toBe([
                    'line' => 2,
                    'content' => 'something invalid',
                    'error' => __('Unable to parse ingredient. Please use format: "quantity unit ingredient" (e.g., "200ml water" or "1 kg sugar")'),
                ]);

            // No ingredients should be created since recipe is not persisted
            assertDatabaseCount('ingredients', 0);
            assertDatabaseCount('ingredient_recipe', 0);
        });

        it('parses ingredients statically with errors and ingredients', function () {
            [$ingredients, $errors] = Recipe::parseIngredientsFromText([
                '20g Kidneybohnen, Dose',
                'something invalid',
                '25g Mais, Dose',
                '',
                'Butter',
                '0ml Wasser',
            ]);

            // Should have 2 valid ingredients
            expect($ingredients)->toHaveCount(2)
                ->and($ingredients[0])->toBe([
                    'title' => 'Kidneybohnen, Dose',
                    'quantity' => 20.0,
                    'unit' => Unit::Grams,
                ])
                ->and($ingredients[1])->toBe([
                    'title' => 'Mais, Dose',
                    'quantity' => 25.0,
                    'unit' => Unit::Grams,
                ])
                ->and($errors)->toHaveCount(3)
                ->and($errors[0])->toBe([
                    'line' => 2,
                    'content' => 'something invalid',
                    'error' => __('Unable to parse ingredient. Please use format: "quantity unit ingredient" (e.g., "200ml water" or "1 kg sugar")'),
                ])
                ->and($errors[1])->toBe([
                    'line' => 5,
                    'content' => 'Butter',
                    'error' => __('Unable to parse ingredient. Please use format: "quantity unit ingredient" (e.g., "200ml water" or "1 kg sugar")'),
                ])
                ->and($errors[2])->toBe([
                    'line' => 6,
                    'content' => '0ml Wasser',
                    'error' => __('Invalid quantity ":quantity". Please use a valid number (e.g., 1, 2.5, ¼, ½, ¾, or ⅓)', ['quantity' => 0]),
                ]);
        });

        it('shows validation errors in create from text form', function () {
            livewire(\App\Livewire\Recipes\CreateFromText::class)
                ->callAction('createFromText', data: [
                    'title' => 'Test Recipe',
                    'recipe_text' => "20g Kidneybohnen, Dose\nsomething invalid\n25g Mais, Dose",
                ])
                ->assertHasActionErrors(['recipe_text']);
        });

        it('creates recipe successfully with valid text', function () {
            livewire(\App\Livewire\Recipes\CreateFromText::class)
                ->callAction('createFromText', data: [
                    'title' => 'Test Recipe',
                    'recipe_text' => "20g Kidneybohnen, Dose\n25g Mais, Dose",
                ])
                ->assertHasNoActionErrors();

            assertDatabaseHas('recipes', [
                'title' => 'Test Recipe',
                'team_id' => Auth::user()->currentTeam->id,
            ]);
        });
    });

    describe('Import', function () {

        beforeEach(function () {
            Http::fake([
                'https://www.kitchenstories.com/de/rezepte/cremiger-nudel-kurbis-auflauf' => Http::response(file_get_contents(__DIR__.'/../fixtures/recipe-kitchenstories.html')),
                'https://www.chefkoch.de/rezepte/1660421274170785/Vegetarisches-Chili-mit-Bulgur.html' => Http::response(file_get_contents(__DIR__.'/../fixtures/recipe-chefkoch.html')),
            ]);
        });

        it('imports from kitchenstories', function () {
            $team = Team::factory()->create();
            $recipe = Recipe::importFromUrl('https://www.kitchenstories.com/de/rezepte/cremiger-nudel-kurbis-auflauf', $team->id);

            assertDatabaseHas('recipes', [
                'id' => $recipe->id,
                'title' => 'Cremiger Nudel-Kürbis-Auflauf',
            ]);

            assertIngredients($recipe, [
                [
                    'title' => 'Hokkaidokürbis',
                    'unit' => Unit::Grams,
                    'quantity' => 400,
                ],
                [
                    'title' => 'Conchiglie',
                    'unit' => Unit::Grams,
                    'quantity' => 300,
                ],
                [
                    'title' => 'Schalotten',
                    'unit' => Unit::Grams,
                    'quantity' => 60,
                ],
                [
                    'title' => 'Gemüsebrühe',
                    'unit' => Unit::Milliliters,
                    'quantity' => 600,
                ],
                [
                    'title' => 'Ricottakäse',
                    'unit' => Unit::Grams,
                    'quantity' => 125,
                ],
                [
                    'title' => 'Basilikum',
                    'unit' => Unit::Grams,
                    'quantity' => 5,
                ],
                [
                    'title' => 'geriebener Parmesankäse',
                    'unit' => Unit::Grams,
                    'quantity' => 50,
                ],
                [
                    'title' => 'Zehen Knoblauch',
                    'unit' => Unit::Pieces,
                    'quantity' => 3,
                ],
                [
                    'title' => 'Currypulver',
                    'unit' => Unit::Milliliters,
                    'quantity' => 5,
                ],
            ]);
        });

        it('imports using existing ingredients', function () {
            $team = Team::factory()->create();
            $ingredient = Ingredient::create([
                'title' => 'Hokkaidokürbis',
            ]);

            $recipe = Recipe::importFromUrl('https://www.kitchenstories.com/de/rezepte/cremiger-nudel-kurbis-auflauf', $team->id);

            assertDatabaseHas('ingredient_recipe', [
                'recipe_id' => $recipe->id,
                'ingredient_id' => $ingredient->id,
            ]);
            assertDatabaseCount('ingredients', 9);
        });

        it('imports from chefkoch', function () {
            $team = Team::factory()->create();
            $recipe = Recipe::importFromUrl('https://www.chefkoch.de/rezepte/1660421274170785/Vegetarisches-Chili-mit-Bulgur.html', $team->id);

            assertDatabaseHas('recipes', [
                'id' => $recipe->id,
                'title' => 'Vegetarisches Chili mit Bulgur',
            ]);

            assertIngredients($recipe, [
                [
                    'title' => 'Bulgur',
                    'unit' => Unit::Grams,
                    'quantity' => 28.125, // 1 tasse = 150g
                ],
                [
                    'title' => 'Olivenöl',
                    'unit' => Unit::Milliliters,
                    'quantity' => 7.5,
                ],
                [
                    'title' => 'große Zwiebeln , klein gehackt',
                    'unit' => Unit::Pieces,
                    'quantity' => 0.25,
                ],
                [
                    'title' => 'Knoblauchzehen , klein gehackt',
                    'unit' => Unit::Pieces,
                    'quantity' => 0.5,
                ],
                [
                    'title' => 'Kreuzkümmelpulver',
                    'unit' => Unit::Milliliters,
                    'quantity' => 2.5,
                ],
                [
                    'title' => 'Zimt',
                    'unit' => Unit::Milliliters,
                    'quantity' => 0.625,
                ],
                [
                    'title' => 'Dosen Pizzatomaten  je 400 g',
                    'unit' => Unit::Pieces,
                    'quantity' => 0.5,
                ],
                [
                    'title' => 'Tassen Gemüsebrühe',
                    'unit' => Unit::Pieces,
                    'quantity' => 0.75,
                ],
                [
                    'title' => 'Dose Mais 310 g',
                    'unit' => Unit::Pieces,
                    'quantity' => 0.25,
                ],
                [
                    'title' => 'Dose Kidneybohnen 440 g',
                    'unit' => Unit::Pieces,
                    'quantity' => 0.25,
                ],
                [
                    'title' => 'Dose Kichererbsen 400 g',
                    'unit' => Unit::Pieces,
                    'quantity' => 0.25,
                ],
                [
                    'title' => 'Tomatenmark',
                    'unit' => Unit::Milliliters,
                    'quantity' => 7.5,
                ],
            ]);
        });
    });
});
