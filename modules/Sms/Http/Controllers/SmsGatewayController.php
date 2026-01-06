<?php

namespace Modules\Sms\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Response;
use Modules\Sms\Http\Requests\SmsGatewayRequest;
use Modules\Sms\Http\Requests\SmsGatewayTestRequest;
use Modules\Sms\Models\SmsGateway;
use Modules\Sms\Services\SmsGatewayService;
use Modules\Support\Http\Controllers\BackendController;

class SmsGatewayController extends BackendController
{
    public function index(): Response
    {
        $gateways = SmsGateway::query()
            ->orderByDesc('is_default')
            ->orderByDesc('is_active')
            ->latest('id')
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn (SmsGateway $g) => [
                'id' => $g->id,
                'name' => $g->name,
                'provider_class' => $g->provider_class,
                'provider_name' => class_basename($g->provider_class),
                'is_default' => (bool) $g->is_default,
                'is_active' => (bool) $g->is_active,
                'created_at' => $g->created_at?->format('M d, Y'),
            ]);

        return inertia('Sms/Gateway/SmsGatewayIndex', [
            'gateways' => $gateways,
        ]);
    }

    public function create(): Response
    {
        return inertia('Sms/Gateway/SmsGatewayForm', [
            'providerOptions' => $this->getProviderOptions(),
            'providerFields' => $this->getProviderFields(),
        ]);
    }

    public function store(SmsGatewayRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            if (! empty($validated['is_default'])) {
                SmsGateway::query()->update(['is_default' => false]);
            }

            SmsGateway::create([
                ...$validated,
                'created_by' => auth()->id(),
            ]);
        });

        return redirect()->route('sms.gateways.index')
            ->with('success', 'SMS Gateway created successfully.');
    }

    public function edit(SmsGateway $smsGateway): Response
    {
        return inertia('Sms/Gateway/SmsGatewayForm', [
            'gateway' => [
                'id' => $smsGateway->id,
                'name' => $smsGateway->name,
                'provider_class' => $smsGateway->provider_class,
                'credentials' => $smsGateway->credentials ?? [],
                'is_default' => (bool) $smsGateway->is_default,
                'is_active' => (bool) $smsGateway->is_active,
            ],
            'providerOptions' => $this->getProviderOptions(),
            'providerFields' => $this->getProviderFields(),
        ]);
    }

    public function update(SmsGatewayRequest $request, SmsGateway $smsGateway): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $smsGateway) {
            if (! empty($validated['is_default'])) {
                SmsGateway::query()
                    ->where('id', '!=', $smsGateway->id)
                    ->update(['is_default' => false]);
            }

            $smsGateway->update([
                ...$validated,
                'updated_by' => auth()->id(),
            ]);
        });

        return redirect()->route('sms.gateways.index')
            ->with('success', 'SMS Gateway updated successfully.');
    }

    public function destroy(SmsGateway $smsGateway): RedirectResponse
    {
        if ($smsGateway->is_default) {
            return redirect()->route('sms.gateways.index')
                ->with('error', 'Cannot delete the default gateway. Set another as default first.');
        }

        $smsGateway->delete();

        return redirect()->route('sms.gateways.index')
            ->with('success', 'SMS Gateway deleted successfully.');
    }

    public function setDefault(SmsGateway $smsGateway): RedirectResponse
    {
        DB::transaction(function () use ($smsGateway) {
            SmsGateway::query()->update(['is_default' => false]);
            $smsGateway->update(['is_default' => true, 'is_active' => true]);
        });

        return redirect()->route('sms.gateways.index')
            ->with('success', 'Default gateway updated.');
    }

    public function test(SmsGatewayTestRequest $request, SmsGateway $smsGateway, SmsGatewayService $gatewayService): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $gatewayService->sendWithGateway(
                $smsGateway,
                $validated['test_phone'],
                $validated['test_message'] ?? 'Test SMS from OTA System.'
            );
        } catch (\Throwable $e) {
            $msg = trim((string) $e->getMessage());

            return back()->with('error', $msg !== '' ? 'Test failed: '.$msg : 'Test SMS failed. Check credentials.');
        }

        return back()->with('success', 'Test SMS sent successfully.');
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function getProviderOptions(): array
    {
        $providersConfig = config('sms.providers', []);

        return collect(array_keys($providersConfig))
            ->map(fn (string $providerClass) => [
                'value' => $providerClass,
                'label' => class_basename($providerClass),
            ])
            ->values()
            ->toArray();
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function getProviderFields(): array
    {
        $providersConfig = config('sms.providers', []);

        return collect($providersConfig)
            ->map(fn (array $credentials) => array_keys($credentials))
            ->toArray();
    }
}
