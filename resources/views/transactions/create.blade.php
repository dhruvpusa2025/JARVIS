@extends('layouts.app')

@section('title', 'Add Transaction - JARVIS')

@section('header_title', 'Add Transaction')

@section('header_action')
    <a href="{{ route('transactions.index') }}" class="btn-link">View All</a>
@endsection

@section('styles')
    <style>
        .transaction-form-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 1rem;
        }

        .form-card {
            background: var(--bg-secondary);
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow-lg);
        }

        .type-toggle {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .type-btn {
            flex: 1;
            padding: 1rem;
            background: var(--bg-tertiary);
            border: 2px solid transparent;
            border-radius: var(--radius-md);
            color: var(--text-primary);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .type-btn.active {
            background: var(--gradient-primary);
            border-color: var(--primary-green);
        }

        .amount-input {
            font-size: 2rem !important;
            font-weight: 700;
            text-align: center;
        }

        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 1rem;
            margin-top: 0.5rem;
        }

        .category-item {
            background: var(--bg-tertiary);
            border: 2px solid transparent;
            border-radius: var(--radius-md);
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .category-item:hover {
            background: var(--bg-primary);
        }

        .category-item.selected {
            border-color: var(--primary-green);
            background: rgba(16, 185, 129, 0.1);
        }

        .category-item i {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            display: block;
        }

        .payment-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 0.5rem;
        }

        .payment-method {
            background: var(--bg-tertiary);
            border: 2px solid transparent;
            border-radius: var(--radius-md);
            padding: 1.5rem 1rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-method:hover {
            background: var(--bg-primary);
        }

        .payment-method.selected {
            border-color: var(--primary-green);
            background: rgba(16, 185, 129, 0.1);
        }

        .submit-btn {
            width: 100%;
            padding: 1.25rem;
            background: var(--gradient-primary);
            border: none;
            border-radius: var(--radius-md);
            color: white;
            font-size: 1.125rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .submit-btn:hover {
            transform: scale(1.02);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 1rem;
            background: var(--bg-tertiary);
            border: 2px solid transparent;
            border-radius: var(--radius-md);
            color: var(--text-primary);
            font-size: 1rem;
        }
    </style>
@endsection

@section('content')
    <div class="transaction-form-container">
        <div class="form-card">
            <form id="transactionForm" onsubmit="handleAddTransaction(event)">
                <!-- Type Toggle -->
                <div class="type-toggle">
                    <button type="button" class="type-btn active" data-type="expense"
                        onclick="setTransactionType('expense')">
                        <i class="fas fa-arrow-up"></i> Expense
                    </button>
                    <button type="button" class="type-btn" data-type="income" onclick="setTransactionType('income')">
                        <i class="fas fa-arrow-down"></i> Income
                    </button>
                </div>

                <input type="hidden" name="type" id="transactionType" value="expense">

                <!-- Amount -->
                <div class="form-group">
                    <label>Amount</label>
                    <input type="number" name="amount" class="amount-input" placeholder="₹ 0" step="0.01" required
                        autofocus>
                </div>

                <!-- Category -->
                <div class="form-group">
                    <label>Category</label>
                    <div class="category-grid" id="categoryGrid"></div>
                    <input type="hidden" name="category" id="selectedCategory" required>
                </div>

                <!-- Payment Method -->
                <div class="form-group">
                    <label>Payment Method</label>
                    <div class="payment-methods" id="paymentMethods"></div>
                    <input type="hidden" name="account" id="selectedAccount" required>
                </div>

                <!-- Date -->
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="date" id="transactionDate" required>
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label>Description (Optional)</label>
                    <textarea name="description" rows="3" placeholder="Add notes..."></textarea>
                </div>

                <!-- Submit -->
                <button type="submit" class="submit-btn">
                    <i class="fas fa-check"></i> Save Transaction
                </button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/add-transaction.js') }}"></script>
@endsection