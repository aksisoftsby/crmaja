<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use App\Models\CustomerGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    /**
     * Display a paginated listing of customers.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Client::class);

        $clients = Client::query()
            ->with(['customerGroup', 'assignedStaff', 'primaryContact'])
            ->when(
                ! $request->user()->can('clients.view_all'),
                fn ($query) => $query->where('assigned_staff_id', $request->user()->id),
            )
            ->when($request->string('search')->trim()->value(), function ($query, string $search): void {
                $query->where(function ($customerQuery) use ($search): void {
                    $customerQuery->where('company_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request): void {
                $query->where('is_active', $request->string('status')->value() === 'active');
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('clients.index', compact('clients'));
    }

    /**
     * Show the customer creation form.
     */
    public function create(): View
    {
        $this->authorize('create', Client::class);

        return view('clients.create', $this->formOptions());
    }

    /**
     * Store a newly created customer.
     */
    public function store(StoreClientRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['currency'] = strtoupper($data['currency']);

        $client = new Client($data);
        $client->created_by = $request->user()->id;
        $client->save();

        return to_route('clients.show', $client)
            ->with('status', 'Pelanggan berhasil ditambahkan.');
    }

    /**
     * Display a customer and its contacts.
     */
    public function show(Client $client): View
    {
        $this->authorize('view', $client);

        $client->load([
            'customerGroup',
            'assignedStaff',
            'creator',
            'contacts' => fn ($query) => $query->orderByDesc('is_primary')->orderBy('first_name'),
            'notes.creator',
        ]);

        return view('clients.show', compact('client'));
    }

    /**
     * Show the customer edit form.
     */
    public function edit(Client $client): View
    {
        $this->authorize('update', $client);

        return view('clients.edit', array_merge(['client' => $client], $this->formOptions()));
    }

    /**
     * Update the specified customer.
     */
    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        $data = $request->validated();
        $data['currency'] = strtoupper($data['currency']);

        $client->update($data);

        return to_route('clients.show', $client)
            ->with('status', 'Data pelanggan berhasil diperbarui.');
    }

    /**
     * Soft-delete the specified customer.
     */
    public function destroy(Client $client): RedirectResponse
    {
        $this->authorize('delete', $client);

        $client->delete();

        return to_route('clients.index')
            ->with('status', 'Pelanggan telah diarsipkan.');
    }

    /**
     * Get lookup data shared by customer forms.
     *
     * @return array{groups: Collection<int, CustomerGroup>, staff: Collection<int, User>}
     */
    private function formOptions(): array
    {
        return [
            'groups' => CustomerGroup::query()->orderBy('name')->get(),
            'staff' => User::query()->orderBy('name')->get(),
        ];
    }
}
