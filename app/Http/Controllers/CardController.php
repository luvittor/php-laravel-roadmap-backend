<?php

namespace App\Http\Controllers;

use App\Http\Requests\PositionRequest;
use App\Models\Card;
use App\Services\CardService;
use App\Services\ColumnService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CardController extends Controller
{
    public function __construct(private CardService $cards, private ColumnService $columns) {}

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'column_id' => 'required|exists:columns,id',
            'order' => 'required|integer|min:1',
            'title' => 'nullable|string',
        ]);

        $column = $this->columns->find($data['column_id']);
        $this->authorize('create', [Card::class, $column]);

        $card = $this->cards->create($data);

        return response()
            ->json($card, Response::HTTP_CREATED)
            ->header('Location', route('cards.show', $card));
    }

    public function show(Card $card): JsonResponse
    {
        $this->authorize('view', $card);

        return response()->json($card);
    }

    /**
     * Update a card's title.
     *
     * Validates that the request contains a `title` field (must be present and at most 255 characters),
     * normalizes a missing or non-string `title` to an empty string, updates the card's title, and returns
     * the updated card as JSON.
     *
     * @param \Illuminate\Http\Request $request Request with a `title` field (present, max 255).
     * @param \App\Models\Card $card The card to update.
     * @return \Illuminate\Http\JsonResponse JSON response containing the updated Card.
     */
    public function updateTitle(Request $request, Card $card): JsonResponse
    {
        $this->authorize('update', $card);

        $data = $request->validate([
            'title' => 'present|max:255',
        ]);

        $title = $data['title'] ?? '';
        $title = is_string($title) ? $title : '';

        $card = $this->cards->updateTitle($card, $title);

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
