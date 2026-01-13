<?php

namespace Modules\Visa\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Visa\Models\Visa;
use Modules\Visa\Models\VisaApplication;

class VisaApplicationService
{
    public function getAll(): LengthAwarePaginator
    {
        return VisaApplication::query()
            ->with(['visa.country', 'visa.category'])
            ->when(request('visa_id'), fn ($q, $visaId) => $q->where('visa_id', $visaId))
            ->when(request('country_id'), fn ($q, $countryId) => $q->where('country_id', $countryId))
            ->when(request('status'), fn ($q, $status) => $q->where('status', $status))
            ->when(request('payment_status'), fn ($q, $status) => $q->where('payment_status', $status))
            ->when(request('date_from'), fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when(request('date_to'), fn ($q, $date) => $q->whereDate('created_at', '<=', $date))
            ->orderBy('id', 'desc')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 15))
            ->withQueryString()
            ->through(fn (VisaApplication $app) => [
                'id' => $app->id,
                'application_number' => $app->application_number,
                'visa_name' => $app->visa?->name,
                'country' => $app->visa?->country?->name,
                'contact_name' => $app->contact_name,
                'contact_email' => $app->contact_email,
                'contact_phone' => $app->contact_phone,
                'total_travelers' => $app->total_travelers,
                'intended_travel_date' => $app->intended_travel_date?->format('M d, Y'),
                'status' => $app->status,
                'payment_status' => $app->payment_status,
                'total_amount' => $app->total_amount,
                'amount_paid' => $app->amount_paid,
                'amount_due' => $app->amount_due,
                'created_at' => $app->created_at->format('M d, Y'),
            ]);
    }

    /**
     * @return array<string, int|float>
     */
    public function getStats(): array
    {
        return [
            'total' => VisaApplication::count(),
            'pending' => VisaApplication::where('status', 'pending')->count(),
            'processing' => VisaApplication::whereIn('status', ['documents_pending', 'documents_submitted', 'under_review', 'processing'])->count(),
            'submitted' => VisaApplication::where('status', 'submitted_to_embassy')->count(),
            'approved' => VisaApplication::where('status', 'approved')->count(),
            'completed' => VisaApplication::where('status', 'completed')->count(),
            'rejected' => VisaApplication::where('status', 'rejected')->count(),
            'cancelled' => VisaApplication::where('status', 'cancelled')->count(),
            'total_revenue' => VisaApplication::sum('amount_paid'),
            'pending_revenue' => VisaApplication::sum('amount_due'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getById(int $id): array
    {
        $app = VisaApplication::with([
            'visa.country',
            'visa.category',
            'travelers' => fn ($q) => $q->orderBy('is_lead', 'desc'),
            'documents',
            'payments' => fn ($q) => $q->latest(),
            'notes' => fn ($q) => $q->latest(),
            'statusHistories' => fn ($q) => $q->with('changedBy')->latest(),
        ])->findOrFail($id);

        return [
            'id' => $app->id,
            'application_number' => $app->application_number,
            'visa_id' => $app->visa_id,
            'country_id' => $app->country_id,
            'visa' => $app->visa,
            'status' => $app->status,
            'payment_status' => $app->payment_status,
            'adult_count' => $app->adult_count,
            'child_count' => $app->child_count,
            'infant_count' => $app->infant_count,
            'total_travelers' => $app->total_travelers,
            'intended_travel_date' => $app->intended_travel_date?->format('Y-m-d'),
            'intended_return_date' => $app->intended_return_date?->format('Y-m-d'),
            'travel_purpose' => $app->travel_purpose,
            'contact_name' => $app->contact_name,
            'contact_email' => $app->contact_email,
            'contact_phone' => $app->contact_phone,
            'whatsapp' => $app->whatsapp,
            'contact_address' => $app->contact_address,
            'city' => $app->city,
            'postal_code' => $app->postal_code,
            'country' => $app->country,
            'emergency_contact_name' => $app->emergency_contact_name,
            'emergency_contact_phone' => $app->emergency_contact_phone,
            'emergency_contact_relationship' => $app->emergency_contact_relationship,
            'subtotal' => $app->subtotal,
            'embassy_fees' => $app->embassy_fees,
            'service_fees' => $app->service_fees,
            'express_fees' => $app->express_fees,
            'additional_fees' => $app->additional_fees,
            'tax_amount' => $app->tax_amount,
            'discount_amount' => $app->discount_amount,
            'total_amount' => $app->total_amount,
            'amount_paid' => $app->amount_paid,
            'amount_due' => $app->amount_due,
            'coupon_code' => $app->coupon_code,
            'internal_notes' => $app->internal_notes,
            'rejection_reason' => $app->rejection_reason,
            'cancellation_reason' => $app->cancellation_reason,
            'application_source' => $app->application_source,
            'submitted_at' => $app->submitted_at?->format('Y-m-d H:i'),
            'approved_at' => $app->approved_at?->format('Y-m-d H:i'),
            'rejected_at' => $app->rejected_at?->format('Y-m-d H:i'),
            'completed_at' => $app->completed_at?->format('Y-m-d H:i'),
            'cancelled_at' => $app->cancelled_at?->format('Y-m-d H:i'),
            'created_at' => $app->created_at->format('M d, Y H:i'),
            'travelers' => $app->travelers,
            'documents' => $app->documents,
            'payments' => $app->payments,
            'notes' => $app->notes,
            'statusHistories' => $app->statusHistories,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getForEdit(int $id): array
    {
        return $this->getById($id);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): VisaApplication
    {
        return DB::transaction(function () use ($data) {
            $visa = Visa::findOrFail($data['visa_id']);

            $data['application_number'] = 'VA-'.strtoupper(Str::random(8));
            $data['country_id'] = $visa->country_id;
            $data['status'] = 'pending';
            $data['payment_status'] = 'unpaid';
            $data['application_source'] = 'admin';

            $this->calculatePricing($data, $visa);

            $app = VisaApplication::create($data);

            if (isset($data['travelers'])) {
                $this->syncTravelers($app, $data['travelers']);
            }

            $this->recordStatusHistory($app, null, 'pending', 'Application created');

            return $app;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(VisaApplication $app, array $data): VisaApplication
    {
        return DB::transaction(function () use ($app, $data) {
            if (isset($data['visa_id']) && $data['visa_id'] !== $app->visa_id) {
                $visa = Visa::findOrFail($data['visa_id']);
                $data['country_id'] = $visa->country_id;
                $this->calculatePricing($data, $visa);
            }

            $oldStatus = $app->status;
            $app->update($data);

            if (isset($data['travelers'])) {
                $this->syncTravelers($app, $data['travelers']);
            }

            if (isset($data['status']) && $oldStatus !== $data['status']) {
                $this->recordStatusHistory($app, $oldStatus, $data['status'], $data['status_note'] ?? null);
            }

            return $app;
        });
    }

    public function updateStatus(VisaApplication $app, string $status, ?string $note = null): VisaApplication
    {
        return DB::transaction(function () use ($app, $status, $note) {
            $oldStatus = $app->status;
            $updateData = ['status' => $status];

            switch ($status) {
                case 'submitted_to_embassy':
                    $updateData['submitted_at'] = now();
                    break;
                case 'approved':
                    $updateData['approved_at'] = now();
                    break;
                case 'rejected':
                    $updateData['rejected_at'] = now();
                    $updateData['rejection_reason'] = $note;
                    break;
                case 'completed':
                    $updateData['completed_at'] = now();
                    break;
                case 'cancelled':
                    $updateData['cancelled_at'] = now();
                    $updateData['cancellation_reason'] = $note;
                    break;
            }

            $app->update($updateData);
            $this->recordStatusHistory($app, $oldStatus, $status, $note);

            return $app;
        });
    }

    /**
     * @param  array<string, mixed>  $paymentData
     */
    public function addPayment(VisaApplication $app, array $paymentData): VisaApplication
    {
        return DB::transaction(function () use ($app, $paymentData) {
            $paymentData['transaction_id'] = $paymentData['transaction_id'] ?? 'TXN-'.strtoupper(Str::random(12));
            $paymentData['status'] = $paymentData['status'] ?? 'completed';
            $paymentData['paid_at'] = $paymentData['paid_at'] ?? now();

            $app->payments()->create($paymentData);

            $totalPaid = $app->payments()->where('status', 'completed')->sum('amount');
            $app->update([
                'amount_paid' => $totalPaid,
                'amount_due' => $app->total_amount - $totalPaid,
                'payment_status' => $this->determinePaymentStatus($totalPaid, $app->total_amount),
            ]);

            return $app->fresh();
        });
    }

    public function delete(VisaApplication $app): bool
    {
        if (in_array($app->status, ['processing', 'submitted_to_embassy', 'approved', 'completed'])) {
            return false;
        }

        $app->delete();

        return true;
    }

    public function restore(int $id): void
    {
        VisaApplication::withTrashed()->findOrFail($id)->restore();
    }

    public function forceDelete(int $id): void
    {
        $app = VisaApplication::withTrashed()->findOrFail($id);
        $app->travelers()->delete();
        $app->documents()->each(fn ($doc) => $doc->clearMediaCollection('document'));
        $app->documents()->delete();
        $app->payments()->delete();
        $app->notes()->delete();
        $app->statusHistories()->delete();
        $app->forceDelete();
    }

    public function getRecycleBin(): LengthAwarePaginator
    {
        return VisaApplication::onlyTrashed()
            ->with('visa.country')
            ->orderBy('deleted_at', 'desc')
            ->paginate(request('rowsPerPage', 15))
            ->withQueryString()
            ->through(fn (VisaApplication $app) => [
                'id' => $app->id,
                'application_number' => $app->application_number,
                'visa_name' => $app->visa?->name,
                'contact_name' => $app->contact_name,
                'deleted_at' => $app->deleted_at->format('M d, Y'),
            ]);
    }

    public function emptyRecycleBin(): void
    {
        VisaApplication::onlyTrashed()->each(function (VisaApplication $app) {
            $this->forceDelete($app->id);
        });
    }

    public function restoreAll(): void
    {
        VisaApplication::onlyTrashed()->restore();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function calculatePricing(array &$data, Visa $visa): void
    {
        $adultCount = $data['adult_count'] ?? 1;
        $childCount = $data['child_count'] ?? 0;
        $infantCount = $data['infant_count'] ?? 0;
        $totalTravelers = $adultCount + $childCount + $infantCount;

        $subtotal = $visa->base_price * $totalTravelers;
        $embassyFees = $visa->embassy_fee * $totalTravelers;
        $serviceFees = $visa->service_fee;
        $vfsFee = $visa->vfs_fee;

        $data['subtotal'] = $subtotal;
        $data['embassy_fees'] = $embassyFees;
        $data['service_fees'] = $serviceFees;
        $data['express_fees'] = $data['express_fees'] ?? 0;
        $data['additional_fees'] = $data['additional_fees'] ?? 0;
        $data['tax_amount'] = $data['tax_amount'] ?? 0;
        $data['discount_amount'] = $data['discount_amount'] ?? 0;
        $data['total_amount'] = $subtotal + $embassyFees + $serviceFees + $vfsFee +
            $data['express_fees'] + $data['additional_fees'] + $data['tax_amount'] - $data['discount_amount'];
        $data['amount_paid'] = $data['amount_paid'] ?? 0;
        $data['amount_due'] = $data['total_amount'] - $data['amount_paid'];
    }

    /**
     * @param  array<int, array<string, mixed>>  $travelers
     */
    protected function syncTravelers(VisaApplication $app, array $travelers): void
    {
        $app->travelers()->delete();

        foreach ($travelers as $index => $traveler) {
            $traveler['is_lead'] = $index === 0;
            $app->travelers()->create($traveler);
        }
    }

    protected function recordStatusHistory(VisaApplication $app, ?string $from, string $to, ?string $note = null): void
    {
        $app->statusHistories()->create([
            'from_status' => $from,
            'to_status' => $to,
            'notes' => $note,
            'changed_by' => auth()->id(),
            'changed_at' => now(),
        ]);
    }

    protected function determinePaymentStatus(float $paid, float $total): string
    {
        if ($paid <= 0) {
            return 'unpaid';
        }

        if ($paid >= $total) {
            return 'paid';
        }

        return 'partial';
    }
}
