<?php

namespace Modules\Customer\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Modules\Customer\Http\Requests\CustomerValidate;
use Modules\Customer\Models\Customer;
use Modules\Customer\Services\CustomerService;
use Modules\Support\Http\Controllers\BackendController;

class CustomerController extends BackendController
{
    public function __construct(
        protected CustomerService $customerService
    ) {
        parent::__construct();
    }

    public function index(): Response
    {
        return inertia('Customer/CustomerIndex', [
            'customers' => $this->customerService->getAll(),
        ]);
    }

    public function create(): Response
    {
        return inertia('Customer/CustomerForm');
    }

    public function store(CustomerValidate $request): RedirectResponse
    {
        try {
            $this->customerService->create($request->validated());

            return redirect()->route('customer.index')
                ->with('success', 'Customer created.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function show(Customer $customer): Response
    {
        return inertia('Customer/CustomerShow', [
            'customer' => $customer->append('documents'),
        ]);
    }

    public function edit(Customer $customer): Response
    {
        return inertia('Customer/CustomerForm', [
            'customer' => $customer->append('documents'),
        ]);
    }

    public function update(CustomerValidate $request, Customer $customer): RedirectResponse
    {
        try {
            $this->customerService->update($customer, $request->validated());

            return redirect()->route('customer.index')
                ->with('success', 'Customer updated.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        try {
            $this->customerService->delete($customer);

            return redirect()->route('customer.index')
                ->with('success', 'Customer deleted.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function recycleBin(): Response
    {
        return inertia('Customer/CustomerRecycleBin', [
            'customers' => $this->customerService->getRecycleBin(),
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        $this->customerService->restore($id);

        return redirect()->route('customer.recycleBin')
            ->with('success', 'Customer restored.');
    }

    public function destroyForce(int $id): RedirectResponse
    {
        $this->customerService->forceDelete($id);

        return redirect()->route('customer.recycleBin')
            ->with('success', 'Customer deleted permanently.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $this->customerService->emptyRecycleBin();

        return redirect()->route('customer.recycleBin')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        $this->customerService->restoreAll();

        return redirect()->route('customer.recycleBin')
            ->with('success', 'All customers restored.');
    }

    public function createUser(Request $request, Customer $customer): RedirectResponse
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $existingUser = \Modules\User\Models\User::where('profile_type', 'customer')
            ->where('profile_id', $customer->id)
            ->first();

        if ($existingUser) {
            return back()->with('error', 'User already exists for this customer.');
        }

        \Modules\User\Models\User::create([
            'name' => $customer->full_name,
            'email' => $customer->email,
            'password' => bcrypt($request->password),
            'profile_type' => 'customer',
            'profile_id' => $customer->id,
        ]);

        return back()->with('success', 'User account created successfully.');
    }
}
