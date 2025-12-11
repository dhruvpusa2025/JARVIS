<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Loan;
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
            'principal_amount' => 'required|numeric',
            'interest_rate' => 'required|numeric',
            'emi_amount' => 'required|numeric',
            'emi_date' => 'required|integer',
            'outstanding_amount' => 'required|numeric',
            'start_date' => 'nullable|date'
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
}
