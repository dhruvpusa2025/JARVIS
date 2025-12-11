@extends('layouts.app')

@section('title', 'Add Lending - JARVIS')

@section('header_title', 'Add Lending')

@section('header_action')
    <a href="{{ route('lendings.index') }}" class="btn-link">View Lendings</a>
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
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            background: var(--bg-tertiary);
            border: 2px solid transparent;
            border-radius: var(--radius-md);
            color: var(--text-primary);
            font-size: 1rem;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-green);
        }

        .interest-fields {
            display: none;
        }

        .interest-fields.active {
            display: block;
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
            <h2 style="margin-bottom: 1.5rem;">New Lending</h2>
            <form id="lendingForm" onsubmit="handleAddLending(event)">
                <div class="form-group">
                    <label>Borrower Name</label>
                    <input type="text" name="borrower" placeholder="e.g., Rajesh, Amit" required>
                </div>

                <div class="form-group">
                    <label>Amount</label>
                    <input type="number" name="amount" step="0.01" placeholder="50000" required>
                </div>

                <div class="form-group">
                    <label>Lend From Account</label>
                    <select name="accountId" id="accountSelect" required>
                        <option value="">Select account</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Interest Type</label>
                    <select name="hasInterest" required onchange="toggleInterestFields(this.value)">
                        <option value="no">No Interest (Temporary/Friendly Loan)</option>
                        <option value="yes">With Interest</option>
                    </select>
                </div>

                <div id="interestFields" class="interest-fields">
                    <div class="form-group">
                        <label>Interest Rate (%)</label>
                        <input type="number" name="interestRate" step="0.01" placeholder="2.00">
                    </div>

                    <div class="form-group">
                        <label>Interest Frequency</label>
                        <select name="frequency">
                            <option value="monthly">Monthly</option>
                            <option value="quarterly">Quarterly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Principal Repayment</label>
                        <select name="repaymentType">
                            <option value="lump_sum">Lump Sum (At end)</option>
                            <option value="monthly">Monthly Installments</option>
                            <option value="custom">Custom Schedule</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Expected Return Date</label>
                    <input type="date" name="returnDate">
                </div>

                <div class="form-group">
                    <label>Start Date</label>
                    <input type="date" name="startDate" id="lendingDate" required>
                </div>

                <div class="form-group">
                    <label>Notes (Optional)</label>
                    <textarea name="notes" rows="3" placeholder="Purpose, terms, etc."></textarea>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-plus"></i> Add Lending
                </button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/add-lending.js') }}"></script>
@endsection