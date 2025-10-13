<?php

namespace App\Http\Controllers;

use App\Http\Requests\PositionRequest;
use App\Models\Card;
use App\Services\CardService;
use App\Services\ColumnService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class CardController extends Controller
{
    public function __construct(private CardService $cards, private ColumnService $columns) {}

    /**
     * @OA\Post(
     *     path="/api/v1/cards",
     *     summary="Create a card",
     *     tags={"cards"},
     *     security={{"SanctumToken": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/CreateCardRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Card created",
     *
     *         @OA\Header(
     *             header="Location",
     *             description="URL to the newly created card resource",
     *
     *             @OA\Schema(type="string", format="uri")
     *         ),
     *
     *         @OA\JsonContent(ref="#/components/schemas/Card")
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         ref="#/components/responses/Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         ref="#/components/responses/Forbidden"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         ref="#/components/responses/ValidationError"
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/v1/cards/{card}",
     *     summary="Retrieve a card",
     *     tags={"cards"},
     *     security={{"SanctumToken": {}}},
     *
     *     @OA\Parameter(ref="#/components/parameters/CardIdParam"),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Card details",
     *
     *         @OA\JsonContent(ref="#/components/schemas/CardWithColumn")
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         ref="#/components/responses/Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         ref="#/components/responses/Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         ref="#/components/responses/NotFound"
     *     )
     * )
     */
    public function show(Card $card): JsonResponse
    {
        $this->authorize('view', $card);

        return response()->json($card);
    }

    /**
     * @OA\Patch(
     *     path="/api/v1/cards/{card}/title",
     *     summary="Update a card title",
     *     tags={"cards"},
     *     security={{"SanctumToken": {}}},
     *
     *     @OA\Parameter(ref="#/components/parameters/CardIdParam"),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/UpdateTitleRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Updated card",
     *
     *         @OA\JsonContent(ref="#/components/schemas/CardWithColumn")
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         ref="#/components/responses/Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         ref="#/components/responses/Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         ref="#/components/responses/NotFound"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         ref="#/components/responses/ValidationError"
     *     )
     * )
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

    /**
     * @OA\Patch(
     *     path="/api/v1/cards/{card}/status",
     *     summary="Update a card status",
     *     tags={"cards"},
     *     security={{"SanctumToken": {}}},
     *
     *     @OA\Parameter(ref="#/components/parameters/CardIdParam"),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/UpdateStatusRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Updated card",
     *
     *         @OA\JsonContent(ref="#/components/schemas/CardWithColumn")
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         ref="#/components/responses/Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         ref="#/components/responses/Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         ref="#/components/responses/NotFound"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         ref="#/components/responses/ValidationError"
     *     )
     * )
     */
    public function updateStatus(Request $request, Card $card): JsonResponse
    {
        $this->authorize('update', $card);

        $data = $request->validate([
            'status' => 'required|in:not_started,in_progress,completed',
        ]);

        $card = $this->cards->updateStatus($card, $data['status']);

        return response()->json($card);
    }

    /**
     * @OA\Patch(
     *     path="/api/v1/cards/{card}/position",
     *     summary="Move a card to a different position or month",
     *     tags={"cards"},
     *     security={{"SanctumToken": {}}},
     *
     *     @OA\Parameter(ref="#/components/parameters/CardIdParam"),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/UpdatePositionRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Updated card",
     *
     *         @OA\JsonContent(ref="#/components/schemas/CardWithColumn")
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         ref="#/components/responses/Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         ref="#/components/responses/Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         ref="#/components/responses/NotFound"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         ref="#/components/responses/ValidationError"
     *     )
     * )
     */
    public function updatePosition(PositionRequest $request, Card $card): JsonResponse
    {
        $this->authorize('update', $card);

        $data = $request->validated();

        $column = $this->columns->firstOrCreate($data['year'], $data['month'], $request->user()->id);

        $card = $this->cards->updatePosition($card, $column, $data['order']);

        return response()->json($card);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/cards/{card}",
     *     summary="Delete a card",
     *     tags={"cards"},
     *     security={{"SanctumToken": {}}},
     *
     *     @OA\Parameter(ref="#/components/parameters/CardIdParam"),
     *
     *     @OA\Response(
     *         response=204,
     *         description="Card deleted",
     *
     *         @OA\JsonContent(type="object", example={})
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         ref="#/components/responses/Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         ref="#/components/responses/Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         ref="#/components/responses/NotFound"
     *     )
     * )
     */
    public function destroy(Card $card): Response
    {
        $this->authorize('delete', $card);

        $this->cards->delete($card);

        return response()->noContent();
    }
}
