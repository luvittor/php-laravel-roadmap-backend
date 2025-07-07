<?php

namespace App\Policies;

use App\Models\Card;
use App\Models\Column;
use App\Models\User;

class CardPolicy
{
    /**
     * Determine whether the user can create a card in the column.
     */
    public function create(User $user, Column $column): bool
    {
        return $column->user_id === $user->id;
    }

    /**
     * Determine whether the user can view the card.
     */
    public function view(User $user, Card $card): bool
    {
        return $card->column->user_id === $user->id;
    }

    /**
     * Determine whether the user can update the card.
     */
    public function update(User $user, Card $card): bool
    {
        return $card->column->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the card.
     */
    public function delete(User $user, Card $card): bool
    {
        return $card->column->user_id === $user->id;
    }
}
