<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $column_id
 * @property int $order
 * @property string $title
 * @property string $status
 */
class Card extends Model
{
    use HasFactory;

    protected $table = 'cards';

    protected $fillable = [
        'column_id',
        'order',
        'title',
        'status',
    ];

    /**
     * Get the column that owns the card.
     */
    public function column(): BelongsTo
    {
        return $this->belongsTo(Column::class);
    }
}
