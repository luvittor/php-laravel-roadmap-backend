<?php

namespace Tests\Feature;

use App\Models\Card;
use App\Models\Column;
use App\Models\User;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CardAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function setUpCard(): array
    {
        $owner = User::factory()->create();
        $column = Column::factory()->for($owner)->create(['year' => 2025, 'month' => 7]);
        $card = Card::factory()->for($column)->create(['order' => 1]);
        $other = User::factory()->create();

        return [$column, $card, $owner, $other];
    }

    /* Route middleware */
    public function test_route_blocks_creating_card_into_foreign_column(): void
    {
        [$column, , , $other] = $this->setUpCard();

        $res = $this->postJson(
            '/api/v1/cards',
            ['column_id' => $column->id, 'order' => 1, 'title' => 'Test Card'],
            $this->authHeaders($other)
        );
        $res->assertForbidden();
    }

    public function test_route_blocks_viewing_foreign_card(): void
    {
        [, $card, , $other] = $this->setUpCard();

        $res = $this->getJson("/api/v1/cards/{$card->id}", $this->authHeaders($other));
        $res->assertForbidden();
    }

    public function test_route_blocks_updating_title_of_foreign_card(): void
    {
        [, $card, , $other] = $this->setUpCard();

        $res = $this->patchJson(
            "/api/v1/cards/{$card->id}/title",
            ['title' => 'New'],
            $this->authHeaders($other)
        );
        $res->assertForbidden();
    }

    public function test_route_blocks_updating_status_of_foreign_card(): void
    {
        [, $card, , $other] = $this->setUpCard();

        $res = $this->patchJson(
            "/api/v1/cards/{$card->id}/status",
            ['status' => 'completed'],
            $this->authHeaders($other)
        );
        $res->assertForbidden();
    }

    public function test_route_blocks_updating_position_of_foreign_card(): void
    {
        [, $card, , $other] = $this->setUpCard();

        $res = $this->patchJson(
            "/api/v1/cards/{$card->id}/position",
            ['year' => 2025, 'month' => 8, 'order' => 1],
            $this->authHeaders($other)
        );
        $res->assertForbidden();
    }

    public function test_route_blocks_deleting_foreign_card(): void
    {
        [, $card, , $other] = $this->setUpCard();

        $res = $this->deleteJson(
            "/api/v1/cards/{$card->id}",
            [],
            $this->authHeaders($other)
        );
        $res->assertForbidden();
    }

    /* Controller authorize */
    public function test_controller_blocks_creating_card_into_foreign_column(): void
    {
        [$column, , , $other] = $this->setUpCard();
        $this->withoutMiddleware([Authorize::class]);

        $res = $this->postJson(
            '/api/v1/cards',
            ['column_id' => $column->id, 'order' => 1, 'title' => 'Test Card'],
            $this->authHeaders($other)
        );
        $res->assertForbidden();
    }

    public function test_controller_blocks_viewing_foreign_card(): void
    {
        [, $card, , $other] = $this->setUpCard();
        $this->withoutMiddleware([Authorize::class]);

        $res = $this->getJson("/api/v1/cards/{$card->id}", $this->authHeaders($other));
        $res->assertForbidden();
    }

    public function test_controller_blocks_updating_title_of_foreign_card(): void
    {
        [, $card, , $other] = $this->setUpCard();
        $this->withoutMiddleware([Authorize::class]);

        $res = $this->patchJson(
            "/api/v1/cards/{$card->id}/title",
            ['title' => 'New'],
            $this->authHeaders($other)
        );
        $res->assertForbidden();
    }

    public function test_controller_blocks_updating_status_of_foreign_card(): void
    {
        [, $card, , $other] = $this->setUpCard();
        $this->withoutMiddleware([Authorize::class]);

        $res = $this->patchJson(
            "/api/v1/cards/{$card->id}/status",
            ['status' => 'completed'],
            $this->authHeaders($other)
        );
        $res->assertForbidden();
    }

    public function test_controller_blocks_updating_position_of_foreign_card(): void
    {
        [, $card, , $other] = $this->setUpCard();
        $this->withoutMiddleware([Authorize::class]);

        $res = $this->patchJson(
            "/api/v1/cards/{$card->id}/position",
            ['year' => 2025, 'month' => 8, 'order' => 1],
            $this->authHeaders($other)
        );
        $res->assertForbidden();
    }

    public function test_controller_blocks_deleting_foreign_card(): void
    {
        [, $card, , $other] = $this->setUpCard();
        $this->withoutMiddleware([Authorize::class]);

        $res = $this->deleteJson(
            "/api/v1/cards/{$card->id}",
            [],
            $this->authHeaders($other)
        );
        $res->assertForbidden();
    }
}
