@extends('layouts.app')

@section('title', 'Investments - JARVIS')

@section('header_title', 'Investments')

@section('header_action')
    <a href="{{ route('investments.create') }}" class="btn-primary">
        <i class="fas fa-plus"></i> Add Investment
    </a>
@endsection

@section('styles')
    <style>
        .investment-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .investment-card {
            background: var(--gradient-card);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            border-left: 4px solid;
            position: relative;
        }

        .investment-card.mutual_fund {
            border-left-color: #3b82f6;
        }

        .investment-card.stock {
            border-left-color: #8b5cf6;
        }

        .investment-card.fd {
            border-left-color: #f59e0b;
        }

        .investment-card.rd {
            border-left-color: #10b981;
        }

        .investment-card.real_estate {
            border-left-color: #22c55e;
        }

        .investment-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }

        .investment-type {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .investment-name {
            font-size: 1.125rem;
            font-weight: 600;
            margin: 0.25rem 0;
        }

        .investment-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--bg-tertiary);
        }

        .stat {
            display: flex;
            flex-direction: column;
        }

        .stat-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-bottom: 0.25rem;
        }

        .stat-value {
            font-size: 1rem;
            font-weight: 600;
        }

        .profit-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .profit-badge.positive {
            background: rgba(34, 197, 94, 0.2);
            color: var(--success);
        }

        .profit-badge.negative {
            background: rgba(239, 68, 68, 0.2);
            color: var(--danger);
        }

        /* Grouping Cards */
        .group-scroll {
            display: flex;
            gap: 1rem;
            overflow-x: auto;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }

        .group-card {
            min-width: 160px;
            background: var(--bg-secondary);
            padding: 1rem;
            border-radius: var(--radius-md);
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .group-card:hover {
            background: var(--bg-tertiary);
        }

        .group-card.active {
            border-color: var(--primary-green);
            background: rgba(34, 197, 94, 0.1);
        }

        .group-card h3 {
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }

        .group-card .amount {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .card-actions {
            position: absolute;
            top: 1rem;
            right: 1rem;
            display: flex;
            gap: 0.5rem;
        }

        .action-btn {
            background: transparent;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 50%;
            transition: all 0.2s;
        }

        .action-btn:hover {
            background: var(--bg-tertiary);
            color: var(--text-primary);
        }

        .action-btn.sell:hover {
            color: var(--danger);
            background: rgba(239, 68, 68, 0.1);
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <!-- Overall Summary -->
        <div class="summary-grid">
            <div class="summary-card balance">
                <div class="card-icon"><i class="fas fa-wallet"></i></div>
                <div class="card-content">
                    <p class="card-label">Current Value</p>
                    <h2 class="card-value" id="currentValue">₹0</h2>
                </div>
            </div>
            <div class="summary-card income">
                <div class="card-icon"><i class="fas fa-arrow-up"></i></div>
                <div class="card-content">
                    <p class="card-label">Total Returns</p>
                    <h2 class="card-value" id="totalReturns">₹0</h2>
                    <span class="card-trend positive" id="returnPercentage">+0%</span>
                </div>
            </div>
            <div class="summary-card investments">
                <div class="card-icon"><i class="fas fa-piggy-bank"></i></div>
                <div class="card-content">
                    <p class="card-label">Total Invested</p>
                    <h2 class="card-value" id="totalInvested">₹0</h2>
                </div>
            </div>
        </div>

        <!-- Grouping/Filter Cards -->
        <h3 style="margin: 1.5rem 0 1rem;">Portfolio Breakdown</h3>
        <div class="group-scroll" id="groupCards">
            <!-- Populated by JS -->
        </div>

        <div id="investmentsList" class="investment-grid">
            <!-- Populated by JS -->
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal" id="editModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Update Value</h3>
                <button class="close-btn" onclick="closeEditModal()"><i class="fas fa-times"></i></button>
            </div>
            <form id="editForm" onsubmit="handleEditSubmit(event)">
                <input type="hidden" id="editId">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" id="editName" readonly style="background-color: var(--bg-tertiary);">
                </div>
                <div class="form-group">
                    <label>Current Price/Value</label>
                    <input type="number" step="0.01" id="editCurrentPrice" required>
                </div>
                <button type="submit" class="submit-btn" style="width: 100%; margin-top: 1rem;">Update</button>
            </form>
        </div>
    </div>

    <!-- Sell Modal -->
    <div class="modal" id="sellModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Sell Investment</h3>
                <button class="close-btn" onclick="closeSellModal()"><i class="fas fa-times"></i></button>
            </div>
            <form id="sellForm" onsubmit="handleSellSubmit(event)">
                <input type="hidden" id="sellId">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" id="sellName" readonly style="background-color: var(--bg-tertiary);">
                </div>

                <div class="form-group">
                    <label>Units to Sell</label>
                    <div style="display: flex; gap: 0.5rem;">
                        <input type="number" step="0.0001" id="sellUnits" required>
                        <button type="button" class="btn-secondary" onclick="setMaxUnits()"
                            style="padding: 0 0.5rem; font-size: 0.75rem;">Max</button>
                    </div>
                    <small id="availableUnits"
                        style="color: var(--text-muted); display: block; margin-top: 0.25rem;"></small>
                </div>

                <div class="form-group">
                    <label>Selling Price (per unit)</label>
                    <input type="number" step="0.01" id="sellPrice" required>
                </div>

                <div class="form-group">
                    <label>Deposit to Account</label>
                    <select id="sellAccount" required>
                        <!-- Populated by JS -->
                    </select>
                </div>

                <div class="form-group">
                    <label>Date</label>
                    <input type="date" id="sellDate" required>
                </div>

                <button type="submit" class="submit-btn"
                    style="width: 100%; margin-top: 1rem; background: var(--danger);">Sell & Confirm</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/investments.js') }}"></script>
@endsection