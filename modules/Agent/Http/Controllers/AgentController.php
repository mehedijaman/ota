<?php

namespace Modules\Agent\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Modules\Agent\Http\Requests\AgentValidate;
use Modules\Agent\Models\Agent;
use Modules\Agent\Services\AgentService;
use Modules\Support\Http\Controllers\BackendController;

class AgentController extends BackendController
{
    public function __construct(
        protected AgentService $agentService
    ) {
        parent::__construct();
    }

    public function index(): Response
    {
        return inertia('Agent/AgentIndex', [
            'agents' => $this->agentService->getAll(),
        ]);
    }

    public function create(): Response
    {
        return inertia('Agent/AgentForm');
    }

    public function store(AgentValidate $request): RedirectResponse
    {
        try {
            $this->agentService->create($request->validated());

            return redirect()->route('agent.index')
                ->with('success', 'Agent created.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function show(Agent $agent): Response
    {
        return inertia('Agent/AgentShow', [
            'agent' => $agent->append('documents'),
        ]);
    }

    public function edit(Agent $agent): Response
    {
        return inertia('Agent/AgentForm', [
            'agent' => $agent->append('documents'),
        ]);
    }

    public function update(AgentValidate $request, Agent $agent): RedirectResponse
    {
        try {
            $this->agentService->update($agent, $request->validated());

            return redirect()->route('agent.index')
                ->with('success', 'Agent updated.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy(Agent $agent): RedirectResponse
    {
        try {
            $this->agentService->delete($agent);

            return redirect()->route('agent.index')
                ->with('success', 'Agent deleted.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function recycleBin(): Response
    {
        return inertia('Agent/AgentRecycleBin', [
            'agents' => $this->agentService->getRecycleBin(),
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        $this->agentService->restore($id);

        return redirect()->route('agent.recycleBin')
            ->with('success', 'Agent restored.');
    }

    public function destroyForce(int $id): RedirectResponse
    {
        $this->agentService->forceDelete($id);

        return redirect()->route('agent.recycleBin')
            ->with('success', 'Agent deleted permanently.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $this->agentService->emptyRecycleBin();

        return redirect()->route('agent.recycleBin')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        $this->agentService->restoreAll();

        return redirect()->route('agent.recycleBin')
            ->with('success', 'All agents restored.');
    }

    public function createUser(Request $request, Agent $agent): RedirectResponse
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $existingUser = \Modules\User\Models\User::where('profile_type', 'agent')
            ->where('profile_id', $agent->id)
            ->first();

        if ($existingUser) {
            return back()->with('error', 'User already exists for this agent.');
        }

        \Modules\User\Models\User::create([
            'name' => $agent->contact_person ?? $agent->company_name,
            'email' => $agent->email,
            'password' => bcrypt($request->password),
            'profile_type' => 'agent',
            'profile_id' => $agent->id,
        ]);

        return back()->with('success', 'User account created successfully.');
    }
}
