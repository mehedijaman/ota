<?php

namespace Modules\Flight\Models;

use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Agent\Models\Agent;
use Modules\Customer\Models\Customer;
use Modules\Flight\Database\Factories\FlightBookingFactory;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\Searchable;
use Nnjeim\World\Models\Country;

class FlightBooking extends BaseModel
{
    use HasFactory, HasUuid, Searchable, SoftDeletes;

    protected $table = 'flight_bookings';

    protected function casts(): array
    {
        return [
            'departure_date' => 'date',
            'return_date' => 'date',
            'total_segments' => 'integer',
            'adult_count' => 'integer',
            'child_count' => 'integer',
            'infant_count' => 'integer',
            'total_passengers' => 'integer',
            'base_fare_total' => 'decimal:2',
            'tax_total' => 'decimal:2',
            'fee_total' => 'decimal:2',
            'ancillary_total' => 'decimal:2',
            'insurance_total' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'promo_discount' => 'decimal:2',
            'service_fee' => 'decimal:2',
            'convenience_fee' => 'decimal:2',
            'markup_amount' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'amount_due' => 'decimal:2',
            'net_cost' => 'decimal:2',
            'commission_earned' => 'decimal:2',
            'profit_amount' => 'decimal:2',
            'payment_due_date' => 'date',
            'payment_deadline' => 'datetime',
            'confirmed_at' => 'datetime',
            'ticketed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'void_at' => 'datetime',
            'refund_requested_at' => 'datetime',
            'refunded_at' => 'datetime',
            'payment_received_at' => 'datetime',
            'schedule_change_at' => 'datetime',
            'hold_expires_at' => 'datetime',
            'fare_expires_at' => 'datetime',
            'assigned_at' => 'datetime',
        ];
    }

    protected static function newFactory(): FlightBookingFactory
    {
        return FlightBookingFactory::new();
    }

    // Relationships
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(FlightSupplier::class, 'supplier_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    public function contactCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'contact_country_id');
    }

    public function segments(): HasMany
    {
        return $this->hasMany(FlightBookingSegment::class, 'booking_id')->orderBy('segment_order');
    }

    public function passengers(): HasMany
    {
        return $this->hasMany(FlightBookingPassenger::class, 'booking_id')->orderBy('passenger_number');
    }

    public function leadPassenger(): HasOne
    {
        return $this->hasOne(FlightBookingPassenger::class, 'booking_id')->where('is_lead_passenger', true);
    }

    public function adultPassengers(): HasMany
    {
        return $this->passengers()->where('passenger_type', 'adult');
    }

    public function childPassengers(): HasMany
    {
        return $this->passengers()->where('passenger_type', 'child');
    }

    public function infantPassengers(): HasMany
    {
        return $this->passengers()->where('passenger_type', 'infant');
    }

    public function seats(): HasMany
    {
        return $this->hasMany(FlightBookingSeat::class, 'booking_id');
    }

    public function ancillaries(): HasMany
    {
        return $this->hasMany(FlightBookingAncillary::class, 'booking_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(FlightBookingPayment::class, 'booking_id')->orderByDesc('created_at');
    }

    public function successfulPayments(): HasMany
    {
        return $this->payments()->where('status', 'completed');
    }

    public function cancellation(): HasOne
    {
        return $this->hasOne(FlightBookingCancellation::class, 'booking_id')->latest();
    }

    public function cancellations(): HasMany
    {
        return $this->hasMany(FlightBookingCancellation::class, 'booking_id');
    }

    public function exchanges(): HasMany
    {
        return $this->hasMany(FlightBookingExchange::class, 'booking_id');
    }

    public function latestExchange(): HasOne
    {
        return $this->hasOne(FlightBookingExchange::class, 'booking_id')->latest();
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(FlightBookingStatusHistory::class, 'booking_id')->orderByDesc('created_at');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function ticketedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ticketed_by');
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Accessors
    public function getContactFullNameAttribute(): string
    {
        return trim("{$this->contact_first_name} {$this->contact_last_name}");
    }

    public function getContactPhoneFullAttribute(): string
    {
        return "{$this->contact_phone_country_code}{$this->contact_phone}";
    }

    public function getTripTypeDisplayAttribute(): string
    {
        return match ($this->trip_type) {
            'one_way' => 'One Way',
            'round_trip' => 'Round Trip',
            'multi_city' => 'Multi-City',
            'open_jaw' => 'Open Jaw',
            default => ucfirst(str_replace('_', ' ', $this->trip_type)),
        };
    }

    public function getStatusDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending', 'awaiting_payment' => 'warning',
            'processing', 'ticketing' => 'info',
            'confirmed', 'completed' => 'success',
            'cancelled', 'void', 'refunded', 'no_show' => 'danger',
            'schedule_changed' => 'secondary',
            default => 'primary',
        };
    }

