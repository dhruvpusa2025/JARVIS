@extends('layouts.app')

@section('title', 'Accounts - JARVIS')

@section('header_title', 'Accounts')

@section('header_action')
    <button class="btn-primary" onclick="openAddAccountModal()">
        <i class="fas fa-plus"></i> Add Account
    </button>
@endsection

@section('styles')
    <style>
        .btn-primary {
            background: var(--gradient-primary);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius-md);
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: transform 0.3s ease;
        }

        .btn-primary:hover {
            transform: scale(1.05);
        }

        .btn-secondary {
            background: var(--bg-tertiary);
            color: var(--text-primary);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius-md);
            font-weight: 500;
            cursor: pointer;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .modal-content {
            background: var(--bg-secondary);
            border-radius: var(--radius-lg);
            max-width: 500px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--bg-tertiary);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-close {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1.5rem;
            cursor: pointer;
        }

        .modal-footer {
            padding: 1.5rem;
            border-top: 1px solid var(--bg-tertiary);
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }

        .form-group {
            padding: 0 1.5rem;
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            background: var(--bg-tertiary);
            border: 1px solid transparent;
            border-radius: var(--radius-md);
            color: var(--text-primary);
            font-size: 1rem;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-green);
        }

        .account-card {
            background: var(--bg-tertiary);
            border-radius: var(--radius-md);
            padding: 1.5rem;
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .account-info {
            flex: 1;
        }

        .account-name {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .account-type {
            color: var(--text-muted);
            font-size: 0.875rem;
        }

        .account-balance {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .account-balance.positive {
            color: var(--success);
        }

        .account-balance.negative {
            color: var(--danger);
        }

        .account-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-icon-small {
            background: var(--bg-secondary);
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            color: var(--text-primary);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-icon-small:hover {
            background: var(--primary-green);
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <!-- Account Summary -->
        <div class="summary-grid">
            <div class="summary-card balance">
                <div class="card-icon"><i class="fas fa-university"></i></div>
                <div class="card-content">
                    <p class="card-label">Bank Accounts</p>
                    <h2 class="card-value" id="bankTotal">₹0</h2>
                    <span class="card-subtitle" id="bankCount">0 accounts</span>
                </div>
            </div>

            <div class="summary-card income">
                <div class="card-icon"><i class="fas fa-money-bill-wave"></i></div>
                <div class="card-content">
                    <p class="card-label">Cash</p>
                    <h2 class="card-value" id="cashTotal">₹0</h2>
                    <span class="card-subtitle">Available cash</span>
                </div>
            </div>

            <div class="summary-card expense">
                <div class="card-icon"><i class="fas fa-credit-card"></i></div>
                <div class="card-content">
                    <p class="card-label">Credit Cards</p>
                    <h2 class="card-value" id="creditTotal">₹0</h2>
                    <span class="card-subtitle" id="creditCount">0 cards</span>
                </div>
            </div>

            <div class="summary-card investments">
                <div class="card-icon"><i class="fas fa-wallet"></i></div>
                <div class="card-content">
                    <p class="card-label">Net Balance</p>
                    <h2 class="card-value" id="netBalance">₹0</h2>
                    <span class="card-subtitle">Total liquid assets</span>
                </div>
            </div>
        </div>

        <!-- Accounts List -->
        <div class="card">
            <div class="card-header">
                <h3>All Accounts</h3>
            </div>
            <div class="card-body">
                <div id="accountsList"></div>
            </div>
        </div>
    </div>

    <!-- Add Account Modal -->
    <div id="addAccountModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Account</h2>
                <button class="modal-close" onclick="closeAddAccountModal()"><i class="fas fa-times"></i></button>
            </div>
            <form id="addAccountForm" onsubmit="handleAddAccount(event)">
                <div class="form-group">
                    <label>Account Type</label>
                    <select name="type" required onchange="toggleAccountFields(this.value)">
                        <option value="">Select type</option>
                        <option value="bank">Bank Account</option>
                        <option value="cash">Cash</option>
                        <option value="credit_card">Credit Card</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Account Name</label>
                    <input type="text" name="name" placeholder="e.g., HDFC Bank" required>
                </div>
                <div class="form-group bank-only">
                    <label>Account Number (Last 4 digits)</label>
                    <input type="text" name="account_number" placeholder="****1234" maxlength="8">
                </div>
                <div class="form-group">
                    <label>Current Balance</label>
                    <input type="number" name="balance" placeholder="0" step="0.01" required>
                </div>
                <div class="form-group credit-only" style="display: none;">
                    <label>Credit Limit</label>
                    <input type="number" name="credit_limit" placeholder="50000" step="0.01">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeAddAccountModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Add Account</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/accounts.js') }}"></script>
@endsection