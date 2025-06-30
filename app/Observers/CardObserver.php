<?php

namespace App\Observers;

use App\Models\Card;

class CardObserver
{
    public function creating(Card $card): void
    {
        if ($card->order === null) {
            $card->order = Card::where('column_id', $card->column_id)->max('order') + 1;
        } else {
            Card::where('column_id', $card->column_id)
                ->where('order', '>=', $card->order)
                ->increment('order');
        }
    }

    public function updating(Card $card): void
    {
        $originalColumn = $card->getOriginal('column_id');
        $originalOrder = $card->getOriginal('order');

        if ($card->column_id !== $originalColumn) {
            // move card to a different column or change its order

            Card::where('column_id', $originalColumn)
                ->where('order', '>', $originalOrder)
                ->decrement('order');

            if ($card->order === null) {
                $card->order = Card::where('column_id', $card->column_id)->max('order') + 1;
            } else {
                Card::where('column_id', $card->column_id)
                    ->where('order', '>=', $card->order)
                    ->increment('order');
            }

        } elseif ($card->order !== $originalOrder) {
            // if the order has changed, adjust the orders of neighboring cards

            if ($card->order === null) {
                throw new \InvalidArgumentException('Order cannot be null when updating to the same column.');
            }

            if ($card->order > $originalOrder) {
                Card::where('column_id', $card->column_id)
                    ->where('order', '<=', $card->order)
                    ->where('order', '>', $originalOrder)
                    ->decrement('order');
            } else {
                Card::where('column_id', $card->column_id)
                    ->where('order', '>=', $card->order)
                    ->where('order', '<', $originalOrder)
                    ->increment('order');
            }
        }
    }

    public function deleting(Card $card): void
    {
        Card::where('column_id', $card->column_id)
            ->where('order', '>', $card->order)
            ->decrement('order');
    }
}
