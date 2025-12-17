document.addEventListener('DOMContentLoaded', async function () {
    await JARVIS.init();
    loadCashFlowData();
    loadAccountsForDropdown();

    document.getElementById('addIncomeForm').addEventListener('submit', handleAddIncome);
});

async function loadCashFlowData() {
    try {
        // Fetch all necessary data in parallel
        const [incomes, investements, loans, lendings] = await Promise.all([
            JARVIS.request('GET', '/api/recurring-incomes') || [],
            JARVIS.request('GET', '/api/investments') || [],
            JARVIS.request('GET', '/api/loans') || [],
            JARVIS.request('GET', '/api/lendings') || [] // Assuming lending endpoint exists
        ]);

        const inflows = [];
        const outflows = [];

        // 1. Recurring Incomes (Salary, Rent, etc.)
        incomes.forEach(inc => {
            inflows.push({
                type: 'Income',
                category: inc.type,
                name: inc.name,
                day: inc.day_of_month,
                amount: parseFloat(inc.amount),
                is_manual: true,
                id: inc.id // For deletion if needed
            });
        });

        // 2. Lending Interest (Income)
        lendings.forEach(lending => {
            const amount = parseFloat(lending.amount);
            const rate = parseFloat(lending.interest_rate);

            // Calculate Monthly Interest: (Principal * Rate / 100) / 12 (assuming rate is annual)
            // If frequency is different, adjust accordingly. Assuming Annual Rate for now.
            if (amount > 0 && rate > 0) {
                const monthlyInterest = (amount * (rate / 100)) / 12;

                inflows.push({
                    type: 'Lending Interest',
                    category: 'INTEREST',
                    name: `Interest from ${lending.borrower}`,
                    day: 1, // Default to 1st or maybe lending start date day
                    amount: monthlyInterest,
                    is_manual: false
                });
            }
        });

        // 3. SIPs (Expense)
        investements.forEach(inv => {
            if (inv.is_sip && inv.sip_status === 'ACTIVE') {
                outflows.push({
                    type: 'SIP',
                    category: 'INVESTMENT',
                    name: inv.name,
                    day: parseInt(inv.sip_date) || 1,
                    amount: parseFloat(inv.sip_amount) || 0
                });
            }
        });

        // 4. EMIs (Expense - Bank Loans)
        loans.forEach(loan => {
            // EMIs
            if (loan.loan_type === 'BANK' && parseFloat(loan.emi_amount) > 0) {
                outflows.push({
                    type: 'EMI',
                    category: 'LOAN',
                    name: `${loan.lender} Loan EMI`,
                    day: new Date(loan.emi_date || Date.now()).getDate(), // Extract day from next EMI date
                    amount: parseFloat(loan.emi_amount)
                });
            }

            // Interest Payment (Personal Loans)
            if (loan.loan_type === 'PERSONAL') {
                // Similar logic to lending, or use specific interest payment amount if user set it
                // Assuming 'interest_payment_frequency' is MONTHLY
                const principal = parseFloat(loan.principal_amount);
                const rate = parseFloat(loan.interest_rate);

                if (principal > 0 && rate > 0) {
                    const monthlyInterest = (principal * (rate / 100)) / 12;
                    outflows.push({
                        type: 'Interest Pay',
                        category: 'LOAN',
                        name: `Interest to ${loan.lender}`,
                        day: parseInt(loan.interest_payment_date) || 1,
                        amount: monthlyInterest
                    });
                }
            }
        });

        renderInflows(inflows);
        renderOutflows(outflows);
        updateSummary(inflows, outflows);

    } catch (e) {
        console.error("Error loading cash flow data", e);
        JARVIS.showToast("Failed to load cash flow data", "error");
    }
}

function renderInflows(inflows) {
    const list = document.getElementById('inflowList');
    inflows.sort((a, b) => a.day - b.day);

    list.innerHTML = inflows.map(item => `
        <tr>
            <td>
                <div class="fw-bold">${item.name}</div>
                <small class="text-muted">${item.category}</small>
            </td>
            <td>${item.day}${getOrdinal(item.day)}</td>
            <td class="text-end fw-bold text-success">+${JARVIS.formatCurrency(item.amount)}</td>
            <td class="text-end">
                ${item.is_manual ? `<button class="btn btn-sm btn-link text-danger" onclick="deleteIncome(${item.id})"><i class="fas fa-trash"></i></button>` : '<span class="text-muted"><i class="fas fa-lock"></i></span>'}
            </td>
        </tr>
    `).join('');
}

function renderOutflows(outflows) {
    const list = document.getElementById('outflowList');
    outflows.sort((a, b) => a.day - b.day);

    list.innerHTML = outflows.map(item => `
        <tr>
             <td><span class="badge ${getBadgeClass(item.type)}">${item.type}</span></td>
            <td>
                <div class="fw-bold">${item.name}</div>
            </td>
            <td>${item.day}${getOrdinal(item.day)}</td>
            <td class="text-end fw-bold text-danger">-${JARVIS.formatCurrency(item.amount)}</td>
        </tr>
    `).join('');
}

function updateSummary(inflows, outflows) {
    const totalIn = inflows.reduce((sum, item) => sum + item.amount, 0);
    const totalOut = outflows.reduce((sum, item) => sum + item.amount, 0);
    const net = totalIn - totalOut;

    document.getElementById('totalInflow').textContent = JARVIS.formatCurrency(totalIn);
    document.getElementById('totalOutflow').textContent = JARVIS.formatCurrency(totalOut);

    const elNet = document.getElementById('netCashFlow');
    elNet.textContent = JARVIS.formatCurrency(net);
    elNet.className = `card-value ${net >= 0 ? 'text-success' : 'text-danger'}`;
}

// --- Utils ---
function getOrdinal(n) {
    const s = ["th", "st", "nd", "rd"];
    const v = n % 100;
    return s[(v - 20) % 10] || s[v] || s[0];
}

function getBadgeClass(type) {
    switch (type) {
        case 'SIP': return 'bg-info bg-opacity-10 text-info';
        case 'EMI': return 'bg-warning bg-opacity-10 text-warning';
        case 'Interest Pay': return 'bg-danger bg-opacity-10 text-danger';
        default: return 'bg-secondary';
    }
}

// --- Modal & Form Actions ---

function openAddIncomeModal() {
    new bootstrap.Modal(document.getElementById('addIncomeModal')).show();
}

async function loadAccountsForDropdown() {
    try {
        const accounts = await JARVIS.request('GET', '/api/accounts');
        const select = document.getElementById('accountSelect');
        if (accounts && select) {
            select.innerHTML = '<option value="">-- None --</option>' +
                accounts.map(acc => `<option value="${acc.id}">${acc.name}</option>`).join('');
        }
    } catch (e) { console.error(e); }
}

async function handleAddIncome(e) {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(e.target));

    try {
        await JARVIS.post('/api/recurring-incomes', data);
        bootstrap.Modal.getInstance(document.getElementById('addIncomeModal')).hide();
        e.target.reset();
        JARVIS.showToast('Income added successfully', 'success');
        loadCashFlowData(); // Reload
    } catch (err) {
        console.error(err);
        JARVIS.showToast('Failed to add income', 'error');
    }
}

async function deleteIncome(id) {
    if (!confirm('Delete this income entry?')) return;
    try {
        await JARVIS.request('DELETE', `/api/recurring-incomes/${id}`);
        JARVIS.showToast('Deleted successfully', 'success');
        loadCashFlowData();
    } catch (e) {
        JARVIS.showToast('Failed to delete', 'error');
    }
}
