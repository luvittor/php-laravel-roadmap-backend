<?php

namespace Tests\Unit;

use App\Models\Card;
use App\Models\Column;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CardOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_cards_assigns_incremental_order(): void
    {
        $user = User::factory()->create();
        $column = Column::factory()->for($user)->create();

        $card1 = Card::factory()->for($column)->create();
        $card2 = Card::factory()->for($column)->create();

        $this->assertEquals(1, $card1->order);
        $this->assertEquals(2, $card2->order);
    }

    public function test_updating_card_order_shifts_neighbors(): void
    {
        $user = User::factory()->create();
        $column = Column::factory()->for($user)->create();

        $cardA = Card::factory()->for($column)->create();
        $cardB = Card::factory()->for($column)->create();
        $cardC = Card::factory()->for($column)->create();

        $cardC->update(['order' => 1]);
        $cardA->refresh();
        $cardB->refresh();
        $cardC->refresh();

        $this->assertEquals(1, $cardC->order);
        $this->assertEquals(2, $cardA->order);
        $this->assertEquals(3, $cardB->order);
    }
}
