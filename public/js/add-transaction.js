// Add Transaction JavaScript
let currentType = 'expense';

document.addEventListener('DOMContentLoaded', function () {
    // Set today's date
    document.getElementById('transactionDate').valueAsDate = new Date();

    // Load categories and accounts
    loadCategories();
    loadPaymentMethods();
});

function setTransactionType(type) {
    currentType = type;
    document.getElementById('transactionType').value = type;

    // Update button states
    document.querySelectorAll('.type-btn').forEach(btn => {
        if (btn.dataset.type === type) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });

    // Reload categories for the selected type
    loadCategories();
}

function loadCategories() {
    const categories = JARVIS.get('categories') || [];
    const filtered = categories.filter(c => c.type === currentType);
    const container = document.getElementById('categoryGrid');

    container.innerHTML = filtered.map(cat => `
        <div class="category-item" onclick="selectCategory('${cat.name}', this)">
            <i class="fas ${cat.icon}" style="color: ${cat.color}"></i>
            <div class="category-name">${cat.name}</div>
        </div>
    `).join('');
}

function selectCategory(category, element) {
    // Remove previous selection
    document.querySelectorAll('.category-item').forEach(item => {
        item.classList.remove('selected');
    });

    // Add selection to clicked item
    element.classList.add('selected');
    document.getElementById('selectedCategory').value = category;
}

function loadPaymentMethods() {
    const accounts = JARVIS.get('accounts') || [];
    const container = document.getElementById('paymentMethods');

    if (accounts.length === 0) {
        container.innerHTML = '<p style="color: var(--text-muted); text-align: center; grid-column: 1/-1;">No accounts found. Please add an account first.</p>';
        return;
    }

    container.innerHTML = accounts.map(acc => `
        <div class="payment-method" onclick="selectPaymentMethod('${acc.name}', this)">
            <i class="fas fa-${getAccountIcon(acc.type)}"></i>
            <div class="method-name">${acc.name}</div>
            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">
                ${JARVIS.formatCurrency(acc.balance)}
            </div>
        </div>
    `).join('');
}

function getAccountIcon(type) {
    const icons = {
        'bank': 'university',
        'cash': 'money-bill-wave',
        'credit_card': 'credit-card'
    };
    return icons[type] || 'wallet';
}

function selectPaymentMethod(accountName, element) {
    // Remove previous selection
    document.querySelectorAll('.payment-method').forEach(item => {
        item.classList.remove('selected');
    });

    // Add selection to clicked item
    element.classList.add('selected');
    document.getElementById('selectedAccount').value = accountName;
}

function handleAddTransaction(event) {
    event.preventDefault();

    const formData = new FormData(event.target);
    const transaction = {
        date: formData.get('date'),
        type: formData.get('type'),
        category: formData.get('category'),
        amount: parseFloat(formData.get('amount')),
        account: formData.get('account'),
        description: formData.get('description') || ''
    };

    // Validate
    if (!transaction.category) {
        showNotification('Please select a category', 'error');
        return;
    }

    if (!transaction.account) {
        showNotification('Please select a payment method', 'error');
        return;
    }

    // Add transaction
    JARVIS.add('transactions', transaction);

    // Update account balance
    updateAccountBalance(transaction);

    // Show success and redirect
    showNotification('Transaction added successfully!', 'success');
    setTimeout(() => {
        window.location.href = 'index.html';
    }, 1000);
}

function updateAccountBalance(transaction) {
    const accounts = JARVIS.get('accounts') || [];
    const account = accounts.find(a => a.name === transaction.account);

    if (account) {
        if (transaction.type === 'income') {
            account.balance += transaction.amount;
        } else {
            account.balance -= transaction.amount;
        }

        JARVIS.update('accounts', account.id, { balance: account.balance });
    }
}
