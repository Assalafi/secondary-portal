<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupportController extends Controller
{
    /**
     * Display a listing of all support tickets.
     */
    public function index(Request $request)
    {
        $query = SupportTicket::with(['user', 'assignedStaff', 'messages'])
            ->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Search by ticket number or title
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }

        $tickets = $query->paginate(15);

        // Get stats
        $stats = [
            'total' => SupportTicket::count(),
            'open' => SupportTicket::where('status', 'Open')->count(),
            'awaiting' => SupportTicket::where('status', 'Awaiting Response')->count(),
            'resolved' => SupportTicket::where('status', 'Resolved')->count(),
            'closed' => SupportTicket::where('status', 'Closed')->count(),
        ];

        // Get staff for assignment
        $staff = User::whereHas('staff')->get();

        return view('admin.support.index', compact('tickets', 'stats', 'staff'));
    }

    /**
     * Display the specified ticket.
     */
    public function show($id)
    {
        $ticket = SupportTicket::with(['user', 'assignedStaff', 'messages.user'])
            ->findOrFail($id);

        // Get staff for reassignment
        $staff = User::whereHas('staff')->get();

        return view('admin.support.show', compact('ticket', 'staff'));
    }

    /**
     * Assign ticket to staff.
     */
    public function assign(Request $request, $id)
    {
        $request->validate([
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $ticket = SupportTicket::findOrFail($id);
        $ticket->update([
            'assigned_to' => $request->assigned_to,
            'status' => 'In Progress',
        ]);

        return back()->with('success', 'Ticket assigned successfully!');
    }

    /**
     * Add a reply to a ticket.
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $ticket = SupportTicket::findOrFail($id);

        DB::beginTransaction();
        try {
            // Create message
            TicketMessage::create([
                'support_ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'message' => $request->message,
                'is_staff_reply' => true,
            ]);

            // Update ticket status
            if ($ticket->status === 'Open' || $ticket->status === 'Awaiting Response') {
                $ticket->update(['status' => 'In Progress']);
            }

            DB::commit();

            return back()->with('success', 'Reply sent successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to send reply. Please try again.');
        }
    }

    /**
     * Update ticket status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Open,Awaiting Response,In Progress,Resolved,Closed',
            'priority' => 'nullable|in:Low,Medium,High,Urgent',
        ]);

        $ticket = SupportTicket::findOrFail($id);

        $data = ['status' => $request->status];

        if ($request->filled('priority')) {
            $data['priority'] = $request->priority;
        }

        // Set resolved_at if status is Resolved
        if ($request->status === 'Resolved' && !$ticket->resolved_at) {
            $data['resolved_at'] = now();
        }

        $ticket->update($data);

        return back()->with('success', 'Ticket status updated successfully!');
    }

    /**
     * Delete a ticket.
     */
    public function destroy($id)
    {
        $ticket = SupportTicket::findOrFail($id);

        DB::beginTransaction();
        try {
            // Delete messages first
            $ticket->messages()->delete();
            // Delete ticket
            $ticket->delete();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Ticket deleted successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to delete ticket.'], 500);
        }
    }
}
