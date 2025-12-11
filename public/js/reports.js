document.addEventListener('DOMContentLoaded', async function () {
    await JARVIS.init();
    populateMonthSelector();
    loadReports();
});

function populateMonthSelector() {
    const select = document.getElementById('reportMonth');
    const now = new Date();

    for (let i = 0; i < 12; i++) {
        const date = new Date(now.getFullYear(), now.getMonth() - i, 1);
        const option = document.createElement('option');
        option.value = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
        option.textContent = date.toLocaleDateString('en-IN', { month: 'long', year: 'numeric' });
        if (i === 0) option.selected = true;
        select.appendChild(option);
    }
}

function loadReports() {
    calculateNetWorth();
    calculateMonthlySummary();
    displayCategoryBreakdown();
}

function calculateNetWorth() {
    const accounts = JARVIS.get('accounts') || [];
    const investments = JARVIS.get('investments') || [];
    const loans = JARVIS.get('loans') || [];

    const liquidAssets = accounts.reduce((sum, a) => sum + parseFloat(a.balance), 0);
    const investmentAssets = investments.reduce((sum, i) => sum + parseFloat(i.current_value || i.amount || 0), 0);
    const liabilities = loans.reduce((sum, l) => sum + parseFloat(l.outstanding_amount || l.outstanding), 0);
    const netWorth = liquidAssets + investmentAssets - liabilities;

    document.getElementById('netWorth').textContent = JARVIS.formatCurrency(netWorth);
    document.getElementById('liquidAssets').textContent = JARVIS.formatCurrency(liquidAssets);
    document.getElementById('investmentAssets').textContent = JARVIS.formatCurrency(investmentAssets);
    document.getElementById('totalLiabilities').textContent = JARVIS.formatCurrency(liabilities);
}

function calculateMonthlySummary() {
    const income = JARVIS.getMonthlyIncome();
    const expenses = JARVIS.getMonthlyExpenses();
    const savings = income - expenses;
    const savingsRate = income > 0 ? ((savings / income) * 100).toFixed(1) : 0;

    document.getElementById('monthlyIncome').textContent = JARVIS.formatCurrency(income);
    document.getElementById('monthlyExpenses').textContent = JARVIS.formatCurrency(expenses);
    document.getElementById('monthlySavings').textContent = JARVIS.formatCurrency(savings);
    document.getElementById('savingsRate').textContent = savingsRate + '%';
}

function displayCategoryBreakdown() {
    const breakdown = JARVIS.getExpenseBreakdown();
    const container = document.getElementById('categoryBreakdown');
    const total = Object.values(breakdown).reduce((sum, val) => sum + val, 0);

    if (total === 0) {
        container.innerHTML = '<p style="text-align: center; color: var(--text-muted); padding: 2rem;">No expenses this month</p>';
        return;
    }

    const colors = ['#10b981', '#f59e0b', '#3b82f6', '#8b5cf6', '#ef4444', '#ec4899'];
    let colorIndex = 0;

    container.innerHTML = Object.entries(breakdown)
        .sort((a, b) => b[1] - a[1])
        .map(([category, amount]) => {
            const percentage = ((amount / total) * 100).toFixed(1);
            const color = colors[colorIndex++ % colors.length];

            return `
                <div style="margin-bottom: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="font-weight: 500;">${category}</span>
                        <span style="font-weight: 600;">${JARVIS.formatCurrency(amount)} (${percentage}%)</span>
                    </div>
                    <div style="background: var(--bg-tertiary); height: 8px; border-radius: 4px; overflow: hidden;">
                        <div style="background: ${color}; height: 100%; width: ${percentage}%; transition: width 0.3s ease;"></div>
                    </div>
                </div>
            `;
        }).join('');
}

function exportReport() {
    showNotification('PDF export feature coming soon!', 'info');
}
