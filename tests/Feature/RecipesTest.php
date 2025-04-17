<?php

use App\Livewire\Recipes\CreateEdit;
use App\Models\Enums\Unit;
use App\Models\Grouping;
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
    });

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
                ]
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
                ]
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
                ]
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
                ]
            ])
            ->call('store')
            ->assertRedirect(route('recipes.view', [
                'recipe' => Recipe::whereTitle($testTitle)->firstOrFail(),
            ]));
    });

    describe('Import', function () {

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

        beforeEach(function () {
            Http::fake([
                'https://www.kitchenstories.com/de/rezepte/cremiger-nudel-kurbis-auflauf' => Http::response(file_get_contents(__DIR__.'/../fixtures/recipe-kitchestories.html')),
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
