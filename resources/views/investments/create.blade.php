@extends('layouts.app')

@section('title', 'Add Investment - JARVIS')

@section('header_title', 'Add Investment')

@section('header_action')
    <a href="{{ route('investments.index') }}" class="btn-link">View Investments</a>
@endsection

@section('styles')
    <style>
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 1rem;
        }

        .form-card {
            background: var(--bg-secondary);
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
                /* Let form-group margin handle it */
            }
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
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            background: var(--bg-tertiary);
            border: 2px solid transparent;
            border-radius: var(--radius-md);
            color: var(--text-primary);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-green);
            background: var(--bg-primary);
        }

        .type-dependent-fields {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .type-dependent-fields.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-top: 0.5rem;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
            width: 1.25rem;
            height: 1.25rem;
            accent-color: var(--primary-green);
        }

        .submit-btn {
            width: 100%;
            padding: 1rem;
            background: var(--gradient-primary);
            border: none;
            border-radius: var(--radius-md);
            color: white;
            font-size: 1.125rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
        }
    </style>
@endsection

@section('content')
    <div class="form-container">
        <div class="form-card">
            <h2 style="margin-bottom: 1.5rem;">New Investment Asset</h2>
            <form id="investmentForm" onsubmit="handleAddInvestment(event)">
                <div class="form-group">
                    <label>Investment Name</label>
                    <input type="text" name="name" placeholder="e.g., SBI Bluechip Fund, HDFC Bank, FD-123" required>
                </div>

                <div class="form-group">
                    <label>Investment Account (Optional)</label>
                    <select name="investment_account_id">
                        <option value="">-- Select Account (e.g. Zerodha) --</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }} ({{ $account->broker }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Asset Type</label>
                    <select name="type" id="typeSelect" onchange="toggleTypeFields()" required>
                        <option value="">Select Type</option>
                        <option value="mutual_fund">Mutual Fund</option>
                        <option value="stock">Stock / Equity</option>
                        <option value="fd">Fixed Deposit (FD)</option>
                        <option value="rd">Recurring Deposit (RD)</option>
                        <option value="real_estate">Real Estate</option>
                        <option value="gold">Gold / Commodity</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <!-- Common Amount Field -->
                <div class="form-group">
                    <label>Total Invested Amount (₹)</label>
                    <input type="number" name="invested_amount" step="0.01" placeholder="0.00" required>
                    <small style="color: var(--text-muted);">For SIPs, enter initial amount or 0 if starting fresh.</small>
                </div>

                <!-- Fields for Unit-based (MF, Stocks) -->
                <div id="unitFields" class="type-dependent-fields">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Quantity / Units</label>
                            <input type="number" name="units" step="0.0001" placeholder="e.g. 10.53">
                        </div>
                        <div class="form-group">
                            <label>Buy Price / NAV (Per Unit)</label>
                            <input type="number" name="buy_price" step="0.01" placeholder="0.00">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Current Price / NAV (Per Unit)</label>
                        <input type="number" name="current_price" step="0.01"
                            placeholder="Optional - fetches latest if unset">
                    </div>
                </div>

                <!-- Fields for Interest-based (FD, RD) -->
                <div id="interestFields" class="type-dependent-fields">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Interest Rate (%)</label>
                            <input type="number" name="interest_rate" step="0.01" placeholder="e.g. 7.5">
                        </div>
                        <div class="form-group">
                            <label>Maturity Date</label>
                            <input type="date" name="maturity_date">
                        </div>
                    </div>
                </div>

                <!-- SIP Option -->
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" name="is_sip" id="sipCheck" onchange="toggleSipFields()">
                        <label for="sipCheck" style="margin:0;">This is a SIP (Systematic Investment Plan)</label>
                    </div>
                </div>

                <div id="sipFields" class="type-dependent-fields">
                    <div class="form-row">
                        <div class="form-group">
                            <label>SIP Amount</label>
                            <input type="number" name="sip_amount" step="0.01" placeholder="e.g. 5000">
                        </div>
                        <div class="form-group">
                            <label>SIP Date (Day of Month)</label>
                            <input type="number" name="sip_date" min="1" max="31" placeholder="e.g. 5">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Current Value (Total)</label>
                    <input type="number" name="current_value" step="0.01" placeholder="Override calculated value">
                    <small style="color: var(--text-muted);">Leave empty to auto-calculate based on units * price.</small>
                </div>

                <div class="form-group">
                    <label style="margin-bottom: 2rem;">&nbsp;</label>
                    <button type="submit" class="submit-btn" id="submitBtn">
                        <i class="fas fa-plus-circle"></i> Add Investment
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/add-investment.js') }}"></script>
@endsection