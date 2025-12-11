<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        return response()->json(Transaction::with('category')->latest()->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:income,expense,transfer',
            'amount' => 'required|numeric',
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'to_account_id' => 'nullable|required_if:type,transfer|exists:accounts,id'
        ]);

        return DB::transaction(function () use ($validated) {
            $transaction = Transaction::create($validated);

            // Update Account Balance
            $account = Account::find($validated['account_id']);
            if ($validated['type'] === 'income') {
                $account->increment('balance', $validated['amount']);
            } elseif ($validated['type'] === 'expense') {
                $account->decrement('balance', $validated['amount']);
            } elseif ($validated['type'] === 'transfer') {
                $account->decrement('balance', $validated['amount']);
                $toAccount = Account::find($validated['to_account_id']);
                $toAccount->increment('balance', $validated['amount']);
            }

            return response()->json($transaction, 201);
        });
    }

    public function destroy(Transaction $transaction)
    {
        DB::transaction(function () use ($transaction) {
            // Revert balance
            $account = $transaction->account;
            if ($transaction->type === 'income') {
                $account->decrement('balance', $transaction->amount);
            } elseif ($transaction->type === 'expense') {
                $account->increment('balance', $transaction->amount);
            } elseif ($transaction->type === 'transfer') {
                $account->increment('balance', $transaction->amount);
                $toAccount = $transaction->toAccount;
                if ($toAccount) {
                    $toAccount->decrement('balance', $transaction->amount);
                }
            }

            $transaction->delete();
        });

        return response()->json(null, 204);
    }
}
