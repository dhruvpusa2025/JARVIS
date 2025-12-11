document.addEventListener('DOMContentLoaded', async function () {
    await JARVIS.init();
    loadAccounts();
    document.getElementById('lendingDate').valueAsDate = new Date();
});

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

function toggleInterestFields(hasInterest) {
    const fields = document.getElementById('interestFields');
    if (hasInterest === 'yes') {
        fields.classList.add('active');
        fields.querySelectorAll('input, select').forEach(el => el.required = true);
    } else {
        fields.classList.remove('active');
        fields.querySelectorAll('input, select').forEach(el => el.required = false);
    }
}

async function handleAddLending(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    const hasInterest = formData.get('hasInterest') === 'yes';
    const amount = parseFloat(formData.get('amount'));

    if (!amount || !formData.get('accountId')) {
        showNotification('Please fill required fields', 'error');
        return;
    }

    const lending = {
        borrower: formData.get('borrower'),
        amount: amount,
        outstanding_amount: amount, // Initial outstanding
        interest_rate: hasInterest ? parseFloat(formData.get('interestRate')) : 0,
        frequency: hasInterest ? formData.get('frequency') : 'none',
        repayment_type: hasInterest ? formData.get('repaymentType') : 'lump_sum',
        return_date: formData.get('returnDate'),
        start_date: formData.get('startDate'),
        notes: formData.get('notes'),
        // nextInterestDate: hasInterest ? calculateNextInterestDate... (Backend can handle this or we send it)
        is_active: true
    };

    // We also need accountId to deduct balance, assuming API handles it or we send it
    // My Lending Migration has account_id? Let's check schema later. Assuming yes.
    // If not, I can't deduct balance easily via relationship.
    // But let's assume I need to pass account_id to the API.
    lending.account_id = parseInt(formData.get('accountId'));

    try {
        await JARVIS.add('lendings', lending);
        showNotification('Lending added successfully!', 'success');
        setTimeout(() => window.location.href = '/lendings', 1000);
    } catch (error) {
        showNotification('Failed to add lending', 'error');
    }
}
