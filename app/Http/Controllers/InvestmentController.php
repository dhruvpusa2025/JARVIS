<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Investment;
use App\Models\InvestmentAccount;

class InvestmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('investments.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $investmentAccounts = InvestmentAccount::all();
        $sourceAccounts = Account::whereIn('type', ['bank', 'savings'])->get();
        return view('investments.create', [
            'investmentAccounts' => $investmentAccounts,
            'sourceAccounts' => $sourceAccounts
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $investment = Investment::with(['account', 'sourceAccount', 'entries'])->findOrFail($id);
        return view('investments.show', compact('investment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
