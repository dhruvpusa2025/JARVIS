<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Investment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Total Balance
        $totalBalance = Account::sum('balance');

        // 2. Monthly Income & Expenses
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $monthlyStats = Transaction::selectRaw('type, SUM(amount) as total')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->groupBy('type')
            ->pluck('total', 'type');

        $monthlyIncome = $monthlyStats['income'] ?? 0;
        $monthlyExpenses = $monthlyStats['expense'] ?? 0;

        // 3. Total Investments
        // Logic: Sum of current_value if set, else invested_amount
        $investments = Investment::all();
        $totalInvestments = $investments->sum(function ($inv) {
            return $inv->current_value ?? $inv->invested_amount ?? 0;
        });

        // 4. Recent Transactions (Limit 5)
        $recentTransactions = Transaction::with('category')
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        // 5. Expense Breakdown (This Month)
        // We need category names. If category is a relationship, we join.
        // Assuming transactions have category_id or direct category string. 
        // Based on previous code, it seems mixed or using category_id relationship. 
        // Let's assume standard relationship for robust query, but fallback to string if needed.
        // Checking Transaction Model would be ideal, but let's try standard relation first.
        $expenseBreakdown = Transaction::where('type', 'expense')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->selectRaw('categories.name as category_name, SUM(transactions.amount) as total')
            ->groupBy('categories.name')
            ->pluck('total', 'category_name');

        // Fallback if that returns empty (e.g. if category is just a string col in older version)
        if ($expenseBreakdown->isEmpty()) {
            // Try grouping by the 'category' string column if it exists
            $expenseBreakdown = Transaction::where('type', 'expense')
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->selectRaw('category as category_name, SUM(amount) as total')
                ->groupBy('category')
                ->pluck('total', 'category_name');
        }

        // 6. Cash Flow (Last 6 Months)
        $sixMonthsAgo = now()->subMonths(6);
        $cashFlow = Transaction::selectRaw('
                DATE_FORMAT(date, "%Y-%m") as month_year, 
                type, 
                SUM(amount) as total
            ')
            ->where('date', '>=', $sixMonthsAgo)
            ->groupBy('month_year', 'type')
            ->orderBy('month_year')
            ->get()
            ->groupBy('month_year')
            ->map(function ($items) {
                return [
                    'income' => $items->where('type', 'income')->sum('total'),
                    'expense' => $items->where('type', 'expense')->sum('total'),
                ];
            });

        return response()->json([
            'totalBalance' => $totalBalance,
            'monthlyIncome' => $monthlyIncome,
            'monthlyExpenses' => $monthlyExpenses,
            'totalInvestments' => $totalInvestments,
            'recentTransactions' => $recentTransactions,
            'expenseBreakdown' => $expenseBreakdown,
            'cashFlow' => $cashFlow
        ]);
    }
}
