// Accounts page JavaScript
document.addEventListener('DOMContentLoaded', async function () {
    // Optimization: Don't use JARVIS.init() which loads everything.
    // Just load accounts for this page.
    loadAccountsData();
});

async function loadAccountsData() {
    try {
        // Direct fetch for speed
        const response = await fetch('/api/accounts', {
            headers: { 'Accept': 'application/json' }
        });

        if (!response.ok) throw new Error('Failed to load accounts');

        const accounts = await response.json();

        // Sync with global state so add/delete works
        JARVIS.state.accounts = accounts;

        // Calculate summaries
        const bankAccounts = accounts.filter(a => a.type === 'bank');
        const cashAccounts = accounts.filter(a => a.type === 'cash');
        const creditCards = accounts.filter(a => a.type === 'credit_card');

        // Fix NaN: Use safe parsing (defaults to 0 if null/undefined/NaN)
        const safeBalance = (val) => {
            const num = parseFloat(val);
            return isNaN(num) ? 0 : num;
        };

        const bankTotal = bankAccounts.reduce((sum, a) => sum + safeBalance(a.balance), 0);
        const cashTotal = cashAccounts.reduce((sum, a) => sum + safeBalance(a.balance), 0);
        const creditTotal = creditCards.reduce((sum, a) => sum + safeBalance(a.balance), 0); // Credit cards usually have +ve balance in this app logic? Or is it debt?
        // If credit card balance is debt, it might be stored as positive number but logically negative.
        // Based on previous code: `Math.abs(creditTotal)` for display, and `netBalance = bank + cash + credit`.
        // If credit balance is entered as negative in DB for debt, then this math works.
        // If entered as positive, `netBalance` addition would be wrong if it's debt.
        // However, based on user request "Net Balance shows NaN", preventing NaN is the priority.
        // We will stick to the existing logic but fixing the parsing.

        // Note: Usually credit card balance is LIABILITY. If stored as positive, Net Worth = Assets - Liabilities.
        // But original code was `bank + cash + credit`. Assuming `credit` balance is stored as negative for debt?
        // Let's assume standard behavior: we just fix the NaN.

        const netBalance = bankTotal + cashTotal + creditTotal;

        // Update summary cards
        document.getElementById('bankTotal').textContent = JARVIS.formatCurrency(bankTotal);
        document.getElementById('bankCount').textContent = `${bankAccounts.length} account${bankAccounts.length !== 1 ? 's' : ''}`;
        document.getElementById('cashTotal').textContent = JARVIS.formatCurrency(cashTotal);
        document.getElementById('creditTotal').textContent = JARVIS.formatCurrency(Math.abs(creditTotal));
        document.getElementById('creditCount').textContent = `${creditCards.length} card${creditCards.length !== 1 ? 's' : ''}`;
        document.getElementById('netBalance').textContent = JARVIS.formatCurrency(netBalance);

        // Load accounts list
        loadAccountsList(accounts);
    } catch (error) {
        console.error('Error loading accounts:', error);
        showNotification('Failed to load accounts', 'error');
    }
}

