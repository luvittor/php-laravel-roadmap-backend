<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Roadmap API",
 *     version="1.0.0",
 *     description="OpenAPI specification for the Roadmap backend.\nAll endpoints are prefixed with `/api/v1`."
 * )
 *
 * @OA\Server(
 *     url="./",
 *     description="Relative base path for the API"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="SanctumToken",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Sanctum"
 * )
 *
 * @OA\SecurityRequirement(
 *     {"SanctumToken": {}}
 * )

 *
 * @OA\Schema(
 *     schema="RegisterRequest",
 *     type="object",
 *     required={"name","email","password"},
 *
 *     @OA\Property(property="name", type="string", maxLength=255),
 *     @OA\Property(property="email", type="string", format="email", description="Must be unique among users."),
 *     @OA\Property(property="password", type="string", minLength=8, format="password")
 * )
 *
 * @OA\Schema(
 *     schema="LoginRequest",
 *     type="object",
 *     required={"email","password"},
 *
 *     @OA\Property(property="email", type="string", format="email"),
 *     @OA\Property(property="password", type="string", format="password")
 * )
 *
 * @OA\Schema(
 *     schema="AuthTokenResponse",
 *     type="object",
 *     required={"token","user"},
 *
 *     @OA\Property(property="token", type="string", description="Bearer token to use in the Authorization header."),
 *     @OA\Property(property="user", ref="#/components/schemas/AuthUserSummary")
 * )
 *
 * @OA\Schema(
 *     schema="AuthUserSummary",
 *     type="object",
 *     required={"id","email"},
 *
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="email", type="string", format="email")
 * )
 *
 * @OA\Schema(
 *     schema="MessageResponse",
 *     type="object",
 *     required={"message"},
 *
 *     @OA\Property(property="message", type="string")
 * )
 *
 * @OA\Schema(
 *     schema="UserProfile",
 *     type="object",
 *     required={"id","name","email","created_at","updated_at"},
 *
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="email", type="string", format="email"),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="Column",
 *     type="object",
 *     required={"id","year","month","user_id"},
 *
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="year", type="integer", minimum=2000, maximum=4000),
 *     @OA\Property(property="month", type="integer", minimum=1, maximum=12),
 *     @OA\Property(property="user_id", type="integer"),
 *     @OA\Property(property="created_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", nullable=true)
 * )
 *
 * @OA\Schema(
 *     schema="Card",
 *     type="object",
 *     required={"id","column_id","order","title","status"},
 *
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="column_id", type="integer"),
 *     @OA\Property(property="order", type="integer", minimum=1),
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(property="status", type="string", enum={"not_started","in_progress","completed"}),
 *     @OA\Property(property="created_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", nullable=true)
 * )
 *
 * @OA\Schema(
 *     schema="CardWithColumn",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/Card"),
 *         @OA\Schema(
 *             type="object",
 *
 *             @OA\Property(property="column", ref="#/components/schemas/Column")
 *         )
 *     }
 * )
 *
 * @OA\Schema(
 *     schema="ColumnWithCardsResponse",
 *     type="object",
 *     required={"column","cards"},
 *
 *     @OA\Property(property="column", ref="#/components/schemas/Column"),
 *     @OA\Property(
 *         property="cards",
 *         type="array",
 *
 *         @OA\Items(ref="#/components/schemas/Card")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="CreateCardRequest",
 *     type="object",
 *     required={"column_id","order"},
 *
 *     @OA\Property(property="column_id", type="integer", description="Must reference an existing column."),
 *     @OA\Property(property="order", type="integer", minimum=1),
 *     @OA\Property(property="title", type="string", nullable=true, description="Defaults to an empty string when omitted.")
 * )
 *
 * @OA\Schema(
 *     schema="UpdateTitleRequest",
 *     type="object",
 *     required={"title"},
 *
 *     @OA\Property(property="title", type="string", minLength=0, maxLength=255)
 * )
 *
 * @OA\Schema(
 *     schema="UpdateStatusRequest",
 *     type="object",
 *     required={"status"},
 *
 *     @OA\Property(property="status", type="string", enum={"not_started","in_progress","completed"})
 * )
 *
 * @OA\Schema(
 *     schema="UpdatePositionRequest",
 *     type="object",
 *     required={"year","month","order"},
 *
 *     @OA\Property(property="year", type="integer", minimum=2000, maximum=4000),
 *     @OA\Property(property="month", type="integer", minimum=1, maximum=12),
 *     @OA\Property(property="order", type="integer", minimum=1)
 * )
 *
 * @OA\Schema(
 *     schema="ErrorMessage",
 *     type="object",
 *     required={"message"},
 *
 *     @OA\Property(property="message", type="string")
 * )
 *
 * @OA\Schema(
 *     schema="ValidationErrors",
 *     type="object",
 *     additionalProperties={
 *         "type": "array",
 *         "items": {"type": "string"}
 *     }
 * )
 * @OA\Schema(
 *     schema="ValidationErrorResponse",
 *     type="object",
 *     required={"message","errors"},
 *
 *     @OA\Property(property="message", type="string"),
 *     @OA\Property(property="errors", ref="#/components/schemas/ValidationErrors")
 * )
 *
 * @OA\Parameter(
 *     parameter="YearParam",
 *     name="year",
 *     in="path",
 *     required=true,
 *     description="Target year for the column.",
 *
 *     @OA\Schema(type="integer", minimum=2000, maximum=4000)
 * )
 *
 * @OA\Parameter(
 *     parameter="MonthParam",
 *     name="month",
 *     in="path",
 *     required=true,
 *     description="Target month for the column.",
 *
 *     @OA\Schema(type="integer", minimum=1, maximum=12)
 * )
 *
 * @OA\Parameter(
 *     parameter="CardIdParam",
 *     name="card",
 *     in="path",
 *     required=true,
 *     description="Identifier of the card.",
 *
 *     @OA\Schema(type="integer", minimum=1)
 * )
 *
 * @OA\Response(
 *     response="Unauthenticated",
 *     description="Authentication required",
 *
 *     @OA\JsonContent(ref="#/components/schemas/ErrorMessage")
 * )
 *
 * @OA\Response(
 *     response="Forbidden",
 *     description="Action is not authorized for the current user",
 *
 *     @OA\JsonContent(ref="#/components/schemas/ErrorMessage")
 * )
 *
 * @OA\Response(
 *     response="NotFound",
 *     description="Resource not found",
 *
 *     @OA\JsonContent(ref="#/components/schemas/ErrorMessage")
 * )
 *
 * @OA\Response(
 *     response="ValidationError",
 *     description="Validation failed",
 *
 *     @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 * )
 */
class OpenApiComponents {}
