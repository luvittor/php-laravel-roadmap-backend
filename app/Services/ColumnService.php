<?php

namespace App\Services;

use App\Models\Column;

class ColumnService
{
    public function findForUser(int $year, int $month, int $userId): ?Column
    {
        return Column::where([
            'year'    => $year,
            'month'   => $month,
            'user_id' => $userId,
        ])->first();
    }
}
