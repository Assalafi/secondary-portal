<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketMessage;
use App\Models\TicketAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SupportController extends Controller
{
    /**
     * Display a listing of tickets assigned to the staff member.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = SupportTicket::where('assigned_to', $user->id)
            ->with(['user', 'messages'])
            ->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
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
            'total' => SupportTicket::where('assigned_to', $user->id)->count(),
            'open' => SupportTicket::where('assigned_to', $user->id)->where('status', 'Open')->count(),
            'awaiting' => SupportTicket::where('assigned_to', $user->id)->where('status', 'Awaiting Response')->count(),
            'in_progress' => SupportTicket::where('assigned_to', $user->id)->where('status', 'In Progress')->count(),
            'resolved' => SupportTicket::where('assigned_to', $user->id)->where('status', 'Resolved')->count(),
            'closed' => SupportTicket::where('assigned_to', $user->id)->where('status', 'Closed')->count(),
        ];

        return view('staff.support.index', compact('tickets', 'stats'));
    }

    /**
     * Display the specified ticket.
     */
    public function show($id)
    {
        $user = Auth::user();
        
        // Verify ticket is assigned to this staff member
        $ticket = SupportTicket::where('assigned_to', $user->id)
            ->with(['user', 'messages.user', 'messages.attachments'])
            ->findOrFail($id);

        return view('staff.support.show', compact('ticket'));
    }

    /**
     * Add a reply to a ticket.
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|max:10240|mimes:jpeg,png,jpg,pdf,doc,docx,xls,xlsx,zip,rar',
        ]);

        $user = Auth::user();
        $ticket = SupportTicket::where('assigned_to', $user->id)->findOrFail($id);

        DB::beginTransaction();
        try {
            // Create message
            $message = TicketMessage::create([
                'support_ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'message' => $request->message,
                'is_staff_reply' => true,
            ]);

            // Handle file attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('ticket-attachments', 'public');
                    TicketAttachment::create([
                        'ticket_message_id' => $message->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

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
        ]);

        $user = Auth::user();
        $ticket = SupportTicket::where('assigned_to', $user->id)->findOrFail($id);

        $data = ['status' => $request->status];

        // Set resolved_at if status is Resolved
        if ($request->status === 'Resolved' && !$ticket->resolved_at) {
            $data['resolved_at'] = now();
        }

        $ticket->update($data);

        return back()->with('success', 'Ticket status updated successfully!');
    }
}
