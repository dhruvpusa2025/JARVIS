@extends('layouts.app')

@section('title', 'Dashboard - JARVIS')

@section('header_title', 'Dashboard')

@section('header_action')
    <div class="header-actions">
        <button class="btn-icon">
            <i class="fas fa-bell"></i>
            <span class="badge">3</span>
        </button>
        <button class="btn-icon">
            <i class="fas fa-user-circle"></i>
        </button>
    </div>
@endsection

@section('content')
    <div class="container">
        <!-- Summary Cards -->
        <div class="summary-grid">
            <div class="summary-card balance">
                <div class="card-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="card-content">
                    <p class="card-label">Total Balance</p>
                    <h2 class="card-value" id="totalBalance">₹0</h2>
                    <span class="card-trend positive">
                        <i class="fas fa-arrow-up"></i> 12.5%
                    </span>
                </div>
            </div>

            <div class="summary-card income">
                <div class="card-icon">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <div class="card-content">
                    <p class="card-label">Monthly Income</p>
                    <h2 class="card-value" id="monthlyIncome">₹0</h2>
                    <span class="card-subtitle">This month</span>
                </div>
            </div>

            <div class="summary-card expense">
                <div class="card-icon">
                    <i class="fas fa-arrow-up"></i>
                </div>
                <div class="card-content">
                    <p class="card-label">Monthly Expenses</p>
                    <h2 class="card-value" id="monthlyExpenses">₹0</h2>
                    <span class="card-subtitle">This month</span>
                </div>
            </div>

            <div class="summary-card investments">
                <div class="card-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="card-content">
                    <p class="card-label">Investments</p>
                    <h2 class="card-value" id="totalInvestments">₹0</h2>
                    <span class="card-trend positive">
                        <i class="fas fa-arrow-up"></i> +7.9%
                    </span>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="charts-row">
            <div class="card chart-card">
                <div class="card-header">
                    <h3>Expense Breakdown</h3>
                    <select class="period-select">
                        <option>This Month</option>
                        <option>Last Month</option>
                        <option>Last 3 Months</option>
                    </select>
                </div>
                <div class="card-body">
                    <canvas id="expenseChart"></canvas>
                </div>
            </div>

            <div class="card chart-card">
                <div class="card-header">
                    <h3>Cash Flow</h3>
                    <select class="period-select">
                        <option>Last 6 Months</option>
                        <option>Last Year</option>
                    </select>
                </div>
                <div class="card-body">
                    <canvas id="cashFlowChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="card">
            <div class="card-header">
                <h3>Recent Transactions</h3>
                <a href="{{ route('transactions.index') }}" class="btn-link">View All</a>
            </div>
            <div class="card-body">
                <div class="transactions-list" id="recentTransactions">
                    <!-- Transactions will be loaded here -->
                </div>
            </div>
        </div>

        <!-- Quick Actions (Mobile) -->
        <div class="quick-actions mobile-only">
            <a href="{{ route('transactions.create') }}" class="quick-action-btn">
                <i class="fas fa-plus"></i>
                <span>Add Transaction</span>
            </a>
            <a href="{{ route('transfer.index') }}" class="quick-action-btn">
                <i class="fas fa-exchange-alt"></i>
                <span>Transfer</span>
            </a>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
@endsection