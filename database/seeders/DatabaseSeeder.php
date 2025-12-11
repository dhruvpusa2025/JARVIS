<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Account;
use App\Models\Category;
use App\Models\Investment;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Categories
        $categories = [
            ['name' => 'Salary', 'type' => 'income', 'icon' => 'fa-briefcase', 'color' => '#10b981'],
            ['name' => 'Food', 'type' => 'expense', 'icon' => 'fa-utensils', 'color' => '#f59e0b'],
            ['name' => 'Transport', 'type' => 'expense', 'icon' => 'fa-car', 'color' => '#3b82f6'],
            ['name' => 'Shopping', 'type' => 'expense', 'icon' => 'fa-shopping-bag', 'color' => '#8b5cf6'],
            ['name' => 'Bills', 'type' => 'expense', 'icon' => 'fa-file-invoice', 'color' => '#ef4444'],
            ['name' => 'Entertainment', 'type' => 'expense', 'icon' => 'fa-film', 'color' => '#ec4899']
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // Accounts
        Account::create(['name' => 'HDFC Bank', 'type' => 'bank', 'balance' => 45000, 'account_number' => '****1234']);
        Account::create(['name' => 'SBI Bank', 'type' => 'bank', 'balance' => 32000, 'account_number' => '****5678']);
        Account::create(['name' => 'Cash', 'type' => 'cash', 'balance' => 15000]);
        Account::create(['name' => 'HDFC Credit Card', 'type' => 'credit_card', 'balance' => -8000, 'credit_limit' => 50000]);

        // Investments
        Investment::create([
            'name' => 'HDFC Balanced Advantage Fund',
            'type' => 'mutual_fund',
            'units' => 1250,
            'buy_price' => 80,
            'current_price' => 85.50,
            'invested_amount' => 100000,
            'current_value' => 106875
        ]);
    }
}
