@extends('layouts.app')

@section('title', 'Reports - JARVIS')

@section('header_title', 'Reports')

@section('header_action')
    <div style="display: flex; gap: 0.5rem;">
        <select id="reportMonth" onchange="loadReports()"
            style="padding: 0.5rem 1rem; background: var(--bg-tertiary); border: none; color: var(--text-primary); border-radius: var(--radius-md); cursor: pointer;">
            <option value="">This Month</option>
        </select>
        <button class="btn-primary" onclick="exportReport()">
            <i class="fas fa-download"></i> Export PDF
        </button>
    </div>
@endsection

@section('content')
    <div class="container">
        <!-- Net Worth -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <h3>Net Worth</h3>
            </div>
            <div class="card-body">
                <div style="text-align: center; padding: 2rem;">
                    <div
                        style="font-size: 3rem; font-weight: 700; background: var(--gradient-primary); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                        <span id="netWorth">₹0</span>
                    </div>
                    <p style="color: var(--text-muted); margin-top: 0.5rem;">Total Assets - Total Liabilities</p>
                </div>
                <div
                    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-top: 2rem;">
                    <div style="text-align: center;">
                        <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.5rem;">Liquid
                            Assets</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: var(--success);" id="liquidAssets">
                            ₹0</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.5rem;">
                            Investments</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: var(--info);" id="investmentAssets">
                            ₹0</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.5rem;">
                            Liabilities</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: var(--danger);" id="totalLiabilities">₹0
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Summary -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <h3>Monthly Summary</h3>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1.5rem;">
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-muted);">Income</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: var(--success);" id="monthlyIncome">
                            ₹0</div>
                    </div>
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-muted);">Expenses</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: var(--danger);" id="monthlyExpenses">₹0
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-muted);">Savings</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary-green);" id="monthlySavings">
                            ₹0</div>
                    </div>
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-muted);">Savings Rate</div>
                        <div style="font-size: 1.5rem; font-weight: 700;" id="savingsRate">0%</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Breakdown -->
        <div class="card">
            <div class="card-header">
                <h3>Expense Breakdown by Category</h3>
            </div>
            <div class="card-body">
                <div id="categoryBreakdown"></div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/reports.js') }}"></script>
@endsection