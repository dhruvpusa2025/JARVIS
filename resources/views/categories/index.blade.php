@extends('layouts.app')

@section('title', 'Categories - JARVIS')

@section('header_title', 'Manage Categories')

@section('header_action')
    <button class="btn-primary" onclick="openAddCategoryModal()">
        <i class="fas fa-plus"></i> Add Category
    </button>
@endsection

@section('styles')
    <style>
        .category-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: var(--bg-tertiary);
            border-radius: var(--radius-md);
            margin-bottom: 0.75rem;
        }

        .category-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .category-info {
            flex: 1;
        }

        .icon-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(40px, 1fr));
            gap: 0.5rem;
            max-height: 150px;
            overflow-y: auto;
            padding: 0.5rem;
            background: var(--bg-primary);
            border-radius: var(--radius-md);
        }

        .icon-option {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            cursor: pointer;
            background: var(--bg-tertiary);
            color: var(--text-muted);
            transition: all 0.2s;
        }

        .icon-option:hover {
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        .icon-option.selected {
            background: var(--primary-green);
            color: white;
        }

        .btn-icon-small {
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: var(--radius-sm);
        }

        .btn-icon-small:hover {
            background: var(--danger);
            color: white;
        }

        /* Reuse Modal Styles */
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
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            padding: 1.5rem;
            border-top: 1px solid var(--bg-tertiary);
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .styled-select,
        .form-control,
        input[type="text"],
        input[type="color"] {
            width: 100%;
            padding: 0.75rem;
            background: var(--bg-tertiary);
            border: 1px solid transparent;
            border-radius: var(--radius-md);
            color: var(--text-primary);
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3>Income Categories</h3>
            </div>
            <div class="card-body" id="incomeCategories"></div>
        </div>

        <div class="card" style="margin-top: 1.5rem;">
            <div class="card-header">
                <h3>Expense Categories</h3>
            </div>
            <div class="card-body" id="expenseCategories"></div>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div id="addCategoryModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add Category</h2>
            </div>
            <form onsubmit="handleAddCategory(event)">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Type</label>
                        <select name="type" required class="styled-select">
                            <option value="income">Income</option>
                            <option value="expense">Expense</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" placeholder="e.g., Freelance, Rent" required>
                    </div>
                    <div class="form-group">
                        <label>Icon</label>
                        <input type="hidden" name="icon" id="selectedIcon" required>
                        <div class="icon-grid" id="iconGrid">
                            <!-- Populated by JS -->
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Color</label>
                        <input type="color" name="color" value="#10b981" required style="height: 50px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeAddCategoryModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Add Category</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/categories.js') }}"></script>
@endsection