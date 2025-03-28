<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use App\Interfaces\TicketServiceInterface;

/**
 * @OA\Schema(
 *     schema="Ticket",
 *     title="Ticket",
 *     description="Ticket model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="agent_id", type="integer", nullable=true, example=2),
 *     @OA\Property(property="title", type="string", example="Login issue"),
 *     @OA\Property(property="description", type="string", example="User is unable to log in due to an error."),
 *     @OA\Property(property="status", type="string", enum={"open", "in_progress", "resolved", "closed"}, example="open"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-03-24T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-03-24T10:05:00Z")
 * )
 */

class TicketController extends Controller
{
    private $ticketservice;
    public function __construct(TicketServiceInterface $ticketservice) {
        $this->ticketservice = $ticketservice;
    }
    /**
     * @OA\Post(
     *     path="/api/tickets/{ticket}/assign/{agent}",
     *     summary="Assign an agent to a ticket",
     *     tags={"Tickets"},
     *     @OA\Parameter(
     *         name="ticket",
     *         in="path",
     *         description="ID of the ticket to assign",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="agent",
     *         in="path",
     *         description="ID of the agent to assign",
     *         required=true,
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ticket assigned successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Ticket assigned successfully"),
     *             @OA\Property(property="ticket", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="agent_id", type="integer", example=2),
     *                 @OA\Property(property="status", type="string", example="open")
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
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ticket or agent not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Ticket or agent not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal Server Error")
     *         )
     *     )
     * )
     */
    public function assign(Ticket $ticket, $agent)
    {
        try {
            $ticket = $this->ticketservice->assignAgent($ticket, $agent);
            return response()->json([
                'status' => true,
                'message' => 'Ticket assigned successfully',
                'ticket' => $ticket
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    
    /**
     * @OA\Get(
     *     path="/api/Tickets",
     *     summary="Get paginated list of Tickets",
     *     tags={"Tickets"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number to retrieve",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated Tickets list",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example="1"),
     *                     @OA\Property(property="agent_id", type="integer", example="2"),
     *                     @OA\Property(property="title", type="string", example="engine problem"),
     *                     @OA\Property(property="description", type="string", example="Lorem ipsum dolor, sit amet consectetur adipisicing elit."),
     *                     @OA\Property(property="status", type="string", enum={"open", "in_progress", "resolved", "closed"}, example="open")
     *                 )
     *             ),
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="total_pages", type="integer", example=5),
     *             @OA\Property(property="total_items", type="integer", example=15),
     *             @OA\Property(property="per_page", type="integer", example=3)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal Server Error")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $data = $this->ticketservice->getTickets(3, ['status' => "open"]);

        return response()->json($data, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/Tickets",
     *     summary="Create a new ticket",
     *     tags={"Tickets"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="ticket data",
     *         @OA\JsonContent(
     *             required={"user_id", "agent_id", "title", "description", "status"},
     *             @OA\Property(property="user_id", type="integer", example="1"),
     *             @OA\Property(property="agent_id", type="integer", example="2"),
     *             @OA\Property(property="title", type="string", example="engine problem"),
     *             @OA\Property(property="description", type="string", example="Lorem ipsum dolor, sit amet consectetur adipisicing elit."),
     *             @OA\Property(property="status", type="string", enum={"open", "in_progress", "resolved", "closed"}, example="open")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="ticket created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="ticket Created Successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Server error")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $ticket = $this->ticketservice->createTicket($request->all());
        try {
            return response()->json(['status' => true, 'message' => 'Ticket created successfully', 'ticket' => $ticket],201);
        } catch (ValidationException $e) {
            return response()->json(['status' => false, 'message' => 'Validation error', 'errors' => $e], 400);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/Tickets/{id}",
     *     summary="Retrieve ticket details",
     *     tags={"Tickets"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the ticket to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ticket retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Ticket retrieved successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Ticket not found")
     * )
     */
    public function show(Ticket $ticket)
    {
        $data = $this->ticketservice->getTicketById($ticket);
        return response()->json($data, 200);
    }

    /**
     * @OA\Put(
     *     path="/api/tickets/{id}",
     *     summary="Update an existing ticket",
     *     tags={"Tickets"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the ticket to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated ticket data",
     *         @OA\JsonContent(
     *             required={"title", "description", "status"},
     *             @OA\Property(property="title", type="string", example="Issue with login"),
     *             @OA\Property(property="description", type="string", example="User is unable to log in due to an unknown error."),
     *             @OA\Property(property="status", type="string", enum={"open", "in_progress", "resolved", "closed"}, example="in_progress"),
     *             @OA\Property(property="agent_id", type="integer", nullable=true, example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ticket updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Ticket updated successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Ticket not found"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function update(Request $request, Ticket $ticket)
    {
        $updatedticket = $this->ticketservice->updateTicket($ticket, $request->all());
        try {
            return response()->json($updatedticket, 200);
        } catch (ValidationException $e) {
            return response()->json(['status' => false, 'message' => 'Validation error', 'errors' => $e], 400);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/tickets/{id}",
     *     summary="Delete a ticket",
     *     tags={"Tickets"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the ticket to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Ticket deleted successfully"),
     *     @OA\Response(response=404, description="Ticket not found"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function destroy(Ticket $ticket)
    {
        $deletedticket = $this->ticketservice->deleteTicket($ticket);
        return response()->json($deletedticket, 204);
    }
}
