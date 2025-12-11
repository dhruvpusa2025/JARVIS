@extends('layouts.app')

@section('title', 'Loans - JARVIS')

@section('header_title', 'Loans')

@section('header_action')
    <a href="{{ route('loans.create') }}" class="btn-primary">
        <i class="fas fa-plus"></i> Add Loan
    </a>
@endsection

@section('styles')
    <style>
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

        .modal.active {
            display: flex;
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

        .close-modal {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1.5rem;
            cursor: pointer;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .form-control,
        .styled-select,
        .readonly-input {
            width: 100%;
            padding: 0.75rem;
            background: var(--bg-tertiary);
            border: 1px solid transparent;
            border-radius: var(--radius-md);
            color: var(--text-primary);
            font-size: 1rem;
        }

        .readonly-input {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .input-group {
            position: relative;
        }

        .input-prefix {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }

        .input-group input {
            padding-left: 2rem;
        }

        .modal-footer {
            padding: 1.5rem;
            border-top: 1px solid var(--bg-tertiary);
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <!-- Summary -->
        <div class="summary-grid">
            <div class="summary-card expense">
                <div class="card-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                <div class="card-content">
                    <p class="card-label">Total Outstanding</p>
                    <h2 class="card-value" id="totalOutstanding">₹0</h2>
                </div>
            </div>
            <div class="summary-card balance">
                <div class="card-icon"><i class="fas fa-calendar-alt"></i></div>
                <div class="card-content">
                    <p class="card-label">Monthly EMI</p>
                    <h2 class="card-value" id="totalEMI">₹0</h2>
                </div>
            </div>
            <div class="summary-card income">
                <div class="card-icon"><i class="fas fa-percent"></i></div>
                <div class="card-content">
                    <p class="card-label">Avg Interest Rate</p>
                    <h2 class="card-value" id="avgInterest">0%</h2>
                </div>
            </div>
        </div>

        <!-- Loans List -->
        <div id="loansList"></div>
    </div>

    <!-- Pay EMI Modal -->
    <div class="modal" id="payEMIModal">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h2>Pay EMI</h2>
                <button class="close-modal" onclick="closePayEMIModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form onsubmit="handlePayEMI(event)">
                    <input type="hidden" id="emiLoanId">
                    <div class="form-group">
                        <label>Loan</label>
                        <input type="text" id="emiLoanName" readonly class="readonly-input">
                    </div>
                    <div class="form-group">
                        <label>EMI Amount</label>
                        <div class="input-group">
                            <span class="input-prefix">₹</span>
                            <input type="number" id="emiAmount" required step="0.01" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Pay From</label>
                        <select id="emiAccount" required class="styled-select"></select>
                    </div>
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" id="emiDate" required class="form-control">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" onclick="closePayEMIModal()">Cancel</button>
                        <button type="submit" class="btn-primary">Pay EMI</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Extra Payment Modal -->
    <div class="modal" id="extraPaymentModal">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h2>Extra Payment</h2>
                <button class="close-modal" onclick="closeExtraPaymentModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form onsubmit="handleExtraPayment(event)">
                    <input type="hidden" id="extraLoanId">
                    <div class="form-group">
                        <label>Loan</label>
                        <input type="text" id="extraLoanName" readonly class="readonly-input">
                    </div>
                    <div class="form-group">
                        <label>Amount</label>
                        <div class="input-group">
                            <span class="input-prefix">₹</span>
                            <input type="number" id="extraAmount" required step="0.01" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Pay From</label>
                        <select id="extraAccount" required class="styled-select"></select>
                    </div>
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" id="extraDate" required class="form-control">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" onclick="closeExtraPaymentModal()">Cancel</button>
                        <button type="submit" class="btn-primary">Make Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/loans.js') }}"></script>
@endsection