<?php

namespace App\Services;

use App\Models\Column;
use Illuminate\Database\Exceptions\UniqueConstraintViolationException;

class ColumnService
{
    public function firstOrCreate(int $year, int $month, int $userId): Column
    {
        try {
            return Column::firstOrCreate([
                'year' => $year,
                'month' => $month,
                'user_id' => $userId,
            ]);
        } catch (UniqueConstraintViolationException $e) {
            // Retry the find when a unique constraint is violated
            return Column::where([
                'year' => $year,
                'month' => $month,
                'user_id' => $userId,
            ])->first();
        }
    }

    public function find(int $columnId): ?Column
    {
        return Column::find($columnId);
    }
}
