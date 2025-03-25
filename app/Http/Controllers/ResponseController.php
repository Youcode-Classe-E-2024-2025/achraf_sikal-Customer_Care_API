<?php

namespace App\Http\Controllers;

use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ResponseResource;

class ResponseController extends Controller
{
    /**
     * Display a listing of responses for a ticket.
     *
     * @OA\Get(
     *     path="/api/tickets/{ticket_id}/responses",
     *     summary="Get responses for a ticket",
     *     tags={"Responses"},
     *     @OA\Parameter(
     *         name="ticket_id",
     *         in="path",
     *         required=true,
     *         description="Ticket ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of responses",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Responses retrieved successfully"),
     *             @OA\Property(property="responses", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="ticket_id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=1),
     *                     @OA\Property(property="message", type="string", example="This is a response."),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-03-25T00:00:00"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-03-25T00:00:00")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ticket not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Ticket not found")
     *         )
     *     )
     * )
     */
    public function index($ticket_id): JsonResponse
    {
        $responses = Response::where('ticket_id', $ticket_id)->with('user')->get();

        return response()->json([
            'status' => true,
            'message' => 'Responses retrieved successfully',
            'responses' => ResponseResource::collection($responses)
        ], 200);
    }

    /**
     * Store a newly created response.
     *
     * @OA\Post(
     *     path="/api/responses",
     *     summary="Create a new response",
     *     tags={"Responses"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="ticket_id", type="integer", example=1),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="message", type="string", example="This is a new response")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Response created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Response added successfully"),
     *             @OA\Property(property="response", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="ticket_id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="message", type="string", example="This is a new response")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error")
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string'
        ]);

        $response = Response::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Response added successfully',
            'response' => new ResponseResource($response)
        ], 201);
    }

    /**
     * Update an existing response.
     *
     * @OA\Put(
     *     path="/api/responses/{response}",
     *     summary="Update a response",
     *     tags={"Responses"},
     *     @OA\Parameter(
     *         name="response",
     *         in="path",
     *         required=true,
     *         description="Response ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Updated response message")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Response updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Response updated successfully"),
     *             @OA\Property(property="response", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="ticket_id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="message", type="string", example="Updated response message")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error")
     *         )
     *     )
     * )
     */
    public function update(Request $request, Response $response): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string'
        ]);

        $response->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Response updated successfully',
            'response' => new ResponseResource($response)
        ], 200);
    }

    /**
     * Remove a response.
     *
     * @OA\Delete(
     *     path="/api/responses/{response}",
     *     summary="Delete a response",
     *     tags={"Responses"},
     *     @OA\Parameter(
     *         name="response",
     *         in="path",
     *         required=true,
     *         description="Response ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Response deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Response not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Response not found")
     *         )
     *     )
     * )
     */
    public function destroy(Response $response): JsonResponse
    {
        $response->delete();

        return response()->json([
            'status' => true,
            'message' => 'Response deleted successfully'
        ], 204);
    }
}

