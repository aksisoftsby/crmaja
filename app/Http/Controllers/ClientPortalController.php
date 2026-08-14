<?php

namespace App\Http\Controllers;

use App\Models\Estimate;
use App\Models\Invoice;
use App\Models\KbArticle;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\Ticket;
use App\Models\TicketDepartment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ClientPortalController extends Controller
{
    public function dashboard(): View
    {
        $contact = Auth::guard('portal')->user();
        $clientId = $contact->client_id;

        return view('portal.dashboard', [
            'contact' => $contact,
            'invoiceCount' => Invoice::query()->where('client_id', $clientId)->count(),
            'openTicketCount' => Ticket::query()->where('client_id', $clientId)->whereNotIn('status', ['closed'])->count(),
            'projectCount' => Project::query()->where('client_id', $clientId)->count(),
            'recentInvoices' => Invoice::query()->where('client_id', $clientId)->latest()->take(5)->get(),
        ]);
    }

    public function invoices(): View
    {
        return view('portal.invoices', ['invoices' => Invoice::query()->where('client_id', Auth::guard('portal')->user()->client_id)->latest()->paginate(15)]);
    }

    public function proposals(): View
    {
        return view('portal.proposals', ['proposals' => Proposal::query()->where('client_id', Auth::guard('portal')->user()->client_id)->latest()->paginate(15)]);
    }

    public function estimates(): View
    {
        return view('portal.estimates', ['estimates' => Estimate::query()->where('client_id', Auth::guard('portal')->user()->client_id)->latest()->paginate(15)]);
    }

    public function projects(): View
    {
        return view('portal.projects', ['projects' => Project::query()->with('tasks')->where('client_id', Auth::guard('portal')->user()->client_id)->latest()->paginate(15)]);
    }

    public function tickets(): View
    {
        $contact = Auth::guard('portal')->user();

        return view('portal.tickets', ['tickets' => Ticket::query()->with(['department', 'replies'])->where('client_id', $contact->client_id)->latest()->paginate(15), 'departments' => TicketDepartment::query()->orderBy('name')->get()]);
    }

    public function storeTicket(Request $request): RedirectResponse
    {
        $contact = Auth::guard('portal')->user();
        $data = $request->validate(['subject' => ['required', 'string', 'max:255'], 'department_id' => ['nullable', 'integer', 'exists:ticket_departments,id'], 'priority' => ['required', 'in:low,medium,high,urgent'], 'message' => ['required', 'string', 'max:10000']]);
        $ticket = Ticket::create([
            'number' => 'TKT-'.now()->format('YmdHis').'-'.str_pad((string) random_int(1, 999), 3, '0', STR_PAD_LEFT),
            'subject' => $data['subject'],
            'client_id' => $contact->client_id,
            'contact_id' => $contact->id,
            'department_id' => $data['department_id'] ?? null,
            'priority' => $data['priority'],
            'status' => 'open',
            'source' => 'portal',
        ]);
        $ticket->replies()->create(['user_type' => 'contact', 'user_id' => $contact->id, 'message' => $data['message']]);

        return to_route('portal.tickets')->with('status', 'Ticket berhasil dikirim.');
    }

    public function knowledgeBase(): View
    {
        return view('portal.knowledge-base', ['articles' => KbArticle::query()->with('category')->where('is_published', true)->latest()->paginate(15)]);
    }

    public function knowledgeBaseShow(KbArticle $article): View
    {
        abort_unless($article->is_published, 404);
        $article->increment('views_count');

        return view('portal.knowledge-base-show', compact('article'));
    }
}
