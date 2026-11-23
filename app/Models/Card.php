<?php

namespace App\Models;

use App\Enums\CardColor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $column_id
 * @property int $order
 * @property string $title
 * @property string $status
 * @property CardColor $color
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
        'color',
    ];

    protected $casts = [
        'id' => 'integer',
        'column_id' => 'integer',
        'order' => 'integer',
        'color' => CardColor::class,
    ];

    /**
     * Get the column that owns the card.
     */
    public function column(): BelongsTo
    {
        return $this->belongsTo(Column::class);
    }
}
