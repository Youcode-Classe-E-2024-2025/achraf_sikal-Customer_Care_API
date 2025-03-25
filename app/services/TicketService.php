<?php
namespace App\Services;

use App\Models\Ticket;
use Illuminate\Support\Facades\Validator;

class TicketService
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
            'title' => 'required|string|max:50',
            'description' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return ['status' => false, 'message' => 'Validation error', 'errors' => $validator->errors()];
        }

        $ticket = Ticket::create($data);

        return ['status' => true, 'message' => 'Ticket created successfully', 'ticket' => $ticket];
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
