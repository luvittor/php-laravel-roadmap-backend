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

    public function test_updating_card_order_shifts_neighbors_when_moving_up(): void
    {
        $user = User::factory()->create();
        $column = Column::factory()->for($user)->create();

        $cardA = Card::factory()->for($column)->create(); // order 1
        $cardB = Card::factory()->for($column)->create(); // order 2
        $cardC = Card::factory()->for($column)->create(); // order 3

        // move C from 3 → 1 (new < original)
        $cardC->update(['order' => 1]);
        $cardA->refresh();
        $cardB->refresh();
        $cardC->refresh();

        $this->assertEquals(1, $cardC->order);
        $this->assertEquals(2, $cardA->order);
        $this->assertEquals(3, $cardB->order);
    }

    public function test_updating_within_column_and_null_order_throws_exception(): void
    {
        $user = User::factory()->create();
        $column = Column::factory()->for($user)->create();

        $cardA = Card::factory()->for($column)->create(['order' => 1]);

        // Expect exception when trying to set order to null
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Order cannot be null when updating to the same column.');

        // explicitly null out order
        $cardA->update(['order' => null]);
    }

    public function test_updating_within_column_and_increasing_order_shifts_neighbors(): void
    {
        $user = User::factory()->create();
        $column = Column::factory()->for($user)->create();

        $cardA = Card::factory()->for($column)->create(['order' => 1]);
        $cardB = Card::factory()->for($column)->create(['order' => 2]);
        $cardC = Card::factory()->for($column)->create(['order' => 3]);

        // move A from 1 → 3 (new > original)
        $cardA->update(['order' => 3]);

        $cardA->refresh();
        $cardB->refresh();
        $cardC->refresh();

        // B and C should each shift down by 1
        $this->assertEquals(1, $cardB->order);
        $this->assertEquals(2, $cardC->order);
        $this->assertEquals(3, $cardA->order);
    }

    public function test_moving_to_different_column_with_specified_order_shifts_neighbors(): void
    {
        $user = User::factory()->create();
        $col1 = Column::factory()->for($user)->create();
        $col2 = Column::factory()->for($user)->create();

        // Column 1 has three cards
        $cardA = Card::factory()->for($col1)->create(['order' => 1]);
        $cardB = Card::factory()->for($col1)->create(['order' => 2]);
        $cardC = Card::factory()->for($col1)->create(['order' => 3]);

        // Column 2 has two cards
        $cardD = Card::factory()->for($col2)->create(['order' => 1]);
        $cardE = Card::factory()->for($col2)->create(['order' => 2]);

        // Move B → column 2 at position 2
        $cardB->update([
            'column_id' => $col2->id,
            'order' => 2,
        ]);

        // Refresh all
        $cardA->refresh();
        $cardB->refresh();
        $cardC->refresh();
        $cardD->refresh();
        $cardE->refresh();

        // In original column (col1): A stays 1, C (was 3) moves to 2
        $this->assertEquals(1, $cardA->order);
        $this->assertEquals(2, $cardC->order);

        // In target column (col2): D stays 1, B=2, E (was 2) moves to 3
        $this->assertEquals(1, $cardD->order);
        $this->assertEquals(2, $cardB->order);
        $this->assertEquals(3, $cardE->order);
    }

    public function test_moving_to_different_column_with_null_order_appends_to_end_and_shifts_original(): void
    {
        $user = User::factory()->create();
        $col1 = Column::factory()->for($user)->create();
        $col2 = Column::factory()->for($user)->create();

        // Column 1 has three cards
        $cardA = Card::factory()->for($col1)->create(['order' => 1]);
        $cardB = Card::factory()->for($col1)->create(['order' => 2]);
        $cardC = Card::factory()->for($col1)->create(['order' => 3]);

        // Column 2 has two cards
        $cardD = Card::factory()->for($col2)->create(['order' => 1]);
        $cardE = Card::factory()->for($col2)->create(['order' => 2]);

        // Move A → column 2 without order (null)
        $cardA->update([
            'column_id' => $col2->id,
            'order' => null,
        ]);

        // Refresh all
        $cardA->refresh();
        $cardB->refresh();
        $cardC->refresh();
        $cardD->refresh();
        $cardE->refresh();

        // Original column (col1): B=1, C=2
        $this->assertEquals(1, $cardB->order);
        $this->assertEquals(2, $cardC->order);

        // Target column (col2): D=1, E=2, A gets appended as 3
        $this->assertEquals(1, $cardD->order);
        $this->assertEquals(2, $cardE->order);
        $this->assertEquals(3, $cardA->order);
    }

    public function test_deleting_card_reorders_remaining_cards(): void
    {
        $user = User::factory()->create();
        $column = Column::factory()->for($user)->create();

        $card1 = Card::factory()->for($column)->create(['order' => 1]);
        $card2 = Card::factory()->for($column)->create(['order' => 2]);
        $card3 = Card::factory()->for($column)->create(['order' => 3]);

        $card2->delete();

        $card1->refresh();
        $card3->refresh();

        $this->assertEquals(1, $card1->order);
        $this->assertEquals(2, $card3->order);
    }
}
