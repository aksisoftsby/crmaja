<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\LeadStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\Ticket;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        abort_unless(request()->user()->can('dashboard.view'), 403);

        return view('dashboard', [
            'leadByStatus' => LeadStatus::query()->withCount('leads')->orderBy('sort_order')->get(),
            'unpaidInvoiceTotal' => Invoice::query()->whereIn('status', ['unpaid', 'partial', 'overdue'])->sum('total'),
            'overdueInvoiceCount' => Invoice::query()->where('status', 'overdue')->count(),
            'activeProjectCount' => Project::query()->whereIn('status', ['not_started', 'in_progress', 'on_hold'])->count(),
            'dueTaskCount' => Task::query()->whereIn('status', ['todo', 'in_progress', 'testing'])->whereDate('due_date', '<=', now()->addWeek())->count(),
            'openTicketCount' => Ticket::query()->whereNotIn('status', ['closed'])->count(),
            'monthlyIncome' => Invoice::query()->where('status', 'paid')->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])->sum('paid_amount'),
        ]);
    }
}
