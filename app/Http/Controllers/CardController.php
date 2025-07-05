<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Services\CardService;
use App\Services\ColumnService;
use App\Http\Requests\PositionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CardController extends Controller
{
    public function __construct(private CardService $cards, private ColumnService $columns)
    {
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'column_id' => 'required|exists:columns,id',
            'order'     => 'required|integer|min:1',
            'title'     => 'nullable|string',
        ]);

        $column = $this->columns->find($data['column_id']);
        $this->authorize('create', [Card::class, $column]);

        $card = $this->cards->create($data);

        return response()
            ->json($card, Response::HTTP_CREATED)
            ->header('Location', '/api/v1/cards/' . $card->id);
    }

    public function show(Card $card): JsonResponse
    {
        $this->authorize('view', $card);

        return response()->json($card);
    }

    public function updateTitle(Request $request, Card $card): JsonResponse
    {
        $this->authorize('update', $card);

        $data = $request->validate([
            'title' => 'required|string|min:0|max:255',
        ]);

        $card = $this->cards->updateTitle($card, $data['title']);

        return response()->json($card);
    }

    public function updateStatus(Request $request, Card $card): JsonResponse
    {
        $this->authorize('update', $card);

        $data = $request->validate([
            'status' => 'required|in:not_started,in_progress,completed',
        ]);

        $card = $this->cards->updateStatus($card, $data['status']);

        return response()->json($card);
    }

    public function updatePosition(PositionRequest $request, Card $card): JsonResponse
    {
        $this->authorize('update', $card);

        $data = $request->validated();

        $column = $this->columns->firstOrCreate($data['year'], $data['month'], $request->user()->id);

        $card = $this->cards->updatePosition($card, $column, $data['order']);

        return response()->json($card);
    }

    public function destroy(Card $card): Response
    {
        $this->authorize('delete', $card);

        $this->cards->delete($card);

        return response()->noContent();
    }
}
