<?php

namespace Modules\Message\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Message\Http\Requests\MessageValidate;
use Modules\Message\Models\Message;
use Modules\Support\Http\Controllers\BackendController;
use Stichoza\GoogleTranslate\GoogleTranslate;

class MessageController extends BackendController
{
    public function index(): Response
    {
        $messages = Message::latest()
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 20))
            ->withQueryString()
            ->through(fn($message) => [
                'id' => $message->id,
                'name' => $message->name,
                'phone' => $message->phone,
                'email' => $message->email,
                'subject' => $message->subject,
                'message' => $message->message,
                'read_at' => $message->read_at,
                'created_at' => $message->created_at->format('d/m/Y H:i') . 'h',
            ]);

        return inertia('Message/MessageIndex', [
            'messages' => $messages,
        ]);
    }

    public function create(): Response
    {
        return inertia('Message/MessageForm');
    }

    public function store(MessageValidate $request): RedirectResponse
    {
        Message::create($request->validated());

        return redirect()->route('message.index')
            ->with('success', 'message created.');
    }

    public function show(int $id): Response
    {
        $message = Message::find($id);
        $message->update(['read_at' => now()]);

        // Translations
        $message->bn = GoogleTranslate::trans($message->message, 'bn');
        $message->en = GoogleTranslate::trans($message->message, 'en');

        return inertia('Message/MessageShow', [
            'message' => $message,
        ]);
    }

    public function update(MessageValidate $request, int $id): RedirectResponse
    {
        $message = Message::findOrFail($id);

        $message->update($request->validated());

        return redirect()->route('message.index')
            ->with('success', 'Message updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Message::findOrFail($id)->delete();

        return redirect()->route('message.index')
            ->with('success', 'Message Deleted.');
    }

    public function recycleBin(): Response
    {
        $messages = Message::onlyTrashed()
            ->orderBy('id')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn($message) => [
                'id' => $message->id,
                'name' => $message->name,
                'deleted_at' => $message->deleted_at ? Carbon::parse($message->deleted_at)->format('d/m/Y') : null,
                'deletedBy' => $message->deletedBy,
                'active' => $message->active,
            ]);

        return inertia('Message/MessageRecycleBin', [
            'messages' => $messages,
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        Message::onlyTrashed()->findOrFail($id)->restore(); // Restore soft deleted record

        return redirect()->route('message.recycleBin.index')
            ->with('success', 'Message Restored.');
    }

    public function destroyForce(int $id): RedirectResponse
    {

        $message = Message::onlyTrashed()->findOrFail($id);

        $message->forceDelete();

        return redirect()->route('message.recycleBin.index')->with('success', 'message deleted.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $messages = Message::onlyTrashed()->get();

        foreach ($messages as $message) {
            $message->forceDelete();
        }

        return redirect()->route('message.recycleBin.index')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        Message::onlyTrashed()->restore(); // Restore soft deleted records

        return redirect()->route('message.recycleBin.index')
            ->with('success', 'Message Restored.');
    }
}
