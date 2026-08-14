<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Client;
use App\Models\Contact;
use App\Models\Ticket;
use App\Models\TicketDepartment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Ticket::class);

        $tickets = Ticket::query()->with(['client', 'department', 'assignee'])->latest();
        if (! $request->user()->can('tickets.view_all')) {
            $tickets->where(fn ($query) => $query->where('created_by', $request->user()->id)->orWhere('assigned_to', $request->user()->id));
        }

        return view('tickets.index', ['tickets' => $tickets->paginate(20)]);
    }

    public function create(): View
    {
        $this->authorize('create', Ticket::class);

        return view('tickets.create', $this->formOptions());
    }

    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $ticket = DB::transaction(function () use ($request): Ticket {
            $data = $request->validated();
            $message = $data['message'] ?? null;
            unset($data['message']);
            $data['number'] = 'TKT-'.now()->format('YmdHis').'-'.str_pad((string) random_int(1, 999), 3, '0', STR_PAD_LEFT);
            $data['source'] = 'manual';
            $data['created_by'] = $request->user()->id;
            $ticket = Ticket::create($data);
            if ($message) {
                $ticket->replies()->create(['user_type' => 'staff', 'user_id' => $request->user()->id, 'message' => $message]);
            }

            return $ticket;
        });

        return to_route('tickets.show', $ticket)->with('status', 'Ticket berhasil dibuat.');
    }

    public function show(Ticket $ticket): View
    {
        $this->authorize('view', $ticket);
        $ticket->load(['client', 'contact', 'department', 'assignee', 'creator', 'replies']);

        return view('tickets.show', compact('ticket'));
    }

    public function edit(Ticket $ticket): View
    {
        $this->authorize('update', $ticket);

        return view('tickets.edit', array_merge(['ticket' => $ticket], $this->formOptions()));
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        $ticket->update($request->validated());

        return to_route('tickets.show', $ticket)->with('status', 'Ticket berhasil diperbarui.');
    }

    public function destroy(Ticket $ticket): RedirectResponse
    {
        $this->authorize('delete', $ticket);
        $ticket->delete();

        return to_route('tickets.index')->with('status', 'Ticket telah diarsipkan.');
    }

    private function formOptions(): array
    {
        return [
            'clients' => Client::query()->active()->orderBy('company_name')->get(),
            'contacts' => Contact::query()->with('client')->where('is_active', true)->orderBy('first_name')->get(),
            'departments' => TicketDepartment::query()->orderBy('name')->get(),
            'staff' => User::query()->orderBy('name')->get(),
        ];
    }
}
