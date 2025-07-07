<?php

namespace Tests\Feature;

use App\Models\Card;
use App\Models\Column;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CardApiSadPathTest extends TestCase
{
    use RefreshDatabase;

    public function test_fetch_cards_requires_authentication(): void
    {
        $this->getJson('/api/v1/columns/2025/6/cards')
            ->assertUnauthorized();
    }

    public function test_create_card_requires_authentication(): void
    {
        $this->postJson('/api/v1/cards', [])
            ->assertUnauthorized();
    }

    public function test_create_card_validation_errors(): void
    {
        $user = User::factory()->create();
        $headers = $this->authHeaders($user);

        $this->postJson('/api/v1/cards', [], $headers)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['column_id', 'order']);
    }

    public function test_create_card_fails_when_column_id_not_exists(): void
    {
        $user = User::factory()->create();
        $headers = $this->authHeaders($user);

        $this->postJson('/api/v1/cards', ['column_id' => 999, 'order' => 1], $headers)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['column_id']);
    }

    public function test_show_card_requires_authentication(): void
    {
        $card = Card::factory()->for(Column::factory()->for(User::factory()->create()))->create();

        $this->getJson("/api/v1/cards/{$card->id}")
            ->assertUnauthorized();
    }

    public function test_show_card_returns_not_found_for_invalid_id(): void
    {
        $user = User::factory()->create();

        $this->getJson('/api/v1/cards/999', $this->authHeaders($user))
            ->assertNotFound();
    }

    public function test_update_title_requires_authentication(): void
    {
        $card = Card::factory()->for(Column::factory()->for(User::factory()->create()))->create();

        $this->patchJson("/api/v1/cards/{$card->id}/title", [])
            ->assertUnauthorized();
    }

    public function test_update_title_validation_errors(): void
    {
        $user = User::factory()->create();
        $card = Card::factory()->for(Column::factory()->for($user)->create())->create();

        $this->patchJson("/api/v1/cards/{$card->id}/title", [], $this->authHeaders($user))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_update_title_returns_not_found_for_invalid_card_id(): void
    {
        $user = User::factory()->create();

        $this->patchJson('/api/v1/cards/999/title', ['title' => 'New'], $this->authHeaders($user))
            ->assertNotFound();
    }

    public function test_update_status_requires_authentication(): void
    {
        $card = Card::factory()->for(Column::factory()->for(User::factory()->create()))->create();

        $this->patchJson("/api/v1/cards/{$card->id}/status", [])
            ->assertUnauthorized();
    }

    public function test_update_status_validation_errors(): void
    {
        $user = User::factory()->create();
        $card = Card::factory()->for(Column::factory()->for($user)->create())->create();

        $this->patchJson("/api/v1/cards/{$card->id}/status", ['status' => 'bad'], $this->authHeaders($user))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_update_status_returns_not_found_for_invalid_card_id(): void
    {
        $user = User::factory()->create();

        $this->patchJson('/api/v1/cards/999/status', ['status' => 'completed'], $this->authHeaders($user))
            ->assertNotFound();
    }

    public function test_update_position_requires_authentication(): void
    {
        $card = Card::factory()->for(Column::factory()->for(User::factory()->create()))->create();

        $this->patchJson("/api/v1/cards/{$card->id}/position", [])
            ->assertUnauthorized();
    }

    public function test_update_position_validation_errors(): void
    {
        $user = User::factory()->create();
        $column = Column::factory()->for($user)->create(['year' => 2025, 'month' => 6]);
        $card = Card::factory()->for($column)->create(['order' => 1]);

        $this->patchJson(
            "/api/v1/cards/{$card->id}/position",
            ['year' => 2025, 'month' => 6, 'order' => 0],
            $this->authHeaders($user)
        )
            ->assertStatus(422)
            ->assertJsonValidationErrors(['order']);
    }

    public function test_update_position_returns_not_found_for_invalid_card_id(): void
    {
        $user = User::factory()->create();

        $this->patchJson(
            '/api/v1/cards/999/position',
            ['year' => 2025, 'month' => 6, 'order' => 1],
            $this->authHeaders($user)
        )
            ->assertNotFound();
    }

    public function test_delete_card_requires_authentication(): void
    {
        $card = Card::factory()->for(Column::factory()->for(User::factory()->create()))->create();

        $this->deleteJson("/api/v1/cards/{$card->id}")
            ->assertUnauthorized();
    }

    public function test_delete_card_returns_not_found_for_invalid_id(): void
    {
        $user = User::factory()->create();

        $this->deleteJson('/api/v1/cards/999', [], $this->authHeaders($user))
            ->assertNotFound();
    }
}
