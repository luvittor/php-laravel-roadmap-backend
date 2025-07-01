<?php

namespace App\Http\Controllers;

use App\Services\ColumnService;
use Illuminate\Http\Request;

class ColumnController extends Controller
{
    public function __construct(private ColumnService $columns)
    {
    }

    public function cards(Request $request, int $year, int $month)
    {
        $column = $this->columns->findOrCreate($year, $month, $request->user()->id);

        $cards = $column->cards()
            ->orderBy('order')
            ->orderBy('title')
            ->get();

        return response()->json($cards);
    }
}