<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InvestmentAccount;
use Illuminate\Http\Request;

class InvestmentAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return InvestmentAccount::withCount('investments')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'broker' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:255',
        ]);

        $account = InvestmentAccount::create($validated);

        return response()->json($account, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return InvestmentAccount::with('investments')->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $account = InvestmentAccount::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'broker' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:255',
        ]);

        $account->update($validated);

        return response()->json($account);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $account = InvestmentAccount::findOrFail($id);
        $account->delete();

        return response()->json(null, 204);
    }

    public function uploadHoldings(Request $request, string $id)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $account = InvestmentAccount::findOrFail($id);
        $file = $request->file('file');
        $path = $file->getPathname();

        // OpenSpout v4 direct instantiation
        $reader = new \OpenSpout\Reader\XLSX\Reader();
        $reader->open($path);

        $headers = [];
        $headerMap = [];
        $rowsProcessed = 0;

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $cells = $row->getCells();
                $data = [];
                foreach ($cells as $cell) {
                    $data[] = $cell->getValue();
                }

                if (empty($headers)) {
                    $headers = $data;
                    // Lowercase and trim headers for easier matching
                    foreach ($headers as $index => $header) {
                        $headerMap[trim(strtolower($header))] = $index;
                    }
                    continue;
                }

                // Helper to safely get value by header name
                $getVal = function ($key) use ($data, $headerMap) {
                    $idx = $headerMap[$key] ?? null;
                    return $idx !== null ? ($data[$idx] ?? null) : null;
                };

                $isin = $getVal('isin');
                $symbol = $getVal('symbol');

                if (!$isin || !$symbol)
                    continue; // Skip invalid rows

                $units = (float) $getVal('quantity available');
                $buyPrice = (float) $getVal('average price');
                $prevClose = (float) $getVal('previous closing price');
                $pnl = (float) $getVal('unrealized p&l');
                $pnlPct = (float) $getVal('unrealized p&l pct.');

                $sector = $getVal('sector');

                $investedAmount = $units * $buyPrice;
                // Current value = Invested + PnL
                $currentValue = $investedAmount + $pnl;

                \App\Models\Investment::updateOrCreate(
                    [
                        'investment_account_id' => $account->id,
                        'isin' => $isin,
                    ],
                    [
                        'name' => $symbol,
                        'symbol' => $symbol,
                        'sector' => $sector,
                        'type' => 'STOCK', // Default to STOCK for now
                        'units' => $units,
                        'buy_price' => $buyPrice,
                        'previous_close_price' => $prevClose,
                        'current_price' => $prevClose, // Use prev close as current proxy
                        'invested_amount' => $investedAmount,
                        'current_value' => $currentValue,
                        'unrealized_pnl' => $pnl,
                        'unrealized_pnl_pct' => $pnlPct,
                        'quantity_discrepant' => (float) $getVal('quantity discrepant'),
                        'quantity_long_term' => (float) $getVal('quantity long term'),
                        'quantity_pledged_margin' => (float) $getVal('quantity pledged (margin)'),
                        'quantity_pledged_loan' => (float) $getVal('quantity pledged (loan)'),
                    ]
                );

                $rowsProcessed++;
            }
            break; // Only process the first sheet
        }

        $reader->close();

        return response()->json(['message' => "Processed $rowsProcessed holdings."]);
    }
}
