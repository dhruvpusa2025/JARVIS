document.addEventListener('DOMContentLoaded', async function () {
    await JARVIS.init();
    loadAccounts();
    document.getElementById('loanDate').valueAsDate = new Date();
});

function loadAccounts() {
    const accounts = JARVIS.get('accounts') || [];
    const select = document.getElementById('accountSelect');
    // Filter for Bank accounts usually, but let's show all for flexibility
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

    if (principal && rate && tenure) {
        const monthlyRate = rate / 1200;
        const emi = principal * monthlyRate * Math.pow(1 + monthlyRate, tenure) / (Math.pow(1 + monthlyRate, tenure) - 1);
        document.getElementById('emiAmount').value = Math.round(emi);
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

    const loan = {
        type: formData.get('type'),
        lender: formData.get('lender'),
        principal: parseFloat(formData.get('principal')),
        interest_rate: parseFloat(formData.get('interestRate')),
        tenure_months: parseInt(formData.get('tenureMonths')),
        emi_amount: parseFloat(formData.get('emiAmount')),
        emi_day: parseInt(formData.get('emiDay')),
        outstanding_amount: parseFloat(formData.get('principal')), // Initial outstanding
        account_id: parseInt(formData.get('accountId')),
        start_date: formData.get('startDate'),
        is_active: true
    };

    try {
        await JARVIS.add('loans', loan);
        showNotification('Loan added successfully!', 'success');
        setTimeout(() => window.location.href = '/loans', 1000);
    } catch (error) {
        showNotification('Failed to add loan', 'error');
    }
}
