document.addEventListener('DOMContentLoaded', async function () {
    await JARVIS.init();
    // Pre-select logic if query params exist? Not needed for now.
});

function toggleTypeFields() {
    const type = document.getElementById('typeSelect').value;
    const unitFields = document.getElementById('unitFields');
    const interestFields = document.getElementById('interestFields');

    // Reset
    unitFields.classList.remove('active');
    interestFields.classList.remove('active');

    // Inputs inside should be not required by default
    unitFields.querySelectorAll('input').forEach(i => i.required = false);
    interestFields.querySelectorAll('input').forEach(i => i.required = false);

    if (['mutual_fund', 'stock', 'gold'].includes(type)) {
        unitFields.classList.add('active');
        // Make units required? Maybe.
    } else if (['fd', 'rd'].includes(type)) {
        interestFields.classList.add('active');
    }
}

function toggleSipFields() {
    const isSip = document.getElementById('sipCheck').checked;
    const sipFields = document.getElementById('sipFields');

    if (isSip) {
        sipFields.classList.add('active');
        sipFields.querySelectorAll('input').forEach(i => i.required = true);
        document.getElementById('sourceAccountGroup').style.display = 'block';
    } else {
        sipFields.classList.remove('active');
        sipFields.querySelectorAll('input').forEach(i => i.required = false);
        document.getElementById('sourceAccountGroup').style.display = 'none';

        // Clear selection if hidden
        document.querySelector('select[name="source_account_id"]').value = "";
    }
}

async function handleAddInvestment(event) {
    event.preventDefault();
    const btn = document.getElementById('submitBtn');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';

    const formData = new FormData(event.target);

    // Build object
    const investment = {
        name: formData.get('name'),
        type: formData.get('type'),
        invested_amount: parseFloat(formData.get('invested_amount')),
        current_value: formData.get('current_value') ? parseFloat(formData.get('current_value')) : null,
        is_sip: document.getElementById('sipCheck').checked
    };

    // Add optional fields
    const accountId = formData.get('investment_account_id');
    if (accountId) investment.investment_account_id = parseInt(accountId);

    const sourceAccountId = formData.get('source_account_id');
    if (sourceAccountId) investment.source_account_id = parseInt(sourceAccountId);
    const units = formData.get('units');
    if (units) investment.units = parseFloat(units);

    const buyPrice = formData.get('buy_price');
    if (buyPrice) investment.buy_price = parseFloat(buyPrice);

    const currentPrice = formData.get('current_price');
    if (currentPrice) investment.current_price = parseFloat(currentPrice);

    const interestRate = formData.get('interest_rate');
    if (interestRate) investment.interest_rate = parseFloat(interestRate);

    const maturityDate = formData.get('maturity_date');
    if (maturityDate) investment.maturity_date = maturityDate;

    if (investment.is_sip) {
        investment.sip_amount = parseFloat(formData.get('sip_amount'));
        investment.sip_date = parseInt(formData.get('sip_date'));
    }

    // Auto-calc current value if missing and units/price exist
    if (!investment.current_value && investment.units && (investment.current_price || investment.buy_price)) {
        investment.current_value = investment.units * (investment.current_price || investment.buy_price);
    }

    // If still no current value, default to invested amount (for initial entry)
    if (!investment.current_value) {
        investment.current_value = investment.invested_amount;
    }

    try {
        await JARVIS.add('investments', investment);
        showNotification('Investment added successfully!', 'success');
        setTimeout(() => window.location.href = '/investments', 1000);
    } catch (error) {
        btn.disabled = false;
        btn.innerHTML = originalText;
        // showNotification handles errors in JARVIS.add, but we can catch extra here
    }
}
