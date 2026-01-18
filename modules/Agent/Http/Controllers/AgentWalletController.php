<?php

namespace Modules\Agent\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Agent\Models\Agent;
use Modules\Agent\Models\AgentWalletTransaction;
use Modules\Agent\Services\AgentWalletService;

class AgentWalletController extends Controller
{
    public function __construct(protected AgentWalletService $walletService) {}

    public function store(Request $request, Agent $agent)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'details' => 'nullable|string',
            'reference' => 'nullable|string',
        ]);

        try {
            $this->walletService->deposit(
                agent: $agent,
                amount: $validated['amount'],
                method: 'manual',
                details: ['notes' => $validated['details'] ?? 'Manual deposit by admin'],
                reference: $validated['reference']
            );

            return back()->with('success', 'Amount deposited successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Deposit failed: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Agent $agent, AgentWalletTransaction $transaction)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'details' => 'nullable|string',
            'reference' => 'nullable|string',
        ]);

        try {
            if ($transaction->type !== 'deposit') {
                throw new \Exception('Only manual deposits can be edited.');
            }

            $this->walletService->updateTransaction(
                agent: $agent,
                transaction: $transaction,
                data: [
                    'amount' => $validated['amount'],
                    'details' => ['notes' => $validated['details'] ?? ''],
                    'reference' => $validated['reference'],
                ]
            );

            return back()->with('success', 'Transaction updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    public function destroy(Agent $agent, AgentWalletTransaction $transaction)
    {
        try {
            if ($transaction->type !== 'deposit') {
                throw new \Exception('Only manual deposits can be deleted.');
            }

            $this->walletService->deleteTransaction($agent, $transaction);

            return back()->with('success', 'Transaction deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Delete failed: ' . $e->getMessage());
        }
    }
}
