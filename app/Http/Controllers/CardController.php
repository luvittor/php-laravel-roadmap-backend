<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Services\CardService;
use App\Services\ColumnService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CardController extends Controller
{
    public function __construct(private CardService $cards, private ColumnService $columns)
    {
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'column_id' => 'required|exists:columns,id',
            'order'     => 'required|integer|min:1',
            'title'     => 'nullable|string',
        ]);

        $card = $this->cards->create($data);

        return response()
            ->json($card, Response::HTTP_CREATED)
            ->header('Location', '/api/v1/cards/' . $card->id);
    }

    public function show(Card $card)
    {
        return response()->json($card);
    }

    public function updateTitle(Request $request, Card $card)
    {
        $data = $request->validate([
            'title' => 'required|string|min:0|max:255',
        ]);

        $card = $this->cards->updateTitle($card, $data['title']);

        return response()->json($card);
    }

    public function updateStatus(Request $request, Card $card)
    {
        $data = $request->validate([
            'status' => 'required|in:not_started,in_progress,completed',
        ]);

        $card = $this->cards->updateStatus($card, $data['status']);

        return response()->json($card);
    }

    public function updatePosition(Request $request, Card $card)
    {
        $data = $request->validate([
            'year'  => 'required|integer',
            'month' => 'required|integer',
            'order' => 'required|integer|min:1',
        ]);

        $column = $this->columns->findForUser($data['year'], $data['month'], $request->user()->id);

        if (! $column) {
            return response()->json(['message' => 'Column not found'], Response::HTTP_NOT_FOUND);
        }

        $card = $this->cards->updatePosition($card, $column, $data['order']);

        return response()->json($card);
    }

    public function destroy(Card $card)
    {
        $this->cards->delete($card);

        return response()->noContent();
    }
}
