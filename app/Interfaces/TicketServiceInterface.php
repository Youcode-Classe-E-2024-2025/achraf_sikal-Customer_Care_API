<?php

namespace App\Interfaces;

use App\Models\Ticket;

interface TicketServiceInterface
{
    public function getTickets($perPage, $filters);
    public function createTicket(array $data);

    public function updateTicket(Ticket $ticket, array $data);

    public function deleteTicket(Ticket $ticket);

    public function assignAgent(Ticket $ticket, $agent_id);

    public function updateStatus(Ticket $ticket, $status);
}
