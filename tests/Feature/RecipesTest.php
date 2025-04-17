<?php

use App\Livewire\Recipes\CreateEdit;
use App\Models\Enums\Unit;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\User;
use function Pest\Laravel\actingAs;
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
});
