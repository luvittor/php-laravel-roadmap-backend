<?php

namespace App\Http\Controllers;

use App\Http\Requests\YearMonthRequest;
use App\Services\ColumnService;
use Illuminate\Http\JsonResponse;

class ColumnController extends Controller
{
    public function __construct(private ColumnService $columns) {}

    public function cards(YearMonthRequest $request): JsonResponse
    {
        $data = $request->validated();
        $column = $this->columns->firstOrCreate($data['year'], $data['month'], $request->user()->id);

        $cards = $column->cards()
            ->orderBy('order')
            ->orderBy('title')
            ->get();

        return response()->json([
            'column' => $column,
            'cards' => $cards,
        ]);
    }
}
