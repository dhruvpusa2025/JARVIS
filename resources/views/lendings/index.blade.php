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
@endsection

@section('scripts')
    <script src="{{ asset('js/lendings.js') }}"></script>
@endsection