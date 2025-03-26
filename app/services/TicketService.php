<?php
namespace App\Services;

use App\Interfaces\TicketServiceInterface;
use App\Models\Ticket;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class TicketService implements TicketServiceInterface
{
    public function getTickets($perPage, $filters)
    {
        $query = Ticket::query();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['agent_id'])) {
            $query->where('agent_id', $filters['agent_id']);
        }

        return $query->paginate($perPage);
    }
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

    public function updateTicket(Ticket $ticket, array $data)
    {
        $ticket->update($data);

        return ['status' => true, 'message' => 'Ticket updated successfully', 'ticket' => $ticket];
    }

    public function deleteTicket(Ticket $ticket)
    {
        $ticket->delete();

        return ['status' => true, 'message' => 'Ticket deleted successfully'];
    }

    public function assignAgent(Ticket $ticket, $agent_id)
    {
        $ticket->update(['agent_id' => $agent_id]);

        return ['status' => true, 'message' => 'Agent assigned successfully', 'ticket' => $ticket];
    }

    public function updateStatus(Ticket $ticket, $status)
    {
        if (!in_array($status, ['open', 'in_progress', 'resolved', 'closed'])) {
            return ['status' => false, 'message' => 'Invalid status'];
        }

        $ticket->update(['status' => $status]);

        return ['status' => true, 'message' => 'Status updated successfully', 'ticket' => $ticket];
    }
}
