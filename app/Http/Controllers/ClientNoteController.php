<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientNoteRequest;
use App\Models\Client;
use App\Models\Note;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClientNoteController extends Controller
{
    public function store(StoreClientNoteRequest $request, Client $client): RedirectResponse
    {
        $client->notes()->create(['content' => $request->validated('content'), 'created_by' => $request->user()->id]);
        ActivityLogger::record($request->user(), $client, 'client_note.created', 'Catatan internal ditambahkan.');

        return to_route('clients.show', $client)->with('status', 'Catatan internal ditambahkan.');
    }

    public function destroy(Request $request, Client $client, Note $note): RedirectResponse
    {
        $this->authorize('update', $client);
        abort_unless($note->related_type === Client::class && $note->related_id === $client->id, 404);
        $note->delete();
        ActivityLogger::record($request->user(), $client, 'client_note.archived', 'Catatan internal diarsipkan.');

        return to_route('clients.show', $client)->with('status', 'Catatan internal diarsipkan.');
    }
}
