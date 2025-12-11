document.addEventListener('DOMContentLoaded', async function () {
    await JARVIS.init();
    loadFormDependencies();
});

const commonCategories = {
    expense: ['Food', 'Transport', 'Shopping', 'Bills', 'Entertainment', 'Health', 'Travel', 'Education', 'Other'],
    income: ['Salary', 'Freelance', 'Investment', 'Refund', 'Gift', 'Other']
};

const commonIcons = {
    'Food': 'fa-utensils',
    'Transport': 'fa-car',
    'Shopping': 'fa-shopping-bag',
    'Bills': 'fa-file-invoice-dollar',
    'Entertainment': 'fa-film',
    'Other': 'fa-circle'
};

function setTransactionType(type) {
    document.querySelectorAll('.type-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelector(`.type-btn[data-type="${type}"]`).classList.add('active');
    document.getElementById('transactionType').value = type;
    loadCategories(type);
}

function loadCategories(type) {
    // In our new system, categories come from DB.
    // If DB is empty, we fall back to static list? No, we should assume DB Categories exist or user creates them.
    // But for "Add Transaction" ease of use, we might want to show all categories filtered by type.
    const categories = JARVIS.get('categories').filter(c => c.type === type);
    const grid = document.getElementById('categoryGrid');

    if (categories.length === 0) {
        grid.innerHTML = '<p style="color:var(--text-muted); grid-column: 1/-1; text-align:center;">No categories found. Please add some in Categories manager.</p>';
        return;
    }

    grid.innerHTML = categories.map(cat => `
        <div class="category-item" onclick="selectCategory(this, '${cat.id}')">
            <i class="fas ${cat.icon || 'fa-circle'}" style="color: ${cat.color}"></i>
            <div class="category-name">${cat.name}</div>
        </div>
    `).join('');
}

function selectCategory(el, id) {
    document.querySelectorAll('.category-item').forEach(item => item.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('selectedCategory').value = id;
}

function loadFormDependencies() {
    // Load Categories
    setTransactionType('expense');

    // Load Accounts
    const accounts = JARVIS.get('accounts') || [];
    const paymentMethodsContainer = document.getElementById('paymentMethods');

    if (accounts.length === 0) {
        paymentMethodsContainer.innerHTML = '<p>No accounts found.</p>';
        return;
    }

    paymentMethodsContainer.innerHTML = accounts.map(acc => `
        <div class="payment-method" onclick="selectAccount(this, ${acc.id})">
            <i class="fas fa-wallet" style="color: var(--primary-green)"></i>
            <div class="method-name">${acc.name}</div>
            <div style="font-size: 0.75rem; color: var(--text-muted);">${JARVIS.formatCurrency(acc.balance)}</div>
        </div>
    `).join('');

    // Set default date
    document.getElementById('transactionDate').valueAsDate = new Date();
}

function selectAccount(el, id) {
    document.querySelectorAll('.payment-method').forEach(item => item.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('selectedAccount').value = id;
}


async function handleAddTransaction(event) {
    event.preventDefault();

    const formData = new FormData(event.target);
    const amount = parseFloat(formData.get('amount'));
    const categoryId = parseInt(formData.get('category'));
    const accountId = parseInt(formData.get('account'));

    if (!amount || !categoryId || !accountId) {
        showNotification('Please fill in all required fields', 'error');
        return;
    }

    const transaction = {
        amount: amount,
        type: formData.get('type'),
        category_id: categoryId,
        account_id: accountId,
        date: formData.get('date'),
        description: formData.get('description')
    };

    try {
        await JARVIS.add('transactions', transaction);
        showNotification('Transaction added successfully!', 'success');
        setTimeout(() => window.location.href = '/transactions', 1000);
    } catch (error) {
        showNotification('Failed to save transaction', 'error');
    }
}
