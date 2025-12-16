@extends('layouts.app')

@section('title', 'Investment Accounts')

@section('content')
    <div class="row mb-4">
        <div class="col-md-12">

            <!-- Tabs -->
            <ul class="nav nav-pills" id="pills-tab" role="tablist"
                style="background: var(--bg-secondary); padding: 0.5rem; border-radius: var(--radius-md); display: inline-flex;">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pills-accounts-tab" data-account-tab onclick="switchTab('accounts')"
                        type="button" role="tab">Investment Accounts</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-sip-tab" data-sip-tab onclick="switchTab('sips')" type="button"
                        role="tab">SIPs</button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="tab-content" id="pills-tabContent">

        <!-- Accounts Tab -->
        <div class="tab-pane fade show active" id="pills-accounts" role="tabpanel">
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-premium" data-bs-toggle="modal" data-bs-target="#addAccountModal">
                    <i class="fas fa-plus"></i> Add Account
                </button>
            </div>

            <div class="row" id="investment-accounts-list">
                <!-- Accounts will be loaded here -->
                <div class="col-12 text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- SIP Tab -->
        <div class="tab-pane fade" id="pills-sip" role="tabpanel">
            <div class="table-responsive">
                <table class="table" style="width: 100%; border-collapse: separate; border-spacing: 0 0.5rem;">
                    <thead>
                        <tr style="color: var(--text-muted); font-size: 0.875rem; text-align: left;">
                            <th style="padding: 1rem;">Name</th>
                            <th style="padding: 1rem;">Deduct From</th>
                            <th style="padding: 1rem;">Amount</th>
                            <th style="padding: 1rem;">Date</th>
                            <th style="padding: 1rem;">Status</th>
                            <th style="padding: 1rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sipList">
                        <!-- SIPs Populated by JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modals (Add Account / Upload) - Kept same structure, added SIP edit modal -->

    <!-- Add Account Modal -->
    <div class="modal fade" id="addAccountModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Investment Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="add-account-form">
                        <div class="mb-3">
                            <label class="form-label">Account Name</label>
                            <input type="text" class="form-control" name="name" placeholder="e.g. My Zerodha" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Broker</label>
                            <select class="form-select" name="broker" required>
                                <option value="ZERODHA">Zerodha</option>
                                <option value="COIN">Coin (Zerodha)</option>
                                <option value="GROWW">Groww</option>
                                <option value="KITE">Kite</option>
                                <option value="OTHER">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Account Number / ID (Optional)</label>
                            <input type="text" class="form-control" name="account_number">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Create Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Holdings Modal -->
    <div class="modal fade" id="uploadHoldingsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Holdings (Zerodha XLSX)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="upload-holdings-form">
                        <input type="hidden" name="account_id" id="upload-account-id">
                        <div class="mb-3">
                            <label class="form-label">Select File (.xlsx)</label>
                            <input type="file" class="form-control" name="file" accept=".xlsx,.xls,.csv" required>
                            <div class="form-text">Ensure the file has columns: Symbol, ISIN, Quantity Available, Average
                                Price, etc.</div>
                        </div>
                        <div id="upload-status" class="alert alert-info d-none">
                            Uploading... please wait.
                        </div>
                        <button type="submit" class="btn btn-success w-100">Upload & Sync</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit SIP Modal -->
    <div class="modal" id="editSipModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit SIP</h3>
                <button class="close-btn" onclick="closeEditSipModal()"><i class="fas fa-times"></i></button>
            </div>
            <form id="editSipForm" onsubmit="handleEditSipSubmit(event)">
                <input type="hidden" id="editSipId">
                <div class="form-group">
                    <label>Investment Name</label>
                    <input type="text" id="editSipName" readonly style="background-color: var(--bg-tertiary);">
                </div>

                <div class="form-group">
                    <label>SIP Amount</label>
                    <input type="number" step="0.01" id="editSipAmount" required>
                </div>

                <div class="form-group">
                    <label>Debit Date</label>
                    <select id="editSipDate" required>
                        @for ($i = 1; $i <= 28; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group">
                    <label>Deduct from Account</label>
                    <select id="editSipAccount" required>
                        <!-- Populated by JS -->
                    </select>
                </div>

                <button type="submit" class="submit-btn" style="width: 100%; margin-top: 1rem;">Update SIP</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/investment-accounts.js') }}"></script>
@endsection