<?php

namespace Tests\Feature;

use App\Models\Card;
use App\Models\Column;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CardApiTest extends TestCase
{
    use RefreshDatabase;

    private function authHeaders(User $user): array
    {
        $token = $user->createToken('api')->plainTextToken;
        return ['Authorization' => 'Bearer ' . $token];
    }

    public function test_fetch_cards_returns_cards_for_column(): void
    {
        $user = User::factory()->create();
        $column = Column::factory()->for($user)->create(['year' => 2025, 'month' => 6]);
        $cardA = Card::factory()->for($column)->create(['order' => 1]);
        $cardB = Card::factory()->for($column)->create(['order' => 2]);

        $res = $this->getJson("/api/v1/columns/{$column->year}/{$column->month}/cards", $this->authHeaders($user));
        $res->assertOk();
        $res->assertJsonCount(2);
        $res->assertJsonPath('0.id', $cardA->id);
        $res->assertJsonPath('1.id', $cardB->id);
    }

    public function test_card_lifecycle(): void
    {
        $user = User::factory()->create();
        $column1 = Column::factory()->for($user)->create(['year' => 2025, 'month' => 6]);
        $headers = $this->authHeaders($user);

        $res = $this->postJson('/api/v1/cards', [
            'column_id' => $column1->id,
            'order' => 1,
            'title' => 'My Card',
        ], $headers);
        $res->assertCreated();
        $cardId = $res->json('id');

        $this->patchJson("/api/v1/cards/{$cardId}/title", ['title' => 'Updated'], $headers)
            ->assertOk()
            ->assertJsonPath('title', 'Updated');

        $this->patchJson("/api/v1/cards/{$cardId}/status", ['status' => 'completed'], $headers)
            ->assertOk()
            ->assertJsonPath('status', 'completed');

        $column2 = Column::factory()->for($user)->create(['year' => 2025, 'month' => 7]);
        $this->patchJson("/api/v1/cards/{$cardId}/position", [
            'year' => 2025,
            'month' => 7,
            'order' => 1,
        ], $headers)
            ->assertOk()
            ->assertJsonPath('column_id', $column2->id);

        $this->deleteJson("/api/v1/cards/{$cardId}", [], $headers)
            ->assertNoContent();

        $this->assertDatabaseMissing('cards', ['id' => $cardId]);
    }
}
