<?php

namespace App\Http\Controllers;

use App\Services\ColumnService;
use App\Http\Requests\YearMonthRequest;

class ColumnController extends Controller
{
    public function __construct(private ColumnService $columns)
    {
    }

    public function cards(YearMonthRequest $request)
    {
        $data = $request->validated();
        $column = $this->columns->findOrCreate($data['year'], $data['month'], $request->user()->id);

        $cards = $column->cards()
            ->orderBy('order')
            ->orderBy('title')
            ->get();

        return response()->json($cards);
    }
}
