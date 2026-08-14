<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\LeadStatus;
use App\Models\Project;
use App\Models\Ticket;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        abort_unless(request()->user()->can('reports.view'), 403);

        return view('reports.index', [
            'invoiceByStatus' => Invoice::query()->selectRaw('status, COUNT(*) as count, SUM(total) as total, SUM(paid_amount) as paid')->groupBy('status')->orderBy('status')->get(),
            'topClients' => Client::query()->withSum('invoices', 'total')->orderByDesc('invoices_sum_total')->take(10)->get(),
            'leadByStatus' => LeadStatus::query()->withCount('leads')->orderBy('sort_order')->get(),
            'projectsByStatus' => Project::query()->selectRaw('status, COUNT(*) as count')->groupBy('status')->orderBy('status')->get(),
            'ticketsByStatus' => Ticket::query()->selectRaw('status, COUNT(*) as count')->groupBy('status')->orderBy('status')->get(),
            'totalBilled' => Invoice::query()->sum('total'),
            'totalReceived' => Invoice::query()->sum('paid_amount'),
        ]);
    }
}
