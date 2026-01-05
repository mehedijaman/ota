<?php

namespace Modules\Sms\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Modules\Sms\Models\SmsPackage;
use Modules\Support\Http\Controllers\BackendController;

class SuperadminSmsPackageController extends BackendController
{
    public function index(): Response
    {
        $packages = SmsPackage::query()
            ->latest('id')
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'sms_credits' => (int) $p->sms_credits,
                'duration_days' => (int) $p->duration_days,
                'price' => $p->price,
                'currency' => $p->currency,
                'is_active' => (bool) $p->is_active,
                'created_at' => $p->created_at,
            ]);

        return inertia('Sms/Superadmin/PackagesIndex', [
            'packages' => $packages,
        ]);
    }

    public function create(): Response
    {
        return inertia('Sms/Superadmin/PackageForm');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'sms_credits' => 'required|integer|min:1|max:1000000',
            'duration_days' => 'required|integer|min:1|max:3650',
            'price' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'is_active' => 'nullable|boolean',
        ]);

        SmsPackage::create([
            ...$validated,
            'currency' => $validated['currency'] ?? 'BDT',
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        return redirect()->route('superadmin.sms.packages.index')->with('success', 'Package created.');
    }

    public function edit(SmsPackage $smsPackage): Response
    {
        return inertia('Sms/Superadmin/PackageForm', [
            'package' => [
                'id' => $smsPackage->id,
                'name' => $smsPackage->name,
                'description' => $smsPackage->description,
                'sms_credits' => (int) $smsPackage->sms_credits,
                'duration_days' => (int) $smsPackage->duration_days,
                'price' => $smsPackage->price,
                'currency' => $smsPackage->currency,
                'is_active' => (bool) $smsPackage->is_active,
            ],
        ]);
    }

    public function update(Request $request, SmsPackage $smsPackage): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'sms_credits' => 'required|integer|min:1|max:1000000',
            'duration_days' => 'required|integer|min:1|max:3650',
            'price' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'is_active' => 'nullable|boolean',
        ]);

        $smsPackage->update([
            ...$validated,
            'currency' => $validated['currency'] ?? 'BDT',
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return redirect()->route('superadmin.sms.packages.index')->with('success', 'Package updated.');
    }

    public function destroy(SmsPackage $smsPackage): RedirectResponse
    {
        $smsPackage->delete();

        return redirect()->route('superadmin.sms.packages.index')->with('success', 'Package deleted.');
    }
}
