document.addEventListener('DOMContentLoaded', async function () {
    await JARVIS.init();
    loadLendings();
});

function loadLendings() {
    const lendings = JARVIS.get('lendings') || [];

    const totalLent = lendings.reduce((sum, l) => sum + parseFloat(l.amount), 0);
    const totalOutstanding = lendings.reduce((sum, l) => sum + parseFloat(l.outstanding_amount || l.outstanding), 0);

    // Calculate total interest earned (mock calculation for now based on what we have)
    // In real app, this should come from transaction history or a calculated field
    const totalInterest = 0;

    document.getElementById('totalLent').textContent = JARVIS.formatCurrency(totalLent);
    document.getElementById('totalOutstanding').textContent = JARVIS.formatCurrency(totalOutstanding);
    document.getElementById('totalInterest').textContent = JARVIS.formatCurrency(totalInterest);

    displayLendings(lendings);
}

function displayLendings(lendings) {
    const container = document.getElementById('lendingsList');

    if (lendings.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: var(--text-muted); padding: 2rem;">No lendings yet</p>';
        return;
    }

    container.innerHTML = lendings.map(lending => {
        const outstanding = parseFloat(lending.outstanding_amount || lending.outstanding);
        const amount = parseFloat(lending.amount);
        const isCompleted = outstanding <= 0;
        // Mock due logic
        const isDue = Math.random() > 0.7;

        return `
        <div class="lending-card ${isDue ? 'due' : ''} ${isCompleted ? 'completed' : ''}">
            <div class="lending-header">
                <div class="borrower-name">${lending.borrower}</div>
                ${isDue ? '<span class="due-badge"><i class="fas fa-bell"></i> Interest Due</span>' : ''}
            </div>
            <div class="lending-amount">
                ${JARVIS.formatCurrency(outstanding)}
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
            ` : '<p style="color: var(--success); font-weight: 600;"><i class="fas fa-check-circle"></i> Fully Repaid</p>'}
        </div>
        `;
    }).join('');
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

// Modal Actions (Stubbed)
function receiveInterest(id) {
    showNotification('Interest logic coming soon', 'info');
}

function receiveRepayment(id) {
    showNotification('Repayment logic coming soon', 'info');
}
