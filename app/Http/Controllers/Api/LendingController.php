<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lending;
use Illuminate\Http\Request;

class LendingController extends Controller
{
    public function index()
    {
        return response()->json(Lending::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'borrower' => 'required|string',
            'amount' => 'required|numeric',
            'interest_rate' => 'required|numeric',
            'frequency' => 'required|string',
            'outstanding_amount' => 'required|numeric',
            'start_date' => 'required|date',
            'return_date' => 'nullable|date'
        ]);

        $lending = Lending::create($validated);
        return response()->json($lending, 201);
    }

    public function update(Request $request, Lending $lending)
    {
        $lending->update($request->all());
        return response()->json($lending);
    }

    public function destroy(Lending $lending)
    {
        $lending->delete();
        return response()->json(null, 204);
    }
}
