<?php

namespace Modules\Agent\Services;

use Illuminate\Support\Facades\DB;
use Modules\Agent\Models\Agent;

class AgentService
{
    public function getAll()
    {
        return Agent::query()
            ->search(request('searchContext'), request('searchTerm'))
            ->orderBy('company_name', 'asc')
            ->paginate(request('rowsPerPage', 20))
            ->withQueryString()
            ->through(fn ($agent) => [
                'id' => $agent->id,
                'uuid' => $agent->uuid,
                'company_name' => $agent->company_name,
                'contact_person' => $agent->contact_person,
                'email' => $agent->email,
                'phone' => $agent->phone,
                'city' => $agent->city,
                'country' => $agent->country,
                'status' => $agent->status,
                'balance' => $agent->balance,
                'credit_limit' => $agent->credit_limit,
                'logo_url' => $agent->logo_url,
                'has_user' => $agent->has_user,
                'updated_at' => $agent->updated_at->format('d/m/Y H:i').'h',
                'created_at' => $agent->created_at->format('d/m/Y H:i').'h',
            ]);
    }

    public function getRecycleBin()
    {
        return Agent::onlyTrashed()
            ->orderBy('id')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($agent) => [
                'id' => $agent->id,
                'uuid' => $agent->uuid,
                'company_name' => $agent->company_name,
                'contact_person' => $agent->contact_person,
                'email' => $agent->email,
                'phone' => $agent->phone,
                'status' => $agent->status,
            ]);
    }

    public function create(array $data): Agent
    {
        return DB::transaction(function () use ($data) {
            $agent = Agent::create($data);

            $this->handleMediaUploads($agent, $data);

            return $agent;
        });
    }

    public function update(Agent $agent, array $data): Agent
    {
        return DB::transaction(function () use ($agent, $data) {
            $agent->update($data);

            $this->handleMediaUpdates($agent, $data);

            return $agent;
        });
    }

    protected function handleMediaUploads(Agent $agent, array $data): void
    {
        if (! empty($data['logo'])) {
            $agent->addMedia($data['logo'])->toMediaCollection('logo');
        }

        $documentTypes = ['trade_license', 'mocat_certificate', 'tin_certificate', 'nid_front', 'nid_back'];

        foreach ($documentTypes as $type) {
            if (! empty($data[$type])) {
                $agent->addMedia($data[$type])->toMediaCollection($type);
            }
        }
    }

    protected function handleMediaUpdates(Agent $agent, array $data): void
    {
        if (! empty($data['logo'])) {
            $agent->clearMediaCollection('logo');
            $agent->addMedia($data['logo'])->toMediaCollection('logo');
        } elseif (! empty($data['remove_logo'])) {
            $agent->clearMediaCollection('logo');
        }

        $documentTypes = ['trade_license', 'mocat_certificate', 'tin_certificate', 'nid_front', 'nid_back'];

        foreach ($documentTypes as $type) {
            if (! empty($data[$type])) {
                $agent->clearMediaCollection($type);
                $agent->addMedia($data[$type])->toMediaCollection($type);
            } elseif (! empty($data["remove_{$type}"])) {
                $agent->clearMediaCollection($type);
            }
        }
    }

    public function delete(Agent $agent): void
    {
        DB::transaction(function () use ($agent) {
            $agent->delete();
            $this->softDeleteRelatedUser($agent);
        });
    }

    public function restore(int $id): void
    {
        DB::transaction(function () use ($id) {
            $agent = Agent::onlyTrashed()->findOrFail($id);
            $agent->restore();
            $this->restoreRelatedUser($agent);
        });
    }

    public function forceDelete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $agent = Agent::onlyTrashed()->findOrFail($id);
            $this->forceDeleteRelatedUser($agent);
            $agent->clearMediaCollection();
            $agent->forceDelete();
        });
    }

    public function emptyRecycleBin(): void
    {
        DB::transaction(function () {
            $agents = Agent::onlyTrashed()->get();
            foreach ($agents as $agent) {
                $this->forceDeleteRelatedUser($agent);
                $agent->clearMediaCollection();
                $agent->forceDelete();
            }
        });
    }

    public function restoreAll(): void
    {
        DB::transaction(function () {
            $agents = Agent::onlyTrashed()->get();
            foreach ($agents as $agent) {
                $agent->restore();
                $this->restoreRelatedUser($agent);
            }
        });
    }

    protected function softDeleteRelatedUser(Agent $agent): void
    {
        $user = \Modules\User\Models\User::where('profile_type', 'agent')
            ->where('profile_id', $agent->id)
            ->first();

        $user?->delete();
    }

    protected function restoreRelatedUser(Agent $agent): void
    {
        $user = \Modules\User\Models\User::onlyTrashed()
            ->where('profile_type', 'agent')
            ->where('profile_id', $agent->id)
            ->first();

        $user?->restore();
    }

    protected function forceDeleteRelatedUser(Agent $agent): void
    {
        $user = \Modules\User\Models\User::withTrashed()
            ->where('profile_type', 'agent')
            ->where('profile_id', $agent->id)
            ->first();

        $user?->forceDelete();
    }
}
