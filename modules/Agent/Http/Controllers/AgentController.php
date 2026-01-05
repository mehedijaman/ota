<?php

namespace Modules\Agent\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Agent\Http\Requests\AgentValidate;
use Modules\Agent\Models\Agent;
use Modules\Support\Http\Controllers\BackendController;

class AgentController extends BackendController
{
    public function index(): Response
    {
        $agents = Agent::orderBy('name')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($agent) => [
                'id' => $agent->id,
                'name' => $agent->name,
                'created_at' => $agent->created_at->format('d/m/Y H:i').'h',
            ]);

        return inertia('Agent/AgentIndex', [
            'agents' => $agents,
        ]);
    }

    public function create(): Response
    {
        return inertia('Agent/AgentForm');
    }

    public function store(AgentValidate $request): RedirectResponse
    {
        Agent::create($request->validated());

        return redirect()->route('agent.index')
            ->with('success', 'Agent created.');
    }

    public function edit(int $id): Response
    {
        $agent = Agent::find($id);

        return inertia('Agent/AgentForm', [
            'agent' => $agent,
        ]);
    }

    public function update(AgentValidate $request, int $id): RedirectResponse
    {
        $agent = Agent::findOrFail($id);

        $agent->update($request->validated());

        return redirect()->route('agent.index')
            ->with('success', 'Agent updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Agent::findOrFail($id)->delete();

        return redirect()->route('agent.index')
            ->with('success', 'Agent deleted.');
    }
}
