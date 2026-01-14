<?php

namespace Modules\Hotel\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Support\Models\BaseModel;

class HotelCancellationPolicy extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'hotel_cancellation_policies';

    protected function casts(): array
    {
        return [
            'is_refundable' => 'boolean',
            'is_free_cancellation' => 'boolean',
            'rules' => 'array',
            'no_show_penalty_percentage' => 'decimal:2',
            'no_show_penalty_nights' => 'integer',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    public function ratePlans(): HasMany
    {
        return $this->hasMany(HotelRatePlan::class, 'cancellation_policy_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeForHotel($query, int $hotelId)
    {
        return $query->where('hotel_id', $hotelId);
    }

    /**
     * Calculate refund based on hours before check-in.
     */
    public function calculateRefund(float $originalAmount, int $hoursBeforeCheckin): array
    {
        if (! $this->rules || ! is_array($this->rules)) {
            return [
                'refund_percentage' => $this->is_refundable ? 100 : 0,
                'refund_amount' => $this->is_refundable ? $originalAmount : 0,
                'cancellation_fee' => $this->is_refundable ? 0 : $originalAmount,
            ];
        }

        // Sort rules by hours_before_checkin descending
        $rules = collect($this->rules)->sortByDesc('hours_before_checkin');

        foreach ($rules as $rule) {
            if ($hoursBeforeCheckin >= ($rule['hours_before_checkin'] ?? 0)) {
                $refundPercentage = $rule['refund_percentage'] ?? 0;
                $refundAmount = round($originalAmount * ($refundPercentage / 100), 2);
                $cancellationFee = $originalAmount - $refundAmount;

                return [
                    'refund_percentage' => $refundPercentage,
                    'refund_amount' => $refundAmount,
                    'cancellation_fee' => $cancellationFee,
                    'applied_rule' => $rule,
                ];
            }
        }

        // No rule matched - no refund
        return [
            'refund_percentage' => 0,
            'refund_amount' => 0,
            'cancellation_fee' => $originalAmount,
        ];
    }
}
