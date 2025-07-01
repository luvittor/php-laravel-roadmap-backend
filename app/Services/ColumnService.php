<?php

namespace App\Services;

use App\Models\Column;

class ColumnService
{
    public function findOrCreate(int $year, int $month, int $userId): ?Column
    {
        $column = Column::where([
            'year'    => $year,
            'month'   => $month,
            'user_id' => $userId,
        ])->first();

        if (!$column) {
            $column = Column::create([
                'year'    => $year,
                'month'   => $month,
                'user_id' => $userId,
            ]);
        }

        return $column;
    }
  
}
