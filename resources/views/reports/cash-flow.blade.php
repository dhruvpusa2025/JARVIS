@extends('layouts.app')

@section('title', 'Monthly Cash Flow')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="section-title">Monthly Cash Flow & Fix Expenses</h2>
            <p class="text-muted">Overview of your fixed monthly Inflow vs Outflow</p>
        </div>
    </div>

    <!-- Dashboard Widgets -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="summary-card income">
                <div class="card-icon"><i class="fas fa-arrow-down"></i></div>
                <div class="card-content">
                    <div class="card-label">Total Monthly Inflow</div>
                    <div class="card-value" id="totalInflow">₹0.00</div>
                    <div class="card-subtitle">Salary + Lending Interest</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-card expense">
                <div class="card-icon"><i class="fas fa-arrow-up"></i></div>
                <div class="card-content">
                    <div class="card-label">Total Monthly Outflow</div>
                    <div class="card-value" id="totalOutflow">₹0.00</div>
                    <div class="card-subtitle">SIPs + EMIs + Interest Pay</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-card balance">
                <div class="card-icon"><i class="fas fa-wallet"></i></div>
                <div class="card-content">
                    <div class="card-label">Net Assignable Cash</div>
                    <div class="card-value" id="netCashFlow">₹0.00</div>
                    <div class="card-subtitle">Available for other expenses</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- INFLOW COLUMN -->
        <div class="col-md-6">
            <div class="card mb-4" style="height: 100%;">
                <div class="card-header">
                    <h3><i class="fas fa-money-bill-wave text-success me-2"></i> Fixed Inflow</h3>
                    <button class="btn btn-sm btn-primary" onclick="openAddIncomeModal()">
                        <i class="fas fa-plus"></i> Add
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Source</th>
                                    <th>Day</th>
                                    <th class="text-end">Amount</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody id="inflowList">
                                <!-- Populated by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- OUTFLOW COLUMN -->
        <div class="col-md-6">
            <div class="card mb-4" style="height: 100%;">
                <div class="card-header">
                    <h3><i class="fas fa-file-invoice-dollar text-danger me-2"></i> Fixed Outflow</h3>
                    <!-- No Add button here, as sources are from other modules -->
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Name</th>
                                    <th>Day</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody id="outflowList">
                                <!-- Populated by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Income Modal -->
    <div class="modal fade" id="addIncomeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Recurring Income</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addIncomeForm">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" placeholder="e.g. Salary, House Rent"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <input type="number" step="0.01" class="form-control" name="amount" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <select class="form-select" name="type">
                                <option value="SALARY">Salary</option>
                                <option value="BUSINESS">Business</option>
                                <option value="RENT">Rent</option>
                                <option value="OTHER">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Credit Date (Day of Month)</label>
                            <select class="form-select" name="day_of_month">
                                @for ($i = 1; $i <= 31; $i++)
                                    <option value="{{ $i }}">{{ $i }}st/th</option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Credit To Account (Optional)</label>
                            <select class="form-select" name="account_id" id="accountSelect">
                                <option value="">-- None --</option>
                                <!-- Populated by JS -->
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/cash-flow.js') }}"></script>
@endsection