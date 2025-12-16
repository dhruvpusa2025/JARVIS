document.addEventListener('DOMContentLoaded', async function () {
    await JARVIS.init();
    loadAccounts();
    document.getElementById('loanDate').valueAsDate = new Date();
    toggleLoanFields(); // Init state
});

function toggleLoanFields() {
    const type = document.getElementById('loanCategory').value;
    const bankFields = document.getElementById('bankFields');
    const personalFields = document.getElementById('personalFields');

    // Inputs to toggle 'required'
    const tenure = document.querySelector('[name="tenureMonths"]');
    const emiDay = document.querySelector('[name="emiDay"]');

    if (type === 'BANK') {
        bankFields.style.display = 'block';
        personalFields.style.display = 'none';
        tenure.required = true;
        emiDay.required = true;
    } else {
        bankFields.style.display = 'none';
        personalFields.style.display = 'block';
        tenure.required = false;
        emiDay.required = false;
        document.getElementById('emiAmount').value = ''; // Clear EMI
    }
}

function loadAccounts() {
    const accounts = JARVIS.get('accounts') || [];
    const select = document.getElementById('accountSelect');
    accounts.forEach(acc => {
        const option = document.createElement('option');
        option.value = acc.id;
        option.textContent = `${acc.name} (${JARVIS.formatCurrency(acc.balance)})`;
        select.appendChild(option);
    });
}

function calculateEMI() {
    const principal = parseFloat(document.querySelector('[name="principal"]').value) || 0;
    const rate = parseFloat(document.querySelector('[name="interestRate"]').value) || 0;
    const tenure = parseInt(document.querySelector('[name="tenureMonths"]').value) || 0;
    const loanType = document.getElementById('loanCategory').value;

    if (loanType === 'BANK' && principal && rate && tenure) {
        const monthlyRate = rate / 1200;
        const emi = principal * monthlyRate * Math.pow(1 + monthlyRate, tenure) / (Math.pow(1 + monthlyRate, tenure) - 1);
        document.getElementById('emiAmount').value = Math.round(emi);
    } else {
        // For Personal loan, maybe strictly 0 or allow manual? keeping 0 for now as it's non-EMI
        document.getElementById('emiAmount').value = '';
    }
}

async function handleAddLoan(event) {
    event.preventDefault();
    const formData = new FormData(event.target);

    // Basic Validation
    if (!formData.get('principal') || !formData.get('accountId')) {
        showNotification('Please fill all required fields', 'error');
        return;
    }

    const loanType = formData.get('loan_type');

    const loan = {
        loan_type: loanType,
        type: formData.get('type'),
        lender: formData.get('lender'),
        principal_amount: parseFloat(formData.get('principal')),
        interest_rate: formData.get('interestRate') ? parseFloat(formData.get('interestRate')) : null,
        outstanding_amount: parseFloat(formData.get('principal')),
        account_id: parseInt(formData.get('accountId')),
        start_date: formData.get('startDate'),

        // Bank Specific
        tenure_months: loanType === 'BANK' ? parseInt(formData.get('tenureMonths')) : null, // Not in model but maybe useful? API ignores extra fields usually
        emi_amount: loanType === 'BANK' ? parseFloat(formData.get('emiAmount')) : null,
        emi_date: loanType === 'BANK' ? parseInt(formData.get('emiDay')) : null,

        // Personal Specific
        interest_payment_frequency: loanType === 'PERSONAL' ? formData.get('interestFrequency') : null,
        interest_payment_date: (loanType === 'PERSONAL' && formData.get('interestPaymentDate')) ? parseInt(formData.get('interestPaymentDate')) : null,

        is_active: true
    };

    try {
        await JARVIS.add('loans', loan);
        showNotification('Loan added successfully!', 'success');
        setTimeout(() => window.location.href = '/loans', 1000);
    } catch (error) {
        console.error(error);
        showNotification('Failed to add loan: ' + (error.response?.data?.message || error.message), 'error');
    }
}
