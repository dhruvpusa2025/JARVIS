// Accounts page JavaScript
document.addEventListener('DOMContentLoaded', function () {
    loadAccountsData();
});

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
