document.addEventListener('DOMContentLoaded', async function () {
    await JARVIS.init();
    loadLoans();
});

function loadLoans() {
    const loans = JARVIS.get('loans') || [];

    const totalOutstanding = loans.reduce((sum, l) => sum + parseFloat(l.outstanding_amount || l.outstanding), 0);
    const totalEMI = loans.reduce((sum, l) => sum + parseFloat(l.emi_amount || l.emiAmount), 0);
    const avgInterest = loans.length > 0 ? (loans.reduce((sum, l) => sum + parseFloat(l.interest_rate || l.interestRate), 0) / loans.length).toFixed(2) : 0;

    document.getElementById('totalOutstanding').textContent = JARVIS.formatCurrency(totalOutstanding);
    document.getElementById('totalEMI').textContent = JARVIS.formatCurrency(totalEMI);
    document.getElementById('avgInterest').textContent = avgInterest + '%';

    displayLoans(loans);
}

function displayLoans(loans) {
    const container = document.getElementById('loansList');

    if (loans.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: var(--text-muted); padding: 2rem;">No loans yet</p>';
        return;
    }

    container.innerHTML = loans.map(loan => {
        const principal = parseFloat(loan.principal_amount || loan.principal);
        const outstanding = parseFloat(loan.outstanding_amount || loan.outstanding);
        const emiAmount = parseFloat(loan.emi_amount || loan.emiAmount);
        const interestRate = parseFloat(loan.interest_rate || loan.interestRate);
        const emiDay = loan.emi_date || loan.emiDay;

        const paidAmount = principal - outstanding;
        const progress = (paidAmount / principal) * 100;

        const isPersonal = loan.loan_type === 'PERSONAL';
        const emiLabel = isPersonal ? 'Interest Payment' : 'EMI Amount';
        const emiValueLabel = isPersonal
            ? (loan.interest_payment_frequency !== 'NONE' ? `${loan.interest_payment_frequency} (${loan.interest_payment_date ? 'Day ' + loan.interest_payment_date : ''})` : '-')
            : JARVIS.formatCurrency(emiAmount);

        // Buttons
        let buttonsHtml = '';
        if (isPersonal) {
            buttonsHtml = `
                <button class="btn-primary" onclick="openRepaymentModal(${loan.id}, 'PRINCIPAL')" style="flex: 1;">
                    <i class="fas fa-money-bill-wave"></i> Pay Principal
                </button>
                ${(interestRate && interestRate > 0) ? `
                <button class="btn-secondary" onclick="openRepaymentModal(${loan.id}, 'INTEREST')" style="flex: 1;">
                    <i class="fas fa-percent"></i> Pay Interest
                </button>
                ` : ''}
            `;
        } else {
            buttonsHtml = `
                <button class="btn-primary" onclick="openPayEMIModal(${loan.id})" style="flex: 1;">
                    <i class="fas fa-calendar-check"></i> Pay EMI
                </button>
                <button class="btn-secondary" onclick="openRepaymentModal(${loan.id}, 'PRINCIPAL')" style="flex: 1;">
                    <i class="fas fa-coins"></i> Extra Payment
                </button>
            `;
        }

        return `
            <div class="card" style="margin-bottom: 1.5rem;">
                <div class="card-body" style="padding: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                        <div>
                            <h3 style="margin-bottom: 0.25rem;">${formatLoanType(loan.type)} <span style="font-size:0.7em; color:var(--text-muted)">(${isPersonal ? 'Personal' : 'Bank'})</span></h3>
                            <p style="color: var(--text-muted); font-size: 0.875rem;">${loan.lender}</p>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 1.5rem; font-weight: 700; color: var(--danger);">
                                ${JARVIS.formatCurrency(outstanding)}
                            </div>
                            <div style="font-size: 0.875rem; color: var(--text-muted);">Outstanding</div>
                        </div>
                    </div>

                    <div style="background: var(--bg-tertiary); height: 8px; border-radius: 4px; margin: 1rem 0; overflow: hidden;">
                        <div style="background: var(--gradient-primary); height: 100%; width: ${progress}%; transition: width 0.3s ease;"></div>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-top: 1rem;">
                        <div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">Principal</div>
                            <div style="font-weight: 600;">${JARVIS.formatCurrency(principal)}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">${emiLabel}</div>
                            <div style="font-weight: 600;">${emiValueLabel}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">Interest Rate</div>
                            <div style="font-weight: 600;">${interestRate ? interestRate + '% p.a.' : '0%'}</div>
                        </div>
                        ${!isPersonal ? `
                        <div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">EMI Date</div>
                            <div style="font-weight: 600;">${emiDay}th of month</div>
                        </div>` : ''}
                        <div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">Progress</div>
                            <div style="font-weight: 600; color: var(--primary-green);">${progress.toFixed(1)}%</div>
                        </div>
                    </div>
                </div>
                <div class="loan-actions" style="margin-top: 1.5rem; display: flex; gap: 1rem; padding: 0 1.5rem 1.5rem;">
                    ${buttonsHtml}
                </div>
            </div>
        `;
    }).join('');
}

