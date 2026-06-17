<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupportController extends Controller
{
    /**
     * Display a listing of support tickets.
     */
    public function index()
    {
        $user = Auth::user();
        $tickets = $user->supportTickets()
            ->with('messages')
            ->latest()
            ->paginate(10);
        
        return view('parent.support.index', compact('tickets'));
    }
    
    /**
     * Show the form for creating a new ticket.
     */
    public function create()
    {
        return view('parent.support.create');
    }
    
    /**
     * Store a newly created ticket.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:Academic,Payment,Technical,Account,Other',
            'message' => 'required|string',
        ]);
        
        DB::beginTransaction();
        try {
            // Create ticket
            $ticket = SupportTicket::create([
                'ticket_number' => SupportTicket::generateTicketNumber(),
                'user_id' => Auth::id(),
                'title' => $validated['title'],
                'category' => $validated['category'],
                'status' => 'Open',
                'priority' => 'Medium',
            ]);
            
            // Create first message
            TicketMessage::create([
                'support_ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'message' => $validated['message'],
                'is_staff_reply' => false,
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Ticket created successfully',
                'ticket' => $ticket,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ticket: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Display the specified ticket.
     */
    public function show($id)
    {
        $user = Auth::user();
        $ticket = $user->supportTickets()
            ->with(['messages.user', 'assignedStaff'])
            ->findOrFail($id);
        
        return view('parent.support.show', compact('ticket'));
    }
    
    /**
     * Add a reply to a ticket.
     */
    public function reply(Request $request, $id)
    {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);
        
        $user = Auth::user();
        $ticket = $user->supportTickets()->findOrFail($id);
        
        // Create message
        TicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $validated['message'],
            'is_staff_reply' => false,
        ]);
        
        // Update ticket status if it was resolved
        if ($ticket->status === 'Resolved' || $ticket->status === 'Closed') {
            $ticket->update(['status' => 'Awaiting Response']);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Reply sent successfully',
        ]);
    }
}