function loadAccountsList(accounts) {
    const container = document.getElementById('accountsList');

    if (accounts.length === 0) {
        container.innerHTML = '<p class="text-center" style="color: var(--text-muted); padding: 2rem;">No accounts yet. Add your first account!</p>';
        return;
    }

    const safeBalance = (val) => {
        const num = parseFloat(val);
        return isNaN(num) ? 0 : num;
    };

    container.innerHTML = accounts.map(account => `
        <div class="account-card">
            <div class="account-info">
                <div class="account-name">
                    <i class="fas fa-${getAccountIcon(account.type)}"></i>
                    ${account.name}
                </div>
                <div class="account-type">
                    ${formatAccountType(account.type)}
                    ${account.account_number ? ' • ' + account.account_number : ''}
                    ${account.type === 'credit_card' ? ' • Limit: ' + JARVIS.formatCurrency(account.credit_limit || 0) : ''}
                </div>
            </div>
            <div style="text-align: right;">
                <div class="account-balance ${safeBalance(account.balance) >= 0 ? 'positive' : 'negative'}">
                    ${JARVIS.formatCurrency(Math.abs(safeBalance(account.balance)))}
                </div>
                <div class="account-actions" style="margin-top: 0.5rem;">
                    <button class="btn-icon-small" onclick="editAccount(${account.id})" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-icon-small" onclick="deleteAccount(${account.id})" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
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

function formatAccountType(type) {
    const types = {
        'bank': 'Bank Account',
        'cash': 'Cash',
        'credit_card': 'Credit Card'
    };
    return types[type] || type;
}

function openAddAccountModal() {
    document.getElementById('addAccountModal').style.display = 'flex';
    document.getElementById('addAccountForm').reset();
}

function closeAddAccountModal() {
    document.getElementById('addAccountModal').style.display = 'none';
}

function toggleAccountFields(type) {
    const bankFields = document.querySelectorAll('.bank-only');
    const creditFields = document.querySelectorAll('.credit-only');

    bankFields.forEach(field => {
        field.style.display = type === 'bank' ? 'block' : 'none';
        const input = field.querySelector('input');
        if (input) input.required = type === 'bank';
    });

    creditFields.forEach(field => {
        field.style.display = type === 'credit_card' ? 'block' : 'none';
        const input = field.querySelector('input');
        if (input) input.required = type === 'credit_card';
    });
}

async function handleAddAccount(event) {
    event.preventDefault();

    const formData = new FormData(event.target);
    const account = {
        name: formData.get('name'),
        type: formData.get('type'),
        balance: parseFloat(formData.get('balance')) || 0,
        account_number: formData.get('account_number') || null,
        credit_limit: parseFloat(formData.get('credit_limit')) || null
    };

    try {
        await JARVIS.add('accounts', account);
        closeAddAccountModal();
        loadAccountsData(); // Reload to refresh list and sums
        showNotification('Account added successfully!', 'success');
    } catch (error) {
        // Notification handled in JARVIS.add
    }
}

function editAccount(id) {
    showNotification('Edit functionality coming soon!', 'info');
}

async function deleteAccount(id) {
    if (confirm('Are you sure you want to delete this account?')) {
        try {
            await JARVIS.delete('accounts', id);
            loadAccountsData();
            showNotification('Account deleted successfully!', 'success');
        } catch (error) {
            // Notification handled
        }
    }
}

function loadAccountsData() {
    const accounts = JARVIS.get('accounts') || [];

    // Calculate summaries
    const bankAccounts = accounts.filter(a => a.type === 'bank');
    const cashAccounts = accounts.filter(a => a.type === 'cash');
    const creditCards = accounts.filter(a => a.type === 'credit_card');

    const bankTotal = bankAccounts.reduce((sum, a) => sum + a.balance, 0);
    const cashTotal = cashAccounts.reduce((sum, a) => sum + a.balance, 0);
    const creditTotal = creditCards.reduce((sum, a) => sum + a.balance, 0);
    const netBalance = bankTotal + cashTotal + creditTotal;

    // Update summary cards
    document.getElementById('bankTotal').textContent = JARVIS.formatCurrency(bankTotal);
    document.getElementById('bankCount').textContent = `${bankAccounts.length} account${bankAccounts.length !== 1 ? 's' : ''}`;
    document.getElementById('cashTotal').textContent = JARVIS.formatCurrency(cashTotal);
    document.getElementById('creditTotal').textContent = JARVIS.formatCurrency(Math.abs(creditTotal));
    document.getElementById('creditCount').textContent = `${creditCards.length} card${creditCards.length !== 1 ? 's' : ''}`;
    document.getElementById('netBalance').textContent = JARVIS.formatCurrency(netBalance);

    // Load accounts list
    loadAccountsList(accounts);
}

function loadAccountsList(accounts) {
    const container = document.getElementById('accountsList');

    if (accounts.length === 0) {
        container.innerHTML = '<p class="text-center" style="color: var(--text-muted); padding: 2rem;">No accounts yet. Add your first account!</p>';
        return;
    }

    container.innerHTML = accounts.map(account => `
        <div class="account-card">
            <div class="account-info">
                <div class="account-name">
                    <i class="fas fa-${getAccountIcon(account.type)}"></i>
                    ${account.name}
                </div>
                <div class="account-type">
                    ${formatAccountType(account.type)}
                    ${account.accountNumber ? ' • ' + account.accountNumber : ''}
                    ${account.type === 'credit_card' ? ' • Limit: ' + JARVIS.formatCurrency(account.creditLimit) : ''}
                </div>
            </div>
            <div style="text-align: right;">
                <div class="account-balance ${account.balance >= 0 ? 'positive' : 'negative'}">
                    ${JARVIS.formatCurrency(Math.abs(account.balance))}
                </div>
                <div class="account-actions" style="margin-top: 0.5rem;">
                    <button class="btn-icon-small" onclick="editAccount(${account.id})" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-icon-small" onclick="deleteAccount(${account.id})" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
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

function formatAccountType(type) {
    const types = {
        'bank': 'Bank Account',
        'cash': 'Cash',
        'credit_card': 'Credit Card'
    };
    return types[type] || type;
}

function openAddAccountModal() {
    document.getElementById('addAccountModal').style.display = 'flex';
    document.getElementById('addAccountForm').reset();
}

function closeAddAccountModal() {
    document.getElementById('addAccountModal').style.display = 'none';
}

function toggleAccountFields(type) {
    const bankFields = document.querySelectorAll('.bank-only');
    const creditFields = document.querySelectorAll('.credit-only');

    bankFields.forEach(field => {
        field.style.display = type === 'bank' ? 'block' : 'none';
        field.querySelector('input').required = type === 'bank';
    });

    creditFields.forEach(field => {
        field.style.display = type === 'credit_card' ? 'block' : 'none';
        field.querySelector('input').required = type === 'credit_card';
    });
}

function handleAddAccount(event) {
    event.preventDefault();

    const formData = new FormData(event.target);
    const account = {
        name: formData.get('name'),
        type: formData.get('type'),
        balance: parseFloat(formData.get('balance')) || 0,
        accountNumber: formData.get('accountNumber') || null,
        creditLimit: parseFloat(formData.get('creditLimit')) || null
    };

    JARVIS.add('accounts', account);
    closeAddAccountModal();
    loadAccountsData();
    showNotification('Account added successfully!', 'success');
}

function editAccount(id) {
    // For now, just show alert. Can implement edit modal later
    showNotification('Edit functionality coming soon!', 'info');
}

function deleteAccount(id) {
    if (confirm('Are you sure you want to delete this account?')) {
        JARVIS.delete('accounts', id);
        loadAccountsData();
        showNotification('Account deleted successfully!', 'success');
    }
}
