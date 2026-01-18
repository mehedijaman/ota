<?php

namespace Modules\Agent\Services;

use Illuminate\Support\Facades\DB;
use Modules\Agent\Models\Agent;
use Modules\Agent\Models\AgentWalletTransaction;

class AgentWalletService
{
    /**
     * Deposit amount to agent wallet
     */
    public function deposit(Agent $agent, float $amount, string $method, array $details = [], ?string $reference = null): AgentWalletTransaction
    {
        return DB::transaction(function () use ($agent, $amount, $method, $details, $reference) {
            $agent->lockForUpdate()->find($agent->id); // Lock row for atomic update

            $currentBalance = $agent->balance;
            $balanceAfter = $currentBalance + $amount;

            $agent->update(['balance' => $balanceAfter]);

            return $agent->transactions()->create([
                'type' => 'deposit',
                'amount' => $amount,
                'balance_after' => $balanceAfter,
                'payment_method' => $method,
                'details' => $details,
                'reference' => $reference,
                'status' => 'completed',
                'created_by' => auth()->id(),
            ]);
        });
    }

    /**
     * Withdraw/Deduct amount from agent wallet (e.g. for booking payment)
     */
    public function deduct(Agent $agent, float $amount, string $reason, array $details = [], ?string $reference = null): AgentWalletTransaction
    {
        return DB::transaction(function () use ($agent, $amount, $reason, $details, $reference) {
            $agent->lockForUpdate()->find($agent->id);

            if (!$agent->hasSufficientBalance($amount)) {
                throw new \Exception('Insufficient balance.');
            }

            $currentBalance = $agent->balance;
            $balanceAfter = $currentBalance - $amount;

            $agent->update(['balance' => $balanceAfter]);

            return $agent->transactions()->create([
                'type' => 'payment', // or 'withdraw' based on context, using payment for bookings
                'amount' => $amount, // Log as positive amount, type implies deduction? Or negative? usually absolute value with type.
                'balance_after' => $balanceAfter,
                'payment_method' => 'wallet',
                'details' => array_merge($details, ['reason' => $reason]),
                'reference' => $reference,
                'status' => 'completed',
                'created_by' => auth()->id(),
            ]);
        });
    }
    /**
     * Update a transaction and adjust agent balance if amount changed.
     */
    public function updateTransaction(Agent $agent, AgentWalletTransaction $transaction, array $data): AgentWalletTransaction
    {
        return DB::transaction(function () use ($agent, $transaction, $data) {
            $agent->lockForUpdate()->find($agent->id);

            // Calculate amount difference
            $oldAmount = $transaction->amount;
            $newAmount = isset($data['amount']) ? (float) $data['amount'] : $oldAmount;
            $diff = $newAmount - $oldAmount;

            if (abs($diff) > 0.00) {
                // Adjust agent balance
                $currentBalance = $agent->balance;
                $newBalance = $currentBalance + $diff; // amount is positive for deposits
                $agent->update(['balance' => $newBalance]);

                // Update transaction balance_after specifically for this row (optional, but good for record)
                // Note: This does NOT fix subsequent rows.
                $transaction->balance_after = $transaction->balance_after + $diff;
            }

            $transaction->update([
                'amount' => $newAmount,
                'reference' => $data['reference'] ?? $transaction->reference,
                'details' => isset($data['details']) ? $data['details'] : $transaction->details,
            ]);

            return $transaction;
        });
    }

    /**
     * Delete a transaction and reverse its effect on agent balance.
     */
    public function deleteTransaction(Agent $agent, AgentWalletTransaction $transaction): void
    {
        DB::transaction(function () use ($agent, $transaction) {
            $agent->lockForUpdate()->find($agent->id);

            // Reverse balance
            if ($transaction->type === 'deposit') {
                $agent->decrement('balance', $transaction->amount);
            } elseif ($transaction->type === 'payment' || $transaction->type === 'withdraw') {
                $agent->increment('balance', $transaction->amount);
            }

            $transaction->delete();
        });
    }
}
