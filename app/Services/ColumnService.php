<?php

namespace App\Services;

use App\Models\Column;
use Illuminate\Database\UniqueConstraintViolationException;

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
            // This is a workaround for concurrent requests that might cause a unique constraint violation
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
