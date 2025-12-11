<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        return response()->json(Account::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
            'balance' => 'required|numeric',
            'account_number' => 'nullable|string',
            'credit_limit' => 'nullable|numeric'
        ]);

        $account = Account::create($validated);
        return response()->json($account, 201);
    }

    public function show(Account $account)
    {
        return response()->json($account);
    }

    public function update(Request $request, Account $account)
    {
        $validated = $request->validate([
            'name' => 'string',
            'type' => 'string',
            'balance' => 'numeric',
            'account_number' => 'nullable|string',
            'credit_limit' => 'nullable|numeric'
        ]);

        $account->update($validated);
        return response()->json($account);
    }

    public function destroy(Account $account)
    {
        $account->delete();
        return response()->json(null, 204);
    }
}
