<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use Illuminate\Http\Request;

class InvestmentController extends Controller
{
    public function index()
    {
        return response()->json(Investment::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
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
            'sip_amount' => 'nullable|numeric',
            'sip_date' => 'nullable|integer'
        ]);

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
}
