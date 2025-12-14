@extends('layouts.app')

@section('title', $investment->name . ' - Investment Details')

@section('header_title', $investment->name)

@section('header_action')
    <a href="{{ route('investments.index') }}" class="btn-link">
        <i class="fas fa-arrow-left"></i> Back to Investments
    </a>
@endsection

@section('styles')
    <style>
        .details-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }

        .detail-card {
            background: var(--bg-secondary);
            border-radius: var(--radius-lg);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .stat-row {
            display: flex;
            justify-content: space-between;
            padding: 1rem 0;
            border-bottom: 1px solid var(--bg-tertiary);
        }

        .stat-row:last-child {
            border-bottom: none;
        }

        .stat-label {
            color: var(--text-muted);
        }

        .stat-value {
            font-weight: 600;
            color: var(--text-primary);
        }

        .sip-active {
            color: var(--success);
            font-weight: bold;
        }

        .sip-stopped {
            color: var(--danger);
            font-weight: bold;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="details-grid">
            <!-- Main Details -->
            <div class="main-info">
                <div class="detail-card">
                    <h3>Investment Overview</h3>
                    <div class="stat-row">
                        <span class="stat-label">Investment Type</span>
                        <span class="stat-value">{{ strtoupper(str_replace('_', ' ', $investment->type)) }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Linked Account</span>
                        <span class="stat-value">
                            @if($investment->account)
                                <a href="{{ route('investment-accounts.index') }}">{{ $investment->account->name }}</a>
                                <span class="badge bg-secondary">{{ $investment->account->broker }}</span>
                            @else
                                <span class="text-muted">Not linked to any account</span>
                            @endif
                        </span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Invested Amount</span>
                        <span class="stat-value">₹{{ number_format($investment->invested_amount, 2) }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Current Value</span>
                        <span class="stat-value">₹{{ number_format($investment->current_value, 2) }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Units / Quantity</span>
                        <span class="stat-value">{{ $investment->units ?? '-' }}</span>
                    </div>
                </div>

                @if($investment->is_sip)
                    <div class="detail-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3>SIP Configuration</h3>
                            <span class="badge {{ $investment->sip_status === 'ACTIVE' ? 'bg-success' : 'bg-danger' }}">
                                {{ $investment->sip_status }}
                            </span>
                        </div>

                        <div class="stat-row">
                            <span class="stat-label">SIP Amount</span>
                            <span class="stat-value">₹{{ number_format($investment->sip_amount, 2) }}</span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">SIP Date</span>
                            <span class="stat-value">Day {{ $investment->sip_date }} of every month</span>
                        </div>

                        <div class="mt-4">
                            @if($investment->sip_status === 'ACTIVE')
                                <button class="btn btn-danger w-100" onclick="toggleSip({{ $investment->id }}, 'STOPPED')">
                                    <i class="fas fa-stop-circle"></i> Stop SIP
                                </button>
                            @else
                                <button class="btn btn-success w-100" onclick="toggleSip({{ $investment->id }}, 'ACTIVE')">
                                    <i class="fas fa-play-circle"></i> Restart SIP
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar / Actions -->
            <div class="actions">
                <div class="detail-card">
                    <h3>Quick Actions</h3>
                    <button class="btn btn-primary w-100 mb-2" onclick="openEditModal({{ $investment->id }})">
                        <i class="fas fa-edit"></i> Edit Value
                    </button>
                    <button class="btn btn-danger w-100" onclick="deleteInvestment({{ $investment->id }})">
                        <i class="fas fa-trash"></i> Delete Asset
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        async function toggleSip(id, status) {
            if (!confirm(`Are you sure you want to ${status === 'ACTIVE' ? 'restart' : 'stop'} this SIP?`)) return;

            try {
                await JARVIS.request('PUT', `/api/investments/${id}`, {
                    sip_status: status
                });
                window.location.reload();
            } catch (e) {
                console.error(e);
                alert('Failed to update SIP status');
            }
        }

        async function deleteInvestment(id) {
            if (!confirm('Are you sure? This cannot be undone.')) return;
            try {
                await JARVIS.request('DELETE', `/api/investments/${id}`);
                window.location.href = "{{ route('investments.index') }}";
            } catch (e) {
                console.error(e);
                alert('Failed to delete investment');
            }
        }
    </script>
@endsection