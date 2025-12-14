@extends('layouts.app')

@section('title', 'Investment Accounts')

@section('content')
    <div class="row mb-4">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <h2>Investment Accounts</h2>
            <button class="btn btn-premium" data-bs-toggle="modal" data-bs-target="#addAccountModal">
                <i class="fas fa-plus"></i> Add Account
            </button>
        </div>
    </div>

    <div class="row" id="investment-accounts-list">
        <!-- Accounts will be loaded here -->
        <div class="col-12 text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

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
@endsection

@section('scripts')
    <script src="{{ asset('js/investment-accounts.js') }}"></script>
@endsection