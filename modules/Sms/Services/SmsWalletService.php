<?php

namespace Modules\Sms\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Sms\Exceptions\InsufficientSmsBalanceException;
use Modules\Sms\Models\SmsCreditLot;
use Modules\Sms\Models\SmsCreditReservation;
use Modules\Sms\Models\SmsMessage;
use RuntimeException;

class SmsWalletService
{
    public function getAvailableCredits(int $tenantId): int
    {
        return (int) SmsCreditLot::query()
            ->where('tenant_id', $tenantId)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->sum(DB::raw('(credits_total - credits_used - credits_reserved)'));
    }

    public function getReservedCredits(int $tenantId): int
    {
        return (int) SmsCreditLot::query()
            ->where('tenant_id', $tenantId)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->sum('credits_reserved');
    }

    /**
     * @throws RuntimeException
     */
    public function reserveForMessage(int $tenantId, SmsMessage $message, int $credits): void
    {
        if ($credits <= 0) {
            return;
        }

        DB::transaction(function () use ($tenantId, $message, $credits) {
            $available = $this->getAvailableCredits($tenantId);

            if ($available < $credits) {
                throw new InsufficientSmsBalanceException('Insufficient SMS balance.');
            }

            $remaining = $credits;

            $lots = SmsCreditLot::query()
                ->where('tenant_id', $tenantId)
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->orderByRaw('CASE WHEN expires_at IS NULL THEN 1 ELSE 0 END')
                ->orderBy('expires_at')
                ->lockForUpdate()
                ->get();

            foreach ($lots as $lot) {
                $lotAvailable = (int) ($lot->credits_total - $lot->credits_used - $lot->credits_reserved);
                if ($lotAvailable <= 0) {
                    continue;
                }

                $take = min($lotAvailable, $remaining);

                $lot->credits_reserved = (int) $lot->credits_reserved + (int) $take;
                $lot->save();

                SmsCreditReservation::create([
                    'tenant_id' => $tenantId,
                    'sms_credit_lot_id' => $lot->id,
                    'sms_message_id' => $message->id,
                    'credits' => (int) $take,
                ]);

                $remaining -= $take;

                if ($remaining <= 0) {
                    break;
                }
            }

            if ($remaining > 0) {
                Log::warning('SMS reserve credits incomplete', [
                    'tenant_id' => $tenantId,
                    'message_id' => $message->id,
                    'requested' => $credits,
                    'remaining' => $remaining,
                ]);

                throw new RuntimeException('Unable to reserve SMS credits.');
            }
        });
    }

    public function commitReservations(SmsMessage $message): void
    {
        DB::transaction(function () use ($message) {
            $reservations = SmsCreditReservation::query()
                ->where('sms_message_id', $message->id)
                ->lockForUpdate()
                ->get();

            foreach ($reservations as $res) {
                $lot = SmsCreditLot::query()->whereKey($res->sms_credit_lot_id)->lockForUpdate()->first();
                if (! $lot) {
                    continue;
                }

                $lot->credits_reserved = max(0, (int) $lot->credits_reserved - (int) $res->credits);
                $lot->credits_used = (int) $lot->credits_used + (int) $res->credits;
                $lot->save();

                $res->delete();
            }
        });
    }

    public function releaseReservations(SmsMessage $message): void
    {
        DB::transaction(function () use ($message) {
            $reservations = SmsCreditReservation::query()
                ->where('sms_message_id', $message->id)
                ->lockForUpdate()
                ->get();

            foreach ($reservations as $res) {
                $lot = SmsCreditLot::query()->whereKey($res->sms_credit_lot_id)->lockForUpdate()->first();
                if ($lot) {
                    $lot->credits_reserved = max(0, (int) $lot->credits_reserved - (int) $res->credits);
                    $lot->save();
                }

                $res->delete();
            }
        });
    }
}
