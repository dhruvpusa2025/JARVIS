document.addEventListener('DOMContentLoaded', async function () {
    await JARVIS.init();
    loadLendings();

    // Set default dates
    const today = new Date().toISOString().split('T')[0];
    const elIntDate = document.getElementById('interestDate');
    const elRepDate = document.getElementById('repaymentDate');
    if (elIntDate) elIntDate.value = today;
    if (elRepDate) elRepDate.value = today;
});

function loadLendings() {
    const lendings = JARVIS.get('lendings') || [];

    const totalLent = lendings.reduce((sum, l) => sum + parseFloat(l.amount), 0);
    const totalOutstanding = lendings.reduce((sum, l) => sum + parseFloat(l.outstanding_amount || l.outstanding), 0);

    // Calculate total interest earned from transactions
    const transactions = JARVIS.get('transactions') || [];
    const totalInterest = transactions
        .filter(t => t.category === 'Lending Interest' && t.type === 'income')
        .reduce((sum, t) => sum + parseFloat(t.amount), 0);

    const elTotalLent = document.getElementById('totalLent');
    const elTotalOutstanding = document.getElementById('totalOutstanding');
    const elTotalInterest = document.getElementById('totalInterest');

    if (elTotalLent) elTotalLent.textContent = JARVIS.formatCurrency(totalLent);
    if (elTotalOutstanding) elTotalOutstanding.textContent = JARVIS.formatCurrency(totalOutstanding);
    if (elTotalInterest) elTotalInterest.textContent = JARVIS.formatCurrency(totalInterest);

    displayLendings(lendings);
}

function displayLendings(lendings) {
    const container = document.getElementById('lendingsList');

    if (!container) return;

    if (lendings.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: var(--text-muted); padding: 2rem;">No lendings yet</p>';
        return;
    }

    container.innerHTML = lendings.map(lending => {
        const outstanding = parseFloat(lending.outstanding_amount || lending.outstanding);
        const amount = parseFloat(lending.amount);
        const isCompleted = outstanding <= 0;

        let nextInterestBadge = '';
        if (!isCompleted && lending.interest_rate > 0 && lending.frequency !== 'none') {
            const nextDate = getNextInterestDate(lending.start_date, lending.frequency);
            if (nextDate) {
                nextInterestBadge = `<span class="due-badge" style="background: var(--info); cursor: default;" title="Next Interest Due"><i class="fas fa-calendar-alt"></i> Next: ${JARVIS.formatDate(nextDate)}</span>`;
            }
        }

        return `
        <div class="lending-card ${isCompleted ? 'completed' : ''}">
            <div class="lending-header">
                <div>
                    <div class="borrower-name">${lending.borrower}</div>
                    ${nextInterestBadge}
                </div>
            </div>
            <div class="lending-amount">
                ${JARVIS.formatCurrency(outstanding)}
                ${isCompleted ? '<span style="font-size: 0.8rem; color: var(--success); margin-left:10px;">(Repaid)</span>' : ''}
            </div>

            <div class="lending-details">
                <div class="detail-item">
                    <div class="detail-label">Original Amount</div>
                    <div class="detail-value">${JARVIS.formatCurrency(amount)}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Interest Rate</div>
                    <div class="detail-value">${lending.interest_rate || lending.interestRate}% ${formatFrequency(lending.frequency)}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Start Date</div>
                    <div class="detail-value">${JARVIS.formatDate(lending.start_date || lending.startDate)}</div>
                </div>
            </div>

            ${!isCompleted ? `
            <div class="lending-actions">
                ${(lending.interest_rate || lending.interestRate) > 0 ? `
                <button class="btn-small btn-success" onclick="receiveInterest(${lending.id})">
                    <i class="fas fa-coins"></i> Receive Interest
                </button>
                ` : ''}
                <button class="btn-small btn-info" onclick="receiveRepayment(${lending.id})">
                    <i class="fas fa-hand-holding-usd"></i> Receive Repayment
                </button>
            </div>
            ` : ''}
        </div>
        `;
    }).join('');
}

function getNextInterestDate(startDateStr, frequency) {
    if (!startDateStr || frequency === 'none') return null;

    const start = new Date(startDateStr);
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    let nextDate = new Date(start);

    // Simple logic: increment nextDate by frequency until it is >= today
    // Or if user wants strictly next cycle date regardless of overdue:
    // "Calculate base on start date and frequency"

    // If start date is in future, that is the next date
    if (nextDate > today) return nextDate;

    while (nextDate < today) {
        if (frequency === 'monthly') {
            nextDate.setMonth(nextDate.getMonth() + 1);
        } else if (frequency === 'quarterly') {
            nextDate.setMonth(nextDate.getMonth() + 3);
        } else if (frequency === 'yearly') {
            nextDate.setFullYear(nextDate.getFullYear() + 1);
        } else {
            return null; // Unknown freq
        }
    }

    return nextDate;
}

