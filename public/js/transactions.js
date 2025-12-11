let allTransactions = [];

document.addEventListener('DOMContentLoaded', async function () {
    await JARVIS.init();
    loadTransactions();
    loadCategoryFilter();
});

function loadTransactions() {
    allTransactions = JARVIS.get('transactions') || [];
    filterTransactions();
}

function loadCategoryFilter() {
    const categories = JARVIS.get('categories') || [];
    const select = document.getElementById('filterCategory');
    categories.forEach(cat => {
        const option = document.createElement('option');
        option.value = cat.name;
        option.textContent = cat.name;
        select.appendChild(option);
    });
}

function filterTransactions() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const type = document.getElementById('filterType').value;
    const category = document.getElementById('filterCategory').value;
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;

    let filtered = allTransactions;

    // Search
    if (search) {
        filtered = filtered.filter(t =>
            (t.description && t.description.toLowerCase().includes(search)) ||
            (t.category && (t.category.name || t.category).toLowerCase().includes(search)) ||
            (t.account && (t.account.name || t.account).toLowerCase().includes(search))
        );
    }

    // Type
    if (type !== 'all') {
        filtered = filtered.filter(t => t.type === type);
    }

    // Category
    if (category !== 'all') {
        filtered = filtered.filter(t => (t.category.name || t.category) === category);
    }

    // Date Range
    if (startDate) {
        filtered = filtered.filter(t => t.date >= startDate);
    }
    if (endDate) {
        filtered = filtered.filter(t => t.date <= endDate);
    }

    displayTransactions(filtered);
}

function displayTransactions(transactions) {
    const container = document.getElementById('transactionsList');
    document.getElementById('transactionCount').textContent = `${transactions.length} transaction${transactions.length !== 1 ? 's' : ''}`;

    if (transactions.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: var(--text-muted); padding: 2rem;">No transactions found</p>';
        return;
    }

    // Sort by date (newest first)
    const sorted = transactions.sort((a, b) => new Date(b.date) - new Date(a.date));

    container.innerHTML = sorted.map(t => {
        let iconClass = 'arrow-right';
        let colorClass = '';
        let sign = '';

        if (t.type === 'income') {
            iconClass = 'arrow-down';
            colorClass = 'income';
            sign = '+';
        } else if (t.type === 'expense') {
            iconClass = 'arrow-up';
            colorClass = 'expense';
            sign = '-';
        } else if (t.type === 'transfer') {
            iconClass = 'exchange-alt';
            colorClass = ''; // Neutral
            sign = '';
        }

        const categoryName = t.category ? (t.category.name || t.category) : 'Uncategorized';
        const accountName = t.account ? (t.account.name || t.account) : 'Unassigned'; // Fallback for safety

        return `
            <div class="transaction-item">
                <div class="transaction-icon ${colorClass}" style="${t.type === 'transfer' ? 'background: rgba(59, 130, 246, 0.2); color: #3b82f6;' : ''}">
                    <i class="fas fa-${iconClass}"></i>
                </div>
                <div class="transaction-details">
                    <div class="transaction-title">${t.description || categoryName}</div>
                    <div class="transaction-category">${categoryName} • ${accountName} ${t.toAccount ? '→ ' + t.toAccount : ''} • ${JARVIS.formatDate(t.date)}</div>
                </div>
                <div class="transaction-amount ${colorClass}">
                    ${sign}${JARVIS.formatCurrency(t.amount)}
                </div>
            </div>
        `;
    }).join('');
}
