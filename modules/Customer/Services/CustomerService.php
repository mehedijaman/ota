<?php

namespace Modules\Customer\Services;

use Illuminate\Support\Facades\DB;
use Modules\Customer\Models\Customer;

class CustomerService
{
    public function getAll()
    {
        return Customer::query()
            ->search(request('searchContext'), request('searchTerm'))
            ->orderBy('first_name', 'asc')
            ->paginate(request('rowsPerPage', 20))
            ->withQueryString()
            ->through(fn ($customer) => [
                'id' => $customer->id,
                'uuid' => $customer->uuid,
                'first_name' => $customer->first_name,
                'last_name' => $customer->last_name,
                'full_name' => $customer->full_name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'city' => $customer->city,
                'country' => $customer->country,
                'status' => $customer->status,
                'avatar_url' => $customer->avatar_url,
                'has_user' => $customer->has_user,
                'updated_at' => $customer->updated_at->format('d/m/Y H:i').'h',
                'created_at' => $customer->created_at->format('d/m/Y H:i').'h',
            ]);
    }

    public function getRecycleBin()
    {
        return Customer::onlyTrashed()
            ->orderBy('id')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($customer) => [
                'id' => $customer->id,
                'uuid' => $customer->uuid,
                'first_name' => $customer->first_name,
                'last_name' => $customer->last_name,
                'full_name' => $customer->full_name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'status' => $customer->status,
            ]);
    }

    public function create(array $data): Customer
    {
        return DB::transaction(function () use ($data) {
            $customer = Customer::create($data);

            $this->handleMediaUploads($customer, $data);

            return $customer;
        });
    }

    public function update(Customer $customer, array $data): Customer
    {
        return DB::transaction(function () use ($customer, $data) {
            $customer->update($data);

            $this->handleMediaUpdates($customer, $data);

            return $customer;
        });
    }

    protected function handleMediaUploads(Customer $customer, array $data): void
    {
        if (! empty($data['avatar'])) {
            $customer->addMedia($data['avatar'])->toMediaCollection('avatar');
        }

        $documentTypes = ['nid_front', 'nid_back', 'passport'];

        foreach ($documentTypes as $type) {
            if (! empty($data[$type])) {
                $customer->addMedia($data[$type])->toMediaCollection($type);
            }
        }
    }

    protected function handleMediaUpdates(Customer $customer, array $data): void
    {
        if (! empty($data['avatar'])) {
            $customer->clearMediaCollection('avatar');
            $customer->addMedia($data['avatar'])->toMediaCollection('avatar');
        } elseif (! empty($data['remove_avatar'])) {
            $customer->clearMediaCollection('avatar');
        }

        $documentTypes = ['nid_front', 'nid_back', 'passport'];

        foreach ($documentTypes as $type) {
            if (! empty($data[$type])) {
                $customer->clearMediaCollection($type);
                $customer->addMedia($data[$type])->toMediaCollection($type);
            } elseif (! empty($data["remove_{$type}"])) {
                $customer->clearMediaCollection($type);
            }
        }
    }

    public function delete(Customer $customer): void
    {
        DB::transaction(function () use ($customer) {
            $customer->delete();
            $this->softDeleteRelatedUser($customer);
        });
    }

    public function restore(int $id): void
    {
        DB::transaction(function () use ($id) {
            $customer = Customer::onlyTrashed()->findOrFail($id);
            $customer->restore();
            $this->restoreRelatedUser($customer);
        });
    }

    public function forceDelete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $customer = Customer::onlyTrashed()->findOrFail($id);
            $this->forceDeleteRelatedUser($customer);
            $customer->clearMediaCollection();
            $customer->forceDelete();
        });
    }

    public function emptyRecycleBin(): void
    {
        DB::transaction(function () {
            $customers = Customer::onlyTrashed()->get();
            foreach ($customers as $customer) {
                $this->forceDeleteRelatedUser($customer);
                $customer->clearMediaCollection();
                $customer->forceDelete();
            }
        });
    }

    public function restoreAll(): void
    {
        DB::transaction(function () {
            $customers = Customer::onlyTrashed()->get();
            foreach ($customers as $customer) {
                $customer->restore();
                $this->restoreRelatedUser($customer);
            }
        });
    }

    protected function softDeleteRelatedUser(Customer $customer): void
    {
        $user = \Modules\User\Models\User::where('profile_type', 'customer')
            ->where('profile_id', $customer->id)
            ->first();

        $user?->delete();
    }

    protected function restoreRelatedUser(Customer $customer): void
    {
        $user = \Modules\User\Models\User::onlyTrashed()
            ->where('profile_type', 'customer')
            ->where('profile_id', $customer->id)
            ->first();

        $user?->restore();
    }

    protected function forceDeleteRelatedUser(Customer $customer): void
    {
        $user = \Modules\User\Models\User::withTrashed()
            ->where('profile_type', 'customer')
            ->where('profile_id', $customer->id)
            ->first();

        $user?->forceDelete();
    }
}
