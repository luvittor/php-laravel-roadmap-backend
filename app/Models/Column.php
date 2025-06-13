<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $year
 * @property int $month
 * @property int $user_id
 */
class Column extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'month',
        'user_id',
    ];

    /**
     * Get the user that owns the column.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the cards for the column.
     */
    public function cards(): HasMany
    {
        return $this->hasMany(Card::class);
    }
}