function formatFrequency(freq) {
    const frequencies = {
        'monthly': 'per month',
        'quarterly': 'per quarter',
        'yearly': 'per year',
        'none': ''
    };
    return frequencies[freq] || '';
}

// Interest Modal Logic
function receiveInterest(id) {
    const lending = JARVIS.get('lendings').find(l => l.id === id);
    if (!lending) return;

    document.getElementById('interestLendingId').value = id;

    // Estimate Interest logic could go here (P * R * T)
    // For now simpler manual input
    document.getElementById('interestAmount').value = '';

    populateAccountSelect('interestAccount');
    document.getElementById('receiveInterestModal').classList.add('active');
}

function closeInterestModal() {
    document.getElementById('receiveInterestModal').classList.remove('active');
}

async function handleInterestSubmit(event) {
    event.preventDefault();
    const id = parseInt(document.getElementById('interestLendingId').value);
    const amount = parseFloat(document.getElementById('interestAmount').value);
    const date = document.getElementById('interestDate').value;
    const accountId = parseInt(document.getElementById('interestAccount').value);

    // Update Account Balance
    const account = JARVIS.get('accounts').find(a => a.id === accountId);
    if (account) {
        account.balance += amount;
        await JARVIS.update('accounts', accountId, account);
    }

    // Create Transaction
    const lending = JARVIS.get('lendings').find(l => l.id === id);
    await JARVIS.add('transactions', {
        date: date,
        amount: amount,
        type: 'income',
        category: 'Lending Interest', // Ensure this category exists or string
        account: account ? account.name : 'Unknown', // Legacy field support
        account_id: accountId,
        description: `Interest received from ${lending ? lending.borrower : 'Loan'}`
    });

    closeInterestModal();
    loadLendings(); // Will refresh total interest
    showNotification('Interest recorded successfully', 'success');
}

// Repayment Modal Logic
function receiveRepayment(id) {
    const lending = JARVIS.get('lendings').find(l => l.id === id);
    if (!lending) return;

    const outstanding = parseFloat(lending.outstanding_amount || lending.outstanding);
    document.getElementById('repaymentLendingId').value = id;
    document.getElementById('repaymentAmount').value = outstanding; // Default to full
    document.getElementById('repaymentAmount').max = outstanding;
    document.getElementById('maxRepaymentHint').textContent = `Max: ${JARVIS.formatCurrency(outstanding)}`;

    populateAccountSelect('repaymentAccount');
    document.getElementById('receiveRepaymentModal').classList.add('active');
}

function closeRepaymentModal() {
    document.getElementById('receiveRepaymentModal').classList.remove('active');
}

async function handleRepaymentSubmit(event) {
    event.preventDefault();
    const id = parseInt(document.getElementById('repaymentLendingId').value);
    const amount = parseFloat(document.getElementById('repaymentAmount').value);
    const date = document.getElementById('repaymentDate').value;
    const accountId = parseInt(document.getElementById('repaymentAccount').value);

    const lending = JARVIS.get('lendings').find(l => l.id === id);
    if (!lending) return;

    // Update Lending Outstanding
    const newOutstanding = (parseFloat(lending.outstanding_amount || lending.outstanding) - amount).toFixed(2);
    lending.outstanding_amount = parseFloat(newOutstanding);
    await JARVIS.update('lendings', id, lending);

    // Update Account Balance
    const account = JARVIS.get('accounts').find(a => a.id === accountId);
    if (account) {
        account.balance += amount;
        await JARVIS.update('accounts', accountId, account);
    }

    // Create Transaction
    await JARVIS.add('transactions', {
        date: date,
        amount: amount,
        type: 'income',
        category: 'Lending Repayment',
        account: account ? account.name : 'Unknown',
        account_id: accountId,
        description: `Repayment received from ${lending.borrower}`
    });

    closeRepaymentModal();
    loadLendings();
    showNotification('Repayment recorded successfully', 'success');
}

function populateAccountSelect(elementId) {
    const accounts = JARVIS.get('accounts') || [];
    const select = document.getElementById(elementId);
    if (select) {
        select.innerHTML = accounts.map(a => `<option value="${a.id}">${a.name} (${JARVIS.formatCurrency(a.balance)})</option>`).join('');
    }
}
