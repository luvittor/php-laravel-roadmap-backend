<?php

namespace App\Http\Controllers;

use App\Services\ColumnService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ColumnController extends Controller
{
    public function __construct(private ColumnService $columns)
    {
    }

    public function cards(Request $request, int $year, int $month)
    {
        $column = $this->columns->findForUser($year, $month, $request->user()->id);

        if (! $column) {
            return response()->json(['message' => 'Column not found'], Response::HTTP_NOT_FOUND);
        }

        $cards = $column->cards()->orderBy('order')->get();

        return response()->json($cards);
    }
}
