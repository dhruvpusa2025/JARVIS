<?php

namespace App\Http\Controllers;

use App\Models\RecurringIncome;
use Illuminate\Http\Request;

class RecurringIncomeController extends Controller
{
    public function index()
    {
        return response()->json(RecurringIncome::with('account')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|string|in:SALARY,BUSINESS,RENT,OTHER',
            'day_of_month' => 'required|integer|min:1|max:31',
            'account_id' => 'nullable|exists:accounts,id'
        ]);

        $income = RecurringIncome::create($validated);
        return response()->json($income, 201);
    }

    public function show($id)
    {
        return response()->json(RecurringIncome::with('account')->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $income = RecurringIncome::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'amount' => 'sometimes|numeric|min:0',
            'type' => 'sometimes|string|in:SALARY,BUSINESS,RENT,OTHER',
            'day_of_month' => 'sometimes|integer|min:1|max:31',
            'account_id' => 'nullable|exists:accounts,id'
        ]);

        $income->update($validated);
        return response()->json($income);
    }

    public function destroy($id)
    {
        $income = RecurringIncome::findOrFail($id);
        $income->delete();
        return response()->json(['message' => 'Recurring income deleted']);
    }
}
