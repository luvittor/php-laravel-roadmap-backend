<?php

namespace Tests\Feature;

use App\Enums\CardColor;
use App\Models\Card;
use App\Models\Column;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CardApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_fetch_cards_returns_cards_for_column(): void
    {
        // create a user and a column for that user
        $user = User::factory()->create();
        $column = Column::factory()->for($user)->create(['year' => 2025, 'month' => 6]);

        // create cards for the column
        $cardA = Card::factory()->for($column)->create([
            'order' => 1,
            'title' => 'Card A',
        ]);

        // order=1 again, this will shift the order of the cardA
        $cardB = Card::factory()->for($column)->create([
            'order' => 1,
            'title' => 'Card B',
        ]);

        // create a third card with order 3
        $cardC = Card::factory()->for($column)->create([
            'order' => 3,
            'title' => 'Card C',
        ]);

        // fetch cards for the column
        $res = $this->getJson("/api/v1/columns/{$column->year}/{$column->month}/cards", $this->authHeaders($user));

        // assert the response
        $res->assertOk();

        // check the response structure
        $res->assertJsonStructure([
            'column' => [
                'id',
                'year',
                'month',
                'user_id',
            ],
            'cards' => [
                '*' => [
                    'id',
                    'title',
                    'order',
                    'column_id',
                ],
            ],
        ]);

        // check the number of cards returned
        $res->assertJsonCount(3, 'cards');

        // check the cards are returned in the correct order, titles and ids
        $res->assertJsonPath('cards.0.id', $cardB->id);
        $res->assertJsonPath('cards.0.title', 'Card B');
        $res->assertJsonPath('cards.0.order', 1);

        $res->assertJsonPath('cards.1.id', $cardA->id);
        $res->assertJsonPath('cards.1.title', 'Card A');
        $res->assertJsonPath('cards.1.order', 2);

        $res->assertJsonPath('cards.2.id', $cardC->id);
        $res->assertJsonPath('cards.2.title', 'Card C');
        $res->assertJsonPath('cards.2.order', 3);
    }

    public function test_fetching_an_empty_month_does_not_create_a_column(): void
    {
        $user = User::factory()->create();

        $response = $this->getJson('/api/v1/columns/2027/3/cards', $this->authHeaders($user));

        $response->assertOk()->assertJsonPath('column', null)->assertJsonCount(0, 'cards');
        $this->assertDatabaseMissing('columns', [
            'year' => 2027,
            'month' => 3,
            'user_id' => $user->id,
        ]);
    }

    public function test_month_summaries_return_card_totals_without_creating_empty_columns(): void
    {
        $user = User::factory()->create();
        $column = Column::factory()->for($user)->create(['year' => 2025, 'month' => 6]);
        Card::factory()->count(2)->for($column)->create();

        $this->getJson('/api/v1/columns/summaries?from_year=2025&from_month=5&to_year=2025&to_month=7', $this->authHeaders($user))
            ->assertOk()
            ->assertJsonPath('2025-6.card_count', 2)
            ->assertJsonMissingPath('2025-5')
            ->assertJsonMissingPath('2025-7');

        $this->assertDatabaseCount('columns', 1);
    }

    public function test_ensuring_a_month_column_creates_it_on_demand(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/v1/columns/2027/3', [], $this->authHeaders($user));

        $response->assertOk()->assertJsonPath('year', 2027)->assertJsonPath('month', 3);
        $this->assertDatabaseHas('columns', [
            'year' => 2027,
            'month' => 3,
            'user_id' => $user->id,
        ]);
    }

    public function test_show_card_returns_card(): void
    {
        $user = User::factory()->create();
        $column = Column::factory()->for($user)->create(['year' => 2025, 'month' => 6]);
        $card = Card::factory()->for($column)->create(['order' => 1, 'title' => 'My Card']);

        $res = $this->getJson("/api/v1/cards/{$card->id}", $this->authHeaders($user));

        $res->assertOk();
        $res->assertJson([
            'id' => $card->id,
            'title' => 'My Card',
            'order' => 1,
            'column_id' => $column->id,
        ]);
    }

    public function test_card_lifecycle(): void
    {
        // create a user and a column for that user
        $user = User::factory()->create();
        $column1 = Column::factory()->for($user)->create(['year' => 2025, 'month' => 6]);
        $headers = $this->authHeaders($user);

        // data for the new card
        $card = [
            'column_id' => $column1->id,
            'order' => 1,
            'title' => 'My Card',
        ];

        // create a new card
        $res = $this->postJson('/api/v1/cards', $card, $headers);
        $res->assertCreated();
        $cardId = $res->json('id');

        // change the title of the card
        $res = $this->patchJson("/api/v1/cards/{$cardId}/title", ['title' => 'Updated'], $headers);
        $res->assertOk();
        $res->assertJsonPath('title', 'Updated');

        // change the status of the card
        $res = $this->patchJson("/api/v1/cards/{$cardId}/status", ['status' => 'completed'], $headers);
        $res->assertOk();
        $res->assertJsonPath('status', 'completed');

        // move card to a different column, this will create a new column
        $res = $this->patchJson("/api/v1/cards/{$cardId}/position", [
            'year' => 2025,
            'month' => 7,
            'order' => 1,
        ], $headers);
        $res->assertOk();

        // check if the column_id has changed
        $this->assertNotEquals($column1->id, $res->json('column_id'));

        // delete the card
        $res = $this->deleteJson("/api/v1/cards/{$cardId}", [], $headers);

        // assert no content response
        $res->assertNoContent();

        // assert the card is deleted from the database
        $this->assertDatabaseMissing('cards', ['id' => $cardId]);
    }

    public function test_card_color_can_be_changed_and_persists_independently_of_status(): void
    {
        $user = User::factory()->create();
        $column = Column::factory()->for($user)->create();
        $card = Card::factory()->for($column)->create(['status' => 'in_progress']);

        foreach (CardColor::cases() as $color) {
            $this->patchJson("/api/v1/cards/{$card->id}/color", ['color' => $color->value], $this->authHeaders($user))
                ->assertOk()
                ->assertJsonPath('color', $color->value)
                ->assertJsonPath('status', 'in_progress');
        }

        $this->assertDatabaseHas('cards', ['id' => $card->id, 'color' => CardColor::Blue->value, 'status' => 'in_progress']);
    }

    public function test_cards_endpoint_returns_validation_errors_for_invalid_year_or_month(): void
    {
        $user = User::factory()->create();
        $headers = $this->authHeaders($user);

        $res = $this->getJson('/api/v1/columns/1999/13/cards', $headers);
        $res->assertStatus(422);
        $res->assertJsonValidationErrors(['year', 'month']);
    }

    public function test_update_position_returns_validation_errors_for_invalid_year_or_month(): void
    {
        $user = User::factory()->create();
        $column = Column::factory()->for($user)->create(['year' => 2025, 'month' => 6]);
        $card = Card::factory()->for($column)->create(['order' => 1]);
        $headers = $this->authHeaders($user);

        $res = $this->patchJson("/api/v1/cards/{$card->id}/position", [
            'year' => 1999,
            'month' => 13,
            'order' => 1,
        ], $headers);

        $res->assertStatus(422);
        $res->assertJsonValidationErrors(['year', 'month']);
    }
}