function formatLoanType(type) {
    const types = {
        'home': 'Home',
        'car': 'Car',
        'personal': 'Personal',
        'education': 'Education',
        'other': 'Other'
    };
    return types[type] || type;
}

// Pay EMI Modal (Bank Only now)
function openPayEMIModal(id) {
    // Treat as Principal payment for now or existing logic?
    // Let's redirect to general repayment logic mapping to PRINCIPAL for simplicity, 
    // or keep separate if Bank EMI logic is distinct. 
    // The user wants 'functionality of loan which is now is needed so do not remove that'.
    // So I keep openPayEMIModal but wire it to 'repay' endpoint if possible, or keep as is.
    // The previous implementation of handlePayEMI was a dummy 'showNotification'. 
    // So I will implement it properly now using the new API.

    const loan = JARVIS.get('loans').find(l => l.id === id);
    if (!loan) return;

    document.getElementById('emiLoanId').value = loan.id;
    document.getElementById('emiLoanName').value = `${formatLoanType(loan.type)} Loan - ${loan.lender}`;
    document.getElementById('emiAmount').value = loan.emi_amount || loan.emiAmount;

    loadAccountsForModal('emiAccount');
    document.getElementById('emiDate').valueAsDate = new Date();
    document.getElementById('payEMIModal').classList.add('active');
}

function closePayEMIModal() {
    document.getElementById('payEMIModal').classList.remove('active');
}

async function handlePayEMI(event) {
    event.preventDefault();
    const id = parseInt(document.getElementById('emiLoanId').value);
    const amount = parseFloat(document.getElementById('emiAmount').value);
    const accountId = parseInt(document.getElementById('emiAccount').value);
    const date = document.getElementById('emiDate').value;

    try {
        // Bank EMI usually is Principal + Interest. 
        // Our simple API handles 'PRINCIPAL' (reducing outstanding) or 'INTEREST' (not reducing).
        // For Bank EMI, we likely want to reduce outstanding? 
        // Or strictly following the new generic 'repay' API:
        // Let's assume EMI pays down Principal for this MVP, or we need a new 'EMI' type?
        // The controller supports PRINCIPAL, INTEREST. 
        // Using PRINCIPAL ensures outstanding drops.

        await JARVIS.request('POST', `/api/loans/${id}/repay`, {
            amount: amount,
            account_id: accountId,
            date: date,
            payment_type: 'PRINCIPAL' // Treating EMI as Principal payment for simplicity
        });

        showNotification('EMI Payment recorded', 'success');
        closePayEMIModal();
        loadLoans(); // Reload
    } catch (e) {
        showNotification('Failed to pay EMI: ' + (e.response?.data?.message || e.message), 'error');
    }
}

// General Repayment Modal (Personal: Principal/Interest, Bank: Extra)
let currentRepaymentType = 'PRINCIPAL';

function openRepaymentModal(id, type) {
    currentRepaymentType = type;
    const loan = JARVIS.get('loans').find(l => l.id === id);
    if (!loan) return;

    document.getElementById('extraLoanId').value = loan.id;
    document.getElementById('extraLoanName').value = `${formatLoanType(loan.type)} - ${type === 'PRINCIPAL' ? 'Principal Repayment' : 'Interest Payment'}`; // Update header
    document.querySelector('#extraPaymentModal h2').textContent = type === 'PRINCIPAL' ? 'Pay Principal' : 'Pay Interest';

    loadAccountsForModal('extraAccount');
    document.getElementById('extraDate').valueAsDate = new Date();
    document.getElementById('extraPaymentModal').classList.add('active');
}

function closeExtraPaymentModal() {
    document.getElementById('extraPaymentModal').classList.remove('active');
}

async function handleExtraPayment(event) {
    event.preventDefault(); // This is bound to 'extraPaymentModal' form onsubmit

    const id = parseInt(document.getElementById('extraLoanId').value);
    const amount = parseFloat(document.getElementById('extraAmount').value);
    const accountId = parseInt(document.getElementById('extraAccount').value);
    const date = document.getElementById('extraDate').value;

    try {
        await JARVIS.request('POST', `/api/loans/${id}/repay`, {
            amount: amount,
            account_id: accountId,
            date: date,
            payment_type: currentRepaymentType
        });

        showNotification('Payment recorded successfully', 'success');
        closeExtraPaymentModal();
        loadLoans();
    } catch (e) {
        console.error(e);
        showNotification('Failed to record payment: ' + (e.response?.data?.message || e.message), 'error');
    }
}

function loadAccountsForModal(selectId) {
    const accounts = JARVIS.get('accounts') || [];
    const select = document.getElementById(selectId);
    if (select) {
        select.innerHTML = accounts.map(a => `<option value="${a.id}">${a.name} (₹${a.balance})</option>`).join('');
    }
}
