<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use App\Models\InvestmentEntry;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvestmentController extends Controller
{
    public function index()
    {
        return response()->json(Investment::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'investment_account_id' => 'nullable|exists:investment_accounts,id',
            'source_account_id' => 'nullable|exists:accounts,id',
            'name' => 'required|string',
            'type' => 'required|string',
            'invested_amount' => 'required|numeric',
            'units' => 'nullable|numeric',
            'buy_price' => 'nullable|numeric',
            'current_price' => 'nullable|numeric',
            'current_value' => 'nullable|numeric',
            'maturity_date' => 'nullable|date',
            'interest_rate' => 'nullable|numeric',
            'is_sip' => 'boolean',
            'sip_status' => 'nullable|string|in:ACTIVE,STOPPED,PAUSED',
            'sip_amount' => 'nullable|numeric',
            'sip_date' => 'nullable|integer'
        ]);

        if ($request->is_sip && !isset($validated['sip_status'])) {
            $validated['sip_status'] = 'ACTIVE';
        }

        $investment = Investment::create($validated);
        return response()->json($investment, 201);
    }

    public function update(Request $request, Investment $investment)
    {
        $investment->update($request->all());
        return response()->json($investment);
    }

    public function destroy(Investment $investment)
    {
        $investment->delete();
        return response()->json(null, 204);
    }

    public function recordInstallment(Request $request, Investment $investment)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'units' => 'nullable|numeric',
            'date' => 'required|date'
        ]);

        return DB::transaction(function () use ($validated, $investment) {
            $amount = $validated['amount'];
            $date = $validated['date'];
            
            // 1. Deduct from Source Account
            if ($investment->source_account_id) {
                $account = Account::findOrFail($investment->source_account_id);
                $account->balance -= $amount;
                $account->save();
                
                // Track as transaction
                Transaction::create([
                    'account_id' => $account->id,
                    'type' => 'expense',
                    'amount' => $amount,
                    'date' => $date,
                    'description' => "SIP Installment: {$investment->name}",
                    'category_id' => 1 // Assuming 1 is Investment or General, logic simplified
                ]);
            }

            // 2. Create Entry
            $price = $investment->current_price ?? 0;
            $units = $validated['units'];
            
            // Auto-calculate units if not provided but price exists
            if (empty($units) && $price > 0) {
                 $units = $amount / $price;
            }

            // Create Entry
            $entry = InvestmentEntry::create([
                'investment_id' => $investment->id,
                'type' => 'SIP_INSTALLMENT',
                'amount' => $amount,
                'price' => $price,
                'units' => $units,
                'date' => $date
            ]);

            // 3. Update Investment Totals
            $investment->invested_amount += $amount;
            if ($units > 0) {
                $investment->units += $units;
                $investment->current_value = $investment->units * $price;
            }
            $investment->save();

            return response()->json($entry);
        });
    }

    public function updateEntry(Request $request, InvestmentEntry $entry)
    {
        $validated = $request->validate([
             'price' => 'required|numeric',
             'units' => 'nullable|numeric'
        ]);

        $entry->load('investment');
        $investment = $entry->investment;

        // Revert old units from investment
        if ($entry->units > 0) {
            $investment->units -= $entry->units;
        }

        $entry->price = $validated['price'];
        
        // Calculate new units
        if (isset($validated['units'])) {
             $entry->units = $validated['units'];
        } else {
             if ($validated['price'] > 0) {
                 $entry->units = $entry->amount / $validated['price'];
             }
        }
        
        $entry->save();

        // Apply new units to investment
        if ($entry->units > 0) {
            $investment->units += $entry->units;
        }

        // Update current value based on latest known price (or this entry's price if it's latest?) 
        // For simplicity, just use `investment->current_price` * new units total
        $investment->current_value = $investment->units * ($investment->current_price ?? $entry->price);
        $investment->save();

        return response()->json($entry);
    }
    
    // private function recalculateInvestment... removed in favor of inline delta updates
