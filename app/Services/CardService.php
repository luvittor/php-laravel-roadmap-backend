<?php

namespace App\Services;

use App\Models\Card;
use App\Models\Column;

class CardService
{
    public function create(array $data): Card
    {
        $card = Card::create([
            'column_id' => $data['column_id'],
            'order'     => $data['order'],
            'title'     => $data['title'] ?? '',
        ]);

        // Refresh to ensure all database defaults are loaded
        return $card->fresh();
    }

    public function updateTitle(Card $card, string $title): Card
    {
        $card->update(['title' => $title]);
        return $card;
    }

    public function updateStatus(Card $card, string $status): Card
    {
        $card->update(['status' => $status]);
        return $card;
    }

    public function updatePosition(Card $card, Column $column, int $order): Card
    {
        $card->update([
            'column_id' => $column->id,
            'order'     => $order,
        ]);

        // Reload the column relationship
        $card->load('column');

        return $card;
    }

    public function delete(Card $card): void
    {
        $card->delete();
    }
}
