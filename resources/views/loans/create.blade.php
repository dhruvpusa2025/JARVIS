@extends('layouts.app')

@section('title', 'Add Loan - JARVIS')

@section('header_title', 'Add Loan')

@section('header_action')
    <a href="{{ route('loans.index') }}" class="btn-link">View Loans</a>
@endsection

@section('styles')
    <style>
        .form-container {
            max-width: 700px;
            margin: 0 auto;
            padding: 1rem;
        }

        .form-card {
            background: var(--bg-secondary);
            border-radius: var(--radius-lg);
            padding: 2rem;
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
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            background: var(--bg-tertiary);
            border: 2px solid transparent;
            border-radius: var(--radius-md);
            color: var(--text-primary);
            font-size: 1rem;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-green);
        }

        .calculated-field {
            background: rgba(16, 185, 129, 0.1);
            border: 2px solid var(--primary-green) !important;
            font-weight: 700;
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
        }
    </style>
@endsection

@section('content')
    <div class="form-container">
        <div class="form-card">
            <h2 style="margin-bottom: 1.5rem;">New Loan</h2>
            <form id="loanForm" onsubmit="handleAddLoan(event)">
                <div class="form-group">
                    <label>Loan Category</label>
                    <select name="loan_type" id="loanCategory" required onchange="toggleLoanFields()">
                        <option value="BANK">Bank Loan</option>
                        <option value="PERSONAL">Personal / Friend</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Loan Purpose</label>
                    <select name="type" required>
                        <option value="">Select purpose</option>
                        <option value="home">Home Loan</option>
                        <option value="car">Car Loan</option>
                        <option value="personal">Personal Loan</option>
                        <option value="education">Education Loan</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Lender Name</label>
                    <input type="text" name="lender" placeholder="e.g., HDFC Bank, SBI, Friend Name" required>
                </div>

                <div class="form-group">
                    <label>Principal Amount</label>
                    <input type="number" name="principal" step="0.01" placeholder="5000000" required
                        oninput="calculateEMI()">
                </div>

                <div class="form-group">
                    <label>Interest Rate (% per annum)</label>
                    <input type="number" name="interestRate" step="0.01" placeholder="8.5" oninput="calculateEMI()">
                    <small style="color: var(--text-muted)">Leave empty for 0% interest</small>
                </div>

                <!-- Bank Loan Specific -->
                <div id="bankFields">
                    <div class="form-group">
                        <label>Tenure (months)</label>
                        <input type="number" name="tenureMonths" placeholder="240" oninput="calculateEMI()">
                    </div>

                    <div class="form-group">
                        <label>Monthly EMI (Auto-calculated)</label>
                        <input type="number" name="emiAmount" id="emiAmount" class="calculated-field" readonly>
                    </div>

                    <div class="form-group">
                        <label>EMI Deduction Date (Day of month)</label>
                        <input type="number" name="emiDay" min="1" max="31" placeholder="10">
                    </div>

                    <div class="form-group">
                        <label>EMI Deduction Account</label>
                        <select name="emiAccountId" id="emiAccountSelect">
                            <option value="">Select account</option>
                        </select>
                    </div>
                </div>

                <!-- Personal Loan Specific -->
                <div id="personalFields" style="display: none;">
                    <div id="interestFreqFields" style="display: none;">
                        <div class="form-group">
                            <label>Interest Payment Frequency</label>
                            <select name="interestFrequency">
                                <option value="NONE">None (Principal Only)</option>
                                <option value="MONTHLY">Monthly</option>
                                <option value="WEEKLY">Weekly</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Payment Date (Day)</label>
                            <input type="number" name="interestPaymentDate" min="1" max="31"
                                placeholder="Day of month or 1-7 for week">
                            <small style="color: var(--text-muted)">1-31 for Monthly, 1-7 for Weekly</small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Deposit to Account (Loan Amount)</label>
                    <select name="accountId" id="accountSelect" required>
                        <option value="">Select account</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Loan Start Date</label>
                    <input type="date" name="startDate" id="loanDate" required>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-plus"></i> Add Loan
                </button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/add-loan.js') }}"></script>
@endsection