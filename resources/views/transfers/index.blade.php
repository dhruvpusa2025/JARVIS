@extends('layouts.app')

@section('title', 'Transfer - JARVIS')

@section('header_title', 'Transfer Money')

@section('header_action')
    <a href="{{ route('accounts.index') }}" class="btn-link">View Accounts</a>
@endsection

@section('styles')
    <style>
        .transfer-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 1rem;
        }

        .transfer-card {
            background: var(--bg-secondary);
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow-lg);
        }

        .transfer-visual {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin: 2rem 0;
        }

        .account-box {
            flex: 1;
            background: var(--bg-tertiary);
            padding: 1.5rem;
            border-radius: var(--radius-md);
            text-align: center;
        }

        .account-box.selected {
            border: 2px solid var(--primary-green);
            background: rgba(16, 185, 129, 0.1);
        }

        .transfer-arrow {
            font-size: 2rem;
            color: var(--primary-green);
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

        .form-group select,
        .form-group input {
            width: 100%;
            padding: 1rem;
            background: var(--bg-tertiary);
            border: 2px solid transparent;
            border-radius: var(--radius-md);
            color: var(--text-primary);
            font-size: 1rem;
        }

        .form-group select:focus,
        .form-group input:focus {
            outline: none;
            border-color: var(--primary-green);
        }

        .amount-input {
            font-size: 2rem !important;
            font-weight: 700;
            text-align: center;
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

        .balance-info {
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
        }
    </style>
@endsection

@section('content')
    <div class="transfer-container">
        <div class="transfer-card">
            <div style="margin-bottom: 2rem;">
                <h2>Transfer Between Accounts</h2>
                <p style="color: var(--text-muted);">Move money between your accounts</p>
            </div>

            <form id="transferForm" onsubmit="handleTransfer(event)">
                <!-- From Account -->
                <div class="form-group">
                    <label>From Account</label>
                    <select name="fromAccount" id="fromAccount" required onchange="updateBalanceInfo()">
                        <option value="">Select source account</option>
                    </select>
                    <div class="balance-info" id="fromBalance"></div>
                </div>

                <!-- Visual Transfer Arrow -->
                <div class="transfer-visual">
                    <div class="account-box" id="fromBox">
                        <i class="fas fa-university" style="font-size: 2rem; color: var(--text-muted);"></i>
                        <div style="margin-top: 0.5rem; color: var(--text-muted);">Source</div>
                    </div>
                    <div class="transfer-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                    <div class="account-box" id="toBox">
                        <i class="fas fa-wallet" style="font-size: 2rem; color: var(--text-muted);"></i>
                        <div style="margin-top: 0.5rem; color: var(--text-muted);">Destination</div>
                    </div>
                </div>

                <!-- To Account -->
                <div class="form-group">
                    <label>To Account</label>
                    <select name="toAccount" id="toAccount" required onchange="updateBalanceInfo()">
                        <option value="">Select destination account</option>
                    </select>
                    <div class="balance-info" id="toBalance"></div>
                </div>

                <!-- Amount -->
                <div class="form-group">
                    <label>Amount</label>
                    <input type="number" name="amount" class="amount-input" placeholder="₹ 0" step="0.01" required>
                </div>

                <!-- Date -->
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="date" id="transferDate" required>
                </div>

                <!-- Notes -->
                <div class="form-group">
                    <label>Notes (Optional)</label>
                    <input type="text" name="notes" placeholder="e.g., ATM withdrawal, Bank transfer">
                </div>

                <!-- Submit -->
                <button type="submit" class="submit-btn">
                    <i class="fas fa-exchange-alt"></i> Transfer Money
                </button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/transfer.js') }}"></script>
@endsection