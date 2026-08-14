<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Lead::class);

        $leads = Lead::query()
            ->with(['source', 'status', 'assignedStaff'])
            ->when(! $request->user()->can('leads.view_all'), fn ($query) => $query->where('assigned_to', $request->user()->id))
            ->when($request->string('search')->trim()->value(), function ($query, string $search): void {
                $query->where(function ($leadQuery) use ($search): void {
                    $leadQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status_id'), fn ($query) => $query->where('status_id', $request->integer('status_id')))
            ->when($request->filled('assigned_to'), fn ($query) => $query->where('assigned_to', $request->integer('assigned_to')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('leads.index', [
            'leads' => $leads,
            'statuses' => LeadStatus::query()->orderBy('sort_order')->get(),
            'staff' => User::query()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Lead::class);

        return view('leads.create', $this->formOptions());
    }

    public function store(StoreLeadRequest $request): RedirectResponse
    {
        $lead = new Lead($request->validated());
        $lead->created_by = $request->user()->id;
        $lead->save();

        return to_route('leads.show', $lead)->with('status', 'Lead berhasil ditambahkan.');
    }

    public function show(Lead $lead): View
    {
        $this->authorize('view', $lead);

        $lead->load(['source', 'status', 'assignedStaff', 'creator', 'convertedClient']);

        return view('leads.show', compact('lead'));
    }

    public function edit(Lead $lead): View
    {
        $this->authorize('update', $lead);

        return view('leads.edit', array_merge(['lead' => $lead], $this->formOptions()));
    }

    public function update(UpdateLeadRequest $request, Lead $lead): RedirectResponse
    {
        $lead->update($request->validated());

        return to_route('leads.show', $lead)->with('status', 'Lead berhasil diperbarui.');
    }

    public function destroy(Lead $lead): RedirectResponse
    {
        $this->authorize('delete', $lead);
        $lead->delete();

        return to_route('leads.index')->with('status', 'Lead telah diarsipkan.');
    }

    /**
     * @return array{sources: Collection<int, LeadSource>, statuses: Collection<int, LeadStatus>, staff: Collection<int, User>}
     */
    private function formOptions(): array
    {
        return [
            'sources' => LeadSource::query()->orderBy('name')->get(),
            'statuses' => LeadStatus::query()->orderBy('sort_order')->get(),
            'staff' => User::query()->orderBy('name')->get(),
        ];
    }
}
