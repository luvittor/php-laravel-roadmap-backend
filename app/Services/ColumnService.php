<?php

namespace App\Services;

use App\Models\Column;

class ColumnService
{
    public function firstOrCreate(int $year, int $month, int $userId): ?Column
    {
        try {
            return Column::firstOrCreate([
                'year'    => $year,
                'month'   => $month,
                'user_id' => $userId,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle unique constraint violation by retrying the find
            if ($e->getCode() === '23000') {
                return Column::where([
                    'year'    => $year,
                    'month'   => $month,
                    'user_id' => $userId,
                ])->first();
            }
            throw $e;
        }
    }

    public function find($columnId): ?Column
    {
        return Column::find($columnId);
    }
}
