<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Models\Client;
use App\Models\Contact;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    /**
     * Store a new customer contact.
     */
    public function store(StoreContactRequest $request, Client $client): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($client, $data): void {
            $isFirstContact = ! $client->contacts()->exists();

            if ($data['is_primary'] || $isFirstContact) {
                $client->contacts()->update(['is_primary' => false]);
                $data['is_primary'] = true;
            }

            $client->contacts()->create($data);
        });

        ActivityLogger::record($request->user(), $client, 'contact.created', 'Kontak pelanggan ditambahkan.');

        return to_route('clients.show', $client)
            ->with('status', 'Kontak pelanggan berhasil ditambahkan.');
    }

    /**
     * Update the specified customer contact.
     */
    public function update(UpdateContactRequest $request, Client $client, Contact $contact): RedirectResponse
    {
        abort_unless($contact->client_id === $client->id, 404);

        $data = $request->validated();

        DB::transaction(function () use ($client, $contact, $data): void {
            if ($data['is_primary']) {
                $client->contacts()->whereKeyNot($contact->id)->update(['is_primary' => false]);
            } elseif ($contact->is_primary && ! $client->contacts()->whereKeyNot($contact->id)->where('is_primary', true)->exists()) {
                $data['is_primary'] = true;
            }

            $contact->update($data);
        });

        ActivityLogger::record($request->user(), $client, 'contact.updated', 'Kontak pelanggan diperbarui.');

        return to_route('clients.show', $client)
            ->with('status', 'Kontak pelanggan berhasil diperbarui.');
    }

    /**
     * Archive the specified customer contact.
     */
    public function destroy(Client $client, Contact $contact): RedirectResponse
    {
        abort_unless($contact->client_id === $client->id, 404);
        $this->authorize('update', $client);

        DB::transaction(function () use ($client, $contact): void {
            $wasPrimary = $contact->is_primary;
            $contact->delete();

            if ($wasPrimary) {
                $client->contacts()->orderBy('id')->first()?->update(['is_primary' => true]);
            }
        });

        ActivityLogger::record($request->user(), $client, 'contact.archived', 'Kontak pelanggan diarsipkan.');

        return to_route('clients.show', $client)
            ->with('status', 'Kontak pelanggan telah diarsipkan.');
    }
}
