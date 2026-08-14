<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketReplyRequest;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;

class TicketReplyController extends Controller
{
    public function store(StoreTicketReplyRequest $request, Ticket $ticket): RedirectResponse
    {
        $ticket->replies()->create([
            'user_type' => 'staff',
            'user_id' => $request->user()->id,
            'message' => $request->validated('message'),
            'is_internal_note' => $request->boolean('is_internal_note'),
        ]);

        if (! $request->boolean('is_internal_note') && $ticket->status === 'open') {
            $ticket->update(['status' => 'answered']);
        }

        return back()->with('status', $request->boolean('is_internal_note') ? 'Catatan internal ditambahkan.' : 'Balasan dikirim.');
    }
}
