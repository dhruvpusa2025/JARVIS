@extends('layouts.app')

@section('title', 'Transactions - JARVIS')

@section('header_title', 'Transactions')

@section('header_action')
    <a href="{{ route('transactions.create') }}" class="btn-primary">
        <i class="fas fa-plus"></i> Add
    </a>
@endsection

@section('content')
    <div class="container">
        <!-- Filters -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-body" style="padding: 1rem;">
                <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
                    <!-- Search -->
                    <div style="flex: 1; min-width: 200px; position: relative;">
                        <i class="fas fa-search"
                            style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                        <input type="text" id="searchInput" placeholder="Search transactions..."
                            onkeyup="filterTransactions()"
                            style="width: 100%; padding: 0.5rem 0.5rem 0.5rem 2.5rem; background: var(--bg-tertiary); border: none; color: var(--text-primary); border-radius: var(--radius-sm);">
                    </div>

                    <!-- Type Filter -->
                    <select id="filterType" onchange="filterTransactions()"
                        style="padding: 0.5rem; background: var(--bg-tertiary); border: none; color: var(--text-primary); border-radius: var(--radius-sm);">
                        <option value="all">All Types</option>
                        <option value="income">Income</option>
                        <option value="expense">Expense</option>
                        <option value="transfer">Transfer</option>
                    </select>

                    <!-- Category Filter -->
                    <select id="filterCategory" onchange="filterTransactions()"
                        style="padding: 0.5rem; background: var(--bg-tertiary); border: none; color: var(--text-primary); border-radius: var(--radius-sm);">
                        <option value="all">All Categories</option>
                    </select>

                    <!-- Date Range -->
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <input type="date" id="startDate" onchange="filterTransactions()"
                            style="padding: 0.5rem; background: var(--bg-tertiary); border: none; color: var(--text-primary); border-radius: var(--radius-sm);">
                        <span style="color: var(--text-muted);">to</span>
                        <input type="date" id="endDate" onchange="filterTransactions()"
                            style="padding: 0.5rem; background: var(--bg-tertiary); border: none; color: var(--text-primary); border-radius: var(--radius-sm);">
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions List -->
        <div class="card">
            <div class="card-header">
                <h3>All Transactions</h3>
                <span id="transactionCount">0 transactions</span>
            </div>
            <div class="card-body">
                <div id="transactionsList"></div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/transactions.js') }}"></script>
@endsection