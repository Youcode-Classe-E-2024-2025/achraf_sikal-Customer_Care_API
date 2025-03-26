<?php
namespace App\Services;

use App\Interfaces\TicketServiceInterface;
use App\Models\Ticket;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class TicketService implements TicketServiceInterface
{
    /**
     * Retrieve paginated tickets with optional filters.
     *
     * @param int $perPage The number of tickets per page.
     * @param array $filters Optional filters for ticket retrieval (e.g., status, agent_id).
     * @return \Illuminate\Pagination\LengthAwarePaginator Paginated list of tickets.
     */
    public function getTickets($perPage, $filters): array
    {
        $query = Ticket::query();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['agent_id'])) {
            $query->where('agent_id', $filters['agent_id']);
        }
        $tickets = $query->paginate($perPage);

        return [
            'data' => $tickets->items(),
            'current_page' => $tickets->currentPage(),
            'total_pages' => $tickets->lastPage(),
            'total_items' => $tickets->total(),
            'per_page' => $tickets->perPage(),
        ];
    }
    /**
     * Create a new ticket.
     *
     * @param array $data The ticket details including user_id, agent_id, title, description, and status.
     * @return Ticket The created ticket instance.
     * @throws ValidationException If validation fails.
     */
    public function createTicket(array $data)
    {
        $validator = Validator::make($data, [
            'user_id' => 'required|integer|exists:users,id',
            'agent_id' => 'required|integer',
            'title' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'status' => 'required|string|max:25',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return Ticket::create($data);
    }
    public function getTicketById(Ticket $ticket)
    {
        return [
            'status' => true,
            'message' => 'Ticket retrieved successfully',
            'ticket' => $ticket
        ];
    }
    /**
     * Update a ticket with the given data.
     *
     * @param Ticket $ticket The ticket instance to update.
     * @param array $data The new data to update the ticket with.
     * @return array The status, message, and updated ticket.
     */
    public function updateTicket(Ticket $ticket, array $data)
    {
        $validator = Validator::make($data, [
            'user_id' => 'required|integer|exists:users,id',
            'agent_id' => 'required|integer',
            'title' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'status' => 'required|string|max:25',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        $ticket->update($data);

        return [
            'status' => true,
            'message' => 'Ticket updated successfully',
            'ticket' => $ticket
        ];
    }
    /**
     * Delete a ticket.
     *
     * @param Ticket $ticket The ticket instance to delete.
     * @return array The status and deletion confirmation message.
     */
    public function deleteTicket(Ticket $ticket)
    {
        $ticket->delete();

        return [
            'status' => true,
            'message' => 'Ticket deleted successfully'
        ];
    }
    /**
     * Assign an agent to a ticket.
     *
     * @param Ticket $ticket The ticket instance to update.
     * @param int $agent_id The ID of the agent to assign.
     * @return array The status, message, and updated ticket.
     */
    public function assignAgent(Ticket $ticket, $agent_id)
    {
        $ticket->update(['agent_id' => $agent_id]);

        return [
            'status' => true,
            'message' => 'Agent assigned successfully',
            'ticket' => $ticket
        ];
    }
    /**
     * Update the status of a ticket.
     *
     * @param Ticket $ticket The ticket instance to update.
     * @param string $status The new status of the ticket (must be 'open', 'in_progress', 'resolved', or 'closed').
     * @return array The status, message, and updated ticket, or an error message if the status is invalid.
     */
    public function updateStatus(Ticket $ticket, $status)
    {
        if (!in_array($status, ['open', 'in_progress', 'resolved', 'closed'])) {
            return [
                'status' => false,
                'message' => 'Invalid status'
            ];
        }

        $ticket->update(['status' => $status]);

        return [
            'status' => true,
            'message' => 'Status updated successfully',
            'ticket' => $ticket
        ];
    }
}
