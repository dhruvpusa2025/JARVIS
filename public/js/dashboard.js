// Dashboard specific JavaScript
document.addEventListener('DOMContentLoaded', function () {
    // Load dashboard data
    loadDashboardData();

    // Initialize charts
    initializeCharts();
});

function loadDashboardData() {
    // Update summary cards
    document.getElementById('totalBalance').textContent = JARVIS.formatCurrency(JARVIS.getTotalBalance());
    document.getElementById('monthlyIncome').textContent = JARVIS.formatCurrency(JARVIS.getMonthlyIncome());
    document.getElementById('monthlyExpenses').textContent = JARVIS.formatCurrency(JARVIS.getMonthlyExpenses());
    document.getElementById('totalInvestments').textContent = JARVIS.formatCurrency(JARVIS.getTotalInvestments());

    // Load recent transactions
    loadRecentTransactions();
}

function loadRecentTransactions() {
    const container = document.getElementById('recentTransactions');
    const transactions = JARVIS.getRecentTransactions(5);

    if (transactions.length === 0) {
        container.innerHTML = '<p class="text-center" style="color: var(--text-muted);">No transactions yet</p>';
        return;
    }

    container.innerHTML = transactions.map(t => `
        <div class="transaction-item">
            <div class="transaction-icon ${t.type}">
                <i class="fas fa-${t.type === 'income' ? 'arrow-down' : 'arrow-up'}"></i>
            </div>
            <div class="transaction-details">
                <div class="transaction-title">${t.description || t.category}</div>
                <div class="transaction-category">${t.category} • ${JARVIS.formatDate(t.date)}</div>
            </div>
            <div class="transaction-amount ${t.type}">
                ${t.type === 'income' ? '+' : '-'}${JARVIS.formatCurrency(t.amount)}
            </div>
        </div>
    `).join('');
}

function initializeCharts() {
    // Expense Breakdown Chart
    const expenseCtx = document.getElementById('expenseChart');
    if (expenseCtx) {
        const breakdown = JARVIS.getExpenseBreakdown();
        const labels = Object.keys(breakdown);
        const data = Object.values(breakdown);
        const colors = [
            '#10b981', // Green
            '#f59e0b', // Orange
            '#3b82f6', // Blue
            '#8b5cf6', // Purple
            '#ef4444', // Red
            '#ec4899'  // Pink
        ];

        new Chart(expenseCtx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors.slice(0, labels.length),
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#cbd5e1',
                            padding: 15,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const label = context.label || '';
                                const value = JARVIS.formatCurrency(context.parsed);
                                return `${label}: ${value}`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Cash Flow Chart
    const cashFlowCtx = document.getElementById('cashFlowChart');
    if (cashFlowCtx) {
        // Generate last 6 months data
        const months = [];
        const income = [];
        const expenses = [];

        for (let i = 5; i >= 0; i--) {
            const date = new Date();
            date.setMonth(date.getMonth() - i);
            months.push(date.toLocaleDateString('en-IN', { month: 'short' }));

            // Mock data for now (in real app, calculate from transactions)
            income.push(80000 + Math.random() * 10000);
            expenses.push(40000 + Math.random() * 15000);
        }

        new Chart(cashFlowCtx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Income',
                        data: income,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Expenses',
                        data: expenses,
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#cbd5e1',
                            padding: 15,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const label = context.dataset.label || '';
                                const value = JARVIS.formatCurrency(context.parsed.y);
                                return `${label}: ${value}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#94a3b8',
                            callback: function (value) {
                                return '₹' + (value / 1000) + 'k';
                            }
                        },
                        grid: {
                            color: 'rgba(148, 163, 184, 0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#94a3b8'
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
}
