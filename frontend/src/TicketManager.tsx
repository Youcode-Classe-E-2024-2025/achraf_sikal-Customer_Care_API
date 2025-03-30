import { useEffect, useState } from "react";
import axios from "axios";

export default function TicketManager() {
    const [tickets, setTickets] = useState([]);
    const [newTicket, setNewTicket] = useState({ title: "", description: "" });

    useEffect(() => {
        fetchTickets();
    }, []);

    const fetchTickets = async () => {
        try {
            const response = await axios.get("http://localhost:8000/api/tickets");
            setTickets(response.data);
        } catch (error) {
            console.error("Error fetching tickets", error);
        }
    };

    const createTicket = async () => {
        try {
            const response = await axios.post("http://localhost:8000/api/tickets", newTicket);
            setTickets([...tickets, response.data.ticket]);
            setNewTicket({ title: "", description: "" });
        } catch (error) {
            console.error("Error creating ticket", error);
        }
    };

    const assignAgent = async (ticketId, agentId) => {
        try {
            await axios.post(`http://localhost:8000/api/tickets/${ticketId}/assign/${agentId}`);
            fetchTickets();
        } catch (error) {
            console.error("Error assigning agent", error);
        }
    };

    const deleteTicket = async (ticketId) => {
        try {
            await axios.delete(`http://localhost:8000/api/tickets/${ticketId}`);
            setTickets(tickets.filter(ticket => ticket.id !== ticketId));
        } catch (error) {
            console.error("Error deleting ticket", error);
        }
    };

    return (
        <div className="p-6 bg-gray-100 min-h-screen flex flex-col items-center">
            <div className="bg-white shadow-lg rounded-lg p-6 w-full max-w-2xl">
                <h1 className="text-3xl font-bold text-center mb-4 text-blue-600">Ticket Management</h1>

                <div className="mb-6 flex flex-col gap-4">
                    <input
                        type="text"
                        placeholder="Title"
                        value={newTicket.title}
                        onChange={(e) => setNewTicket({ ...newTicket, title: e.target.value })}
                        className="border p-2 rounded w-full"
                    />
                    <input
                        type="text"
                        placeholder="Description"
                        value={newTicket.description}
                        onChange={(e) => setNewTicket({ ...newTicket, description: e.target.value })}
                        className="border p-2 rounded w-full"
                    />
                    <button onClick={createTicket} className="bg-blue-500 text-white p-2 rounded hover:bg-blue-600 transition">Create Ticket</button>
                </div>

                <ul className="space-y-4">
                    {tickets.map(ticket => (
                        <li key={ticket.id} className="border p-4 rounded shadow-sm flex justify-between items-center bg-gray-50">
                            <div>
                                <p className="text-lg font-semibold">{ticket.title}</p>
                                <p className="text-gray-600 text-sm">{ticket.description}</p>
                            </div>
                            <div className="flex gap-2">
                                <button onClick={() => assignAgent(ticket.id, 1)} className="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 transition">Assign</button>
                                <button onClick={() => deleteTicket(ticket.id)} className="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition">Delete</button>
                            </div>
                        </li>
                    ))}
                </ul>
            </div>
        </div>
    );
}
