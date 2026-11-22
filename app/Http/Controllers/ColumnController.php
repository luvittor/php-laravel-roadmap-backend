<?php

namespace App\Http\Controllers;

use App\Http\Requests\YearMonthRequest;
use App\Models\Column;
use App\Services\ColumnService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
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

    public function summaries(Request $request): JsonResponse
    {
        $data = $request->validate([
            'from_year' => 'required|integer|min:2000|max:2100',
            'from_month' => 'required|integer|between:1,12',
            'to_year' => 'required|integer|min:2000|max:2100',
            'to_month' => 'required|integer|between:1,12',
        ]);
        if (
            ($data['from_year'] * 12 + $data['from_month']) >
            ($data['to_year'] * 12 + $data['to_month'])
        ) {
            return response()->json([
                'message' => 'The period start must be before its end.',
            ], 422);
        }
        $counts = DB::table('columns')
            ->leftJoin('cards', 'cards.column_id', '=', 'columns.id')
            ->where('columns.user_id', $request->user()->id)
            ->whereRaw('(columns.year * 12 + columns.month) between ? and ?', [
                $data['from_year'] * 12 + $data['from_month'],
                $data['to_year'] * 12 + $data['to_month'],
            ])
            ->groupBy('columns.year', 'columns.month')
            ->selectRaw('columns.year, columns.month, COUNT(cards.id) as card_count')
            ->get()
            ->keyBy(fn ($row) => $row->year.'-'.$row->month);

        return response()->json($counts);
    }

    public function ensure(YearMonthRequest $request): JsonResponse
    {
        $data = $request->validated();
        $column = $this->columns->firstOrCreate($data['year'], $data['month'], $request->user()->id);

        return response()->json($column);
    }
}
