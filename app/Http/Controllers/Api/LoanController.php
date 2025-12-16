<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index()
    {
        return response()->json(Loan::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lender' => 'required|string',
            'type' => 'required|string',
            'loan_type' => 'required|string|in:BANK,PERSONAL',
            'principal_amount' => 'required|numeric',
            'outstanding_amount' => 'required|numeric',
            'start_date' => 'nullable|date',
            // Bank Loan Specific
            'interest_rate' => 'nullable|required_if:loan_type,BANK|numeric',
            'emi_amount' => 'nullable|required_if:loan_type,BANK|numeric',
            'emi_date' => 'nullable|required_if:loan_type,BANK|integer',
            // Personal Loan Specific
            'interest_payment_frequency' => 'nullable|string|in:MONTHLY,WEEKLY,NONE',
            'interest_payment_date' => 'nullable|integer'
        ]);

        $loan = Loan::create($validated);
        return response()->json($loan, 201);
    }

    public function update(Request $request, Loan $loan)
    {
        $loan->update($request->all());
        return response()->json($loan);
    }

    public function destroy(Loan $loan)
    {
        $loan->delete();
        return response()->json(null, 204);
    }

    public function repay(Request $request, Loan $loan)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'account_id' => 'required|exists:accounts,id',
            'date' => 'required|date',
            'payment_type' => 'required|in:PRINCIPAL,INTEREST'
        ]);

        return DB::transaction(function () use ($validated, $loan) {
            $amount = $validated['amount'];
            $account = Account::findOrFail($validated['account_id']);

            // 1. Deduct from Source Account
            if ($account->balance < $amount) {
                return response()->json(['message' => 'Insufficient balance'], 400);
            }
            $account->balance -= $amount;
            $account->save();

            // 2. Determine Transaction Details
            $isPrincipal = $validated['payment_type'] === 'PRINCIPAL';
            $description = $isPrincipal
                ? "Loan Repayment (Principal): {$loan->lender}"
                : "Loan Interest Payment: {$loan->lender}";

            // 3. Create Transaction
            Transaction::create([
                'account_id' => $account->id,
                'type' => 'expense',
                'amount' => $amount,
                'date' => $validated['date'],
                'description' => $description,
                'category_id' => 1 // TODO: Dynamic category for Loans
            ]);

            // 4. Update Loan (Only if Principal)
            if ($isPrincipal) {
                $loan->outstanding_amount -= $amount;
                if ($loan->outstanding_amount < 0)
                    $loan->outstanding_amount = 0;
                $loan->save();
            }

            return response()->json([
                'message' => 'Payment recorded successfully',
                'loan' => $loan->fresh()
            ]);
        });
    }
}
