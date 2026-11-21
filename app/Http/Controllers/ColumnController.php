<?php

namespace App\Http\Controllers;

use App\Http\Requests\YearMonthRequest;
use App\Models\Column;
use App\Services\ColumnService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class ColumnController extends Controller
{
    public function __construct(private ColumnService $columns) {}

    /**
     * @OA\Get(
     *     path="/api/v1/columns/{year}/{month}/cards",
     *     summary="List cards for a month",
     *     description="Retrieve an existing column and its cards ordered by position. Returns a null column without creating one when the month is empty.",
     *     tags={"columns", "cards"},
     *     security={{"SanctumToken": {}}},
     *
     *     @OA\Parameter(ref="#/components/parameters/YearParam"),
     *     @OA\Parameter(ref="#/components/parameters/MonthParam"),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Column with cards",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ColumnWithCardsResponse")
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         ref="#/components/responses/Unauthenticated"
     *     )
     * )
     */
    public function cards(YearMonthRequest $request): JsonResponse
    {
        $data = $request->validated();
        $column = Column::query()
            ->where('year', $data['year'])
            ->where('month', $data['month'])
            ->where('user_id', $request->user()->id)
            ->first();

        $cards = $column?->cards()->orderBy('order')->orderBy('title')->get() ?? collect();

        return response()->json([
            'column' => $column,
            'cards' => $cards,
        ]);
    }

    public function ensure(YearMonthRequest $request): JsonResponse
    {
        $data = $request->validated();
        $column = $this->columns->firstOrCreate($data['year'], $data['month'], $request->user()->id);

        return response()->json($column);
    }
}