    public function getPaymentStatusDisplayAttribute(): string
    {
        return ucfirst($this->payment_status);
    }

    public function getPassengerSummaryAttribute(): string
    {
        $parts = [];
        if ($this->adult_count > 0) {
            $parts[] = "{$this->adult_count} Adult".($this->adult_count > 1 ? 's' : '');
        }
        if ($this->child_count > 0) {
            $parts[] = "{$this->child_count} Child".($this->child_count > 1 ? 'ren' : '');
        }
        if ($this->infant_count > 0) {
            $parts[] = "{$this->infant_count} Infant".($this->infant_count > 1 ? 's' : '');
        }

        return implode(', ', $parts);
    }

    public function getRouteDisplayAttribute(): string
    {
        $segments = $this->segments;
        if ($segments->isEmpty()) {
            return 'N/A';
        }

        $first = $segments->first();
        $last = $segments->last();

        return ($first->departureAirport?->iata_code ?? '???').' → '.($last->arrivalAirport?->iata_code ?? '???');
    }

    public function getTotalPaidAttribute(): float
    {
        return $this->successfulPayments()->sum('amount');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeTicketed($query)
    {
        return $query->where('ticketing_status', 'ticketed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'unpaid');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeForCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeForAgent($query, int $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    public function scopeDepartingOn($query, $date)
    {
        return $query->whereDate('departure_date', $date);
    }

    public function scopeDepartingBetween($query, $from, $to)
    {
        return $query->whereBetween('departure_date', [$from, $to]);
    }

    public function scopeExpiringHold($query)
    {
        return $query->whereNotNull('hold_expires_at')
            ->where('hold_expires_at', '<=', now())
            ->where('ticketing_status', 'not_ticketed');
    }

    public function scopeAssignedTo($query, int $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeFromSource($query, string $source)
    {
        return $query->where('booking_source', $source);
    }

    // Methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isTicketed(): bool
    {
        return $this->ticketing_status === 'ticketed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isFullyPaid(): bool
    {
        return $this->amount_due <= 0;
    }

    public function canBeTicketed(): bool
    {
        return $this->isPaid() && $this->ticketing_status === 'not_ticketed';
    }

    public function canBeCancelled(): bool
    {
        return ! in_array($this->status, ['cancelled', 'void', 'refunded', 'completed']);
    }

    public function hasHoldExpired(): bool
    {
        return $this->hold_expires_at && $this->hold_expires_at->isPast();
    }

    public function recalculateTotals(): void
    {
        $this->amount_paid = $this->successfulPayments()->sum('amount');
        $this->amount_due = $this->total_amount - $this->amount_paid;

        if ($this->amount_paid >= $this->total_amount) {
            $this->payment_status = 'paid';
        } elseif ($this->amount_paid > 0) {
            $this->payment_status = 'partial';
        } else {
            $this->payment_status = 'unpaid';
        }

        $this->save();
    }

    public function recordStatusChange(string $newStatus, ?string $notes = null, ?int $userId = null): void
    {
        $this->statusHistories()->create([
            'old_status' => $this->status,
            'new_status' => $newStatus,
            'status_type' => 'booking',
            'notes' => $notes,
            'changed_by' => $userId ?? auth()->id(),
            'source' => 'user',
        ]);
    }
}
