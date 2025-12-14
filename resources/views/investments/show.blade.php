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

                    @if($investment->sourceAccount)
                        <div class="stat-row">
                            <span class="stat-label">Deduct From</span>
                            <span class="stat-value">{{ $investment->sourceAccount->name }}</span>
                        </div>
                    @endif
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

                    @if($investment->is_sip && $investment->source_account_id)
                        <button class="btn btn-success w-100 mb-2" onclick="openInstallmentModal()">
                            <i class="fas fa-plus-circle"></i> Record Installment
                        </button>
                        <small class="d-block text-muted mb-3 text-center">
                            Auto-deducts ₹{{ $investment->sip_amount }} from {{ $investment->sourceAccount->name }}
                        </small>
                    @endif

                    <button class="btn btn-primary w-100 mb-2" onclick="openEditModal({{ $investment->id }})">
                        <i class="fas fa-edit"></i> Edit Value
                    </button>
                    <button class="btn btn-danger w-100" onclick="deleteInvestment({{ $investment->id }})">
                        <i class="fas fa-trash"></i> Delete Asset
                    </button>
                </div>
            </div>
        </div>

        <!-- History / Entries -->
        <div class="detail-card mt-4">
            <h3>Transaction History</h3>
            <div class="table-responsive">
                <table class="table w-100">
                    <thead>
                        <tr>
                            <th class="text-muted">Date</th>
                            <th class="text-muted">Type</th>
                            <th class="text-muted">Amount</th>
                            <th class="text-muted">Price</th>
                            <th class="text-muted">Units</th>
                            <th class="text-muted">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($investment->entries as $entry)
                            <tr>
                                <td>{{ $entry->date->format('d M Y') }}</td>
                                <td><span class="badge bg-secondary">{{ str_replace('_', ' ', $entry->type) }}</span></td>
                                <td class="fw-bold">₹{{ number_format($entry->amount, 2) }}</td>
                                <td>{{ $entry->price ? '₹' . number_format($entry->price, 2) : '-' }}</td>
                                <td>{{ $entry->units ? number_format($entry->units, 4) : '-' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-link"
                                        onclick="openEditEntryModal({{ $entry->id }}, {{ $entry->price ?? 0 }}, {{ $entry->units ?? 0 }})">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">No history entries found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Record Installment Modal -->
    <div class="modal" id="installmentModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Record SIP Installment</h3>
                <button class="close-btn" onclick="closeInstallmentModal()"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <p>This will deduct <strong>₹{{ $investment->sip_amount }}</strong> from
                    <strong>{{ $investment->sourceAccount->name ?? 'Bank Account' }}</strong>.</p>
                <form id="installmentForm" onsubmit="handleInstallment(event)">
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}">
                    </div>
                    <!-- Hidden units for now, or allow editing? User said: "Show notification to edit buying price... once user put buying price... update unit" -->
                    <!-- So initially we just record amount. Price/Units is unknown or estimated. -->
                    <p class="text-muted small">Units will be estimated based on current price. You can update the exact
                        NAV/Price later.</p>

                    <button type="submit" class="submit-btn w-100 mt-3">Confirm & Deduct</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Entry Modal -->
    <div class="modal" id="editEntryModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Update Entry</h3>
                <button class="close-btn" onclick="closeEditEntryModal()"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <form id="editEntryForm" onsubmit="handleEditEntry(event)">
                    <input type="hidden" id="entryId">
                    <div class="form-group">
                        <label>Actual Price / NAV</label>
                        <input type="number" step="0.01" name="price" id="entryPrice" required>
                    </div>
                    <div class="form-group">
                        <label>Units Received (Optional)</label>
                        <input type="number" step="0.0001" name="units" id="entryUnits"
                            placeholder="Auto-calculated if empty">
                    </div>
                    <button type="submit" class="submit-btn w-100 mt-3">Update Entry</button>
                </form>
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

        // New Functions
        function openInstallmentModal() {
            document.getElementById('installmentModal').classList.add('active');
        }
        function closeInstallmentModal() {
            document.getElementById('installmentModal').classList.remove('active');
        }

        async function handleInstallment(e) {
            e.preventDefault();
            const date = e.target.date.value;
            const amount = {{ $investment->sip_amount ?? 0 }}; // Use blade value

            try {
                await JARVIS.request('POST', `/api/investments/{{ $investment->id }}/installment`, {
                    amount: amount,
                    date: date
                });
                alert('Installment recorded successfully!');
                window.location.reload();
            } catch (e) {
                console.error(e);
                alert('Failed to record installment');
            }
        }

        function openEditEntryModal(id, price, units) {
            document.getElementById('entryId').value = id;
            document.getElementById('entryPrice').value = price;
            document.getElementById('entryUnits').value = units;
            document.getElementById('editEntryModal').classList.add('active');
        }
        function closeEditEntryModal() {
            document.getElementById('editEntryModal').classList.remove('active');
        }

        async function handleEditEntry(e) {
            e.preventDefault();
            const id = document.getElementById('entryId').value;
            const price = document.getElementById('entryPrice').value;
            const units = document.getElementById('entryUnits').value;

            try {
                await JARVIS.request('PUT', `/api/investment-entries/${id}`, {
                    price: parseFloat(price),
                    units: units ? parseFloat(units) : null
                });
                window.location.reload();
            } catch (e) {
                console.error(e);
                alert('Failed to update entry');
            }
        }
    </script>
@endsection