@extends('layouts.app')

@section('title', 'Lendings - JARVIS')

@section('header_title', 'Lendings')

@section('header_action')
    <a href="{{ route('lendings.create') }}" class="btn-primary">
        <i class="fas fa-plus"></i> Add Lending
    </a>
@endsection

@section('styles')
    <style>
        .lending-card {
            background: var(--bg-secondary);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-left: 4px solid var(--primary-green);
        }

        .lending-card.due {
            border-left-color: var(--danger);
            background: linear-gradient(135deg, var(--bg-secondary) 0%, rgba(239, 68, 68, 0.05) 100%);
        }

        .lending-card.completed {
            border-left-color: var(--text-muted);
            opacity: 0.7;
        }

        .lending-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }

        .borrower-name {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .lending-amount {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-green);
        }

        .lending-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin: 1rem 0;
            padding: 1rem 0;
            border-top: 1px solid var(--bg-tertiary);
            border-bottom: 1px solid var(--bg-tertiary);
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-bottom: 0.25rem;
        }

        .detail-value {
            font-weight: 600;
        }

        .lending-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .btn-small {
            padding: 0.5rem 1rem;
            border-radius: var(--radius-md);
            border: none;
            font-weight: 500;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .btn-small:hover {
            transform: scale(1.05);
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-info {
            background: var(--info);
            color: white;
        }

        .due-badge {
            background: var(--danger);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            margin-left: 0.5rem;
            vertical-align: middle;
        }

        /* Modal specific styles */
        .modal-body .form-group {
            margin-bottom: 1.25rem;
        }

        .modal-body label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 0.9rem;
        }

        .modal-body input,
        .modal-body select {
            width: 100%;
            padding: 0.75rem 1rem;
            background: var(--bg-primary);
            border: 1px solid var(--bg-tertiary);
            border-radius: var(--radius-md);
            color: var(--text-primary);
            font-size: 1rem;
            transition: all 0.2s;
        }

        .modal-body input:focus,
        .modal-body select:focus {
            outline: none;
            border-color: var(--primary-green);
            background: var(--bg-tertiary);
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
        }

        .modal-body .submit-btn {
            width: 100%;
            margin-top: 1rem;
            padding: 0.875rem;
            font-size: 1rem;
        }

        .close-btn {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1.25rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 50%;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
        }

        .close-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <!-- Summary -->
        <div class="summary-grid">
            <div class="summary-card balance">
                <div class="card-icon"><i class="fas fa-hand-holding-usd"></i></div>
                <div class="card-content">
                    <p class="card-label">Total Lent</p>
                    <h2 class="card-value" id="totalLent">₹0</h2>
                </div>
            </div>
            <div class="summary-card expense">
                <div class="card-icon"><i class="fas fa-clock"></i></div>
                <div class="card-content">
                    <p class="card-label">Outstanding</p>
                    <h2 class="card-value" id="totalOutstanding">₹0</h2>
                </div>
            </div>
            <div class="summary-card income">
                <div class="card-icon"><i class="fas fa-coins"></i></div>
                <div class="card-content">
                    <p class="card-label">Interest Earned</p>
                    <h2 class="card-value" id="totalInterest">₹0</h2>
                </div>
            </div>
        </div>

        <!-- Lendings List -->
        <div id="lendingsList"></div>
    </div>

    <!-- Receive Interest Modal -->
    <div class="modal" id="receiveInterestModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Receive Interest</h3>
                <button class="close-btn" onclick="closeInterestModal()"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <form id="interestForm" onsubmit="handleInterestSubmit(event)">
                    <input type="hidden" id="interestLendingId">
                    <div class="form-group">
                        <label>Interest Amount</label>
                        <input type="number" id="interestAmount" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Date Received</label>
                        <input type="date" id="interestDate" required>
                    </div>
                    <div class="form-group">
                        <label>Deposit To Account</label>
                        <select id="interestAccount" required></select>
                    </div>
                    <button type="submit" class="submit-btn">Record Interest</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Receive Repayment Modal -->
    <div class="modal" id="receiveRepaymentModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Receive Repayment</h3>
                <button class="close-btn" onclick="closeRepaymentModal()"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <form id="repaymentForm" onsubmit="handleRepaymentSubmit(event)">
                    <input type="hidden" id="repaymentLendingId">
                    <div class="form-group">
                        <label>Repayment Amount</label>
                        <input type="number" id="repaymentAmount" step="0.01" required>
                        <small style="color: var(--text-muted);" id="maxRepaymentHint">Max: ₹0</small>
                    </div>
                    <div class="form-group">
                        <label>Date Received</label>
                        <input type="date" id="repaymentDate" required>
                    </div>
                    <div class="form-group">
                        <label>Deposit To Account</label>
                        <select id="repaymentAccount" required></select>
                    </div>
                    <button type="submit" class="submit-btn">Record Repayment</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/lendings.js') }}"></script>
@endsection