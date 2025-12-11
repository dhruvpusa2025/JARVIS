let currentFilter = 'all';
let allInvestments = [];

document.addEventListener('DOMContentLoaded', async function () {
    await JARVIS.init();
    loadInvestments();
    const sellDateInput = document.getElementById('sellDate');
    if (sellDateInput) {
        sellDateInput.valueAsDate = new Date();
    }
});

function loadInvestments() {
    allInvestments = JARVIS.get('investments') || [];
    updateSummary(allInvestments);
    renderGroupCards(allInvestments);
    displayInvestments(allInvestments);
}

function updateSummary(investments) {
    const totalInvested = investments.reduce((sum, inv) => sum + (inv.invested || inv.amount ||
        inv.purchasePrice || 0), 0);
    const currentValue = investments.reduce((sum, inv) => sum + (inv.currentValue || inv.amount ||
        inv.purchasePrice || 0), 0);
    const totalReturns = currentValue - totalInvested;
    const returnPercentage = totalInvested > 0 ? ((totalReturns / totalInvested) * 100).toFixed(2) : 0;

    const elTotalInvested = document.getElementById('totalInvested');
    const elCurrentValue = document.getElementById('currentValue');
    const elTotalReturns = document.getElementById('totalReturns');
    const elReturnPercentage = document.getElementById('returnPercentage');

    if (elTotalInvested) elTotalInvested.textContent = JARVIS.formatCurrency(totalInvested);
    if (elCurrentValue) elCurrentValue.textContent = JARVIS.formatCurrency(currentValue);
    if (elTotalReturns) elTotalReturns.textContent = JARVIS.formatCurrency(totalReturns);
    if (elReturnPercentage) {
        elReturnPercentage.textContent = `${totalReturns >= 0 ? '+' : ''}${returnPercentage}%`;
        elReturnPercentage.className = `card-trend ${totalReturns >= 0 ? 'positive' : 'negative'}`;
    }
}

function renderGroupCards(investments) {
    const groups = {
        'all': { name: 'All Assets', amount: 0, count: 0 },
        'mutual_fund': { name: 'Mutual Funds', amount: 0, count: 0 },
        'stock': { name: 'Stocks', amount: 0, count: 0 },
        'fd': { name: 'Fixed Deposits', amount: 0, count: 0 },
        'rd': { name: 'Recurring Deposits', amount: 0, count: 0 },
        'real_estate': { name: 'Real Estate', amount: 0, count: 0 }
    };

    investments.forEach(inv => {
        const val = inv.currentValue || inv.amount || inv.purchasePrice || 0;
        groups.all.amount += val;
        groups.all.count++;

        if (groups[inv.type]) {
            groups[inv.type].amount += val;
            groups[inv.type].count++;
        }
    });

    const container = document.getElementById('groupCards');
    if (container) {
        container.innerHTML = Object.entries(groups).map(([key, data]) => {
            if (data.count === 0 && key !== 'all') return '';
            return `
            <div class="group-card ${currentFilter === key ? 'active' : ''}" onclick="filterInvestments('${key}')">
                <h3>${data.name}</h3>
                <div class="amount">${JARVIS.formatCurrency(data.amount)}</div>
                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">${data.count} items
                </div>
            </div>
            `;
        }).join('');
    }
}

function filterInvestments(type) {
    currentFilter = type;
    renderGroupCards(allInvestments);
    displayInvestments(allInvestments);
}

function displayInvestments(investments) {
    const container = document.querySelector('.investment-grid'); // Changed logic to rely on class if ID not present? Or add simple ID in PHP
    // In original code, where was displayInvestments?
    // It seems displayInvestments wasn't in the original view_file output I saw?
    // Wait, let me check the view_file output again.
    // Lines 268 calls displayInvestments(allInvestments).
    // The definition of displayInvestments is MISSING in the view_file output for lines 1-595??
    // Ah, wait. I scrolled past it? No.
    // Let me check line 323: function filterInvestments...
    // Line 336: function getInvestmentStats...
    // The previous user tool output might have been truncated? No, it says "The above content shows the entire...".
    // Wait, line 327-328 syntax looks broken in the tool output:
    // 327:                     const filtered = type === 'all'
    // 328:                     'stock': 'Stock',
    // That looks like the tool output got garbled or the file itself is corrupt.
    // Line 327-331 seems to contain a random object map?
}

// Re-implementing displayInvestments properly based on logical inference
function displayInvestments(investments) {
    const container = document.createElement('div');
    container.className = 'investment-grid';
    // Actually we should append to a container in DOM.
    // There is <div class="container"> but no specific ID for grid.
    // The original HTML had:
    // .investment-grid { display: grid ... }
    // But where is the HTML element using this class?
    // It's likely dynamically created or I missed it.
    // Let's assume there's a div with ID 'investmentsList' or similar, or I should add one.
    // I will add <div id="investmentsList" class="investment-grid"></div> in the Blade file.

    const target = document.getElementById('investmentsList');
    if (!target) return;

    const filtered = currentFilter === 'all'
        ? investments
        : investments.filter(i => i.type === currentFilter);

    target.innerHTML = filtered.map(inv => `
        <div class="investment-card ${inv.type}">
            <div class="card-actions">
                <button class="action-btn" onclick="openEditModal(${inv.id})"><i class="fas fa-pencil-alt"></i></button>
                <button class="action-btn sell" onclick="openSellModal(${inv.id})"><i class="fas fa-money-bill-wave"></i></button>
            </div>
            <div class="investment-header">
                <div>
                    <div class="investment-type">${formatType(inv.type)}</div>
                    <div class="investment-name">${inv.name}</div>
                </div>
            </div>
            <div class="investment-stats">
                ${getInvestmentStats(inv)}
            </div>
        </div>
    `).join('');
}

function formatType(type) {
    const types = {
        'mutual_fund': 'Mutual Fund',
        'stock': 'Stock',
        'fd': 'Fixed Deposit',
        'rd': 'Recurring Deposit',
        'real_estate': 'Real Estate'
    };
    return types[type] || type;
}

function getInvestmentStats(inv) {
    if (inv.type === 'mutual_fund') {
        return `
            <div class="stat">
                <div class="stat-label">Units</div>
                <div class="stat-value">${inv.units ? inv.units.toFixed(4) : '0'}</div>
            </div>
            <div class="stat">
                <div class="stat-label">NAV</div>
                <div class="stat-value">₹${inv.currentPrice || inv.buyPrice}</div>
            </div>
            <div class="stat">
                <div class="stat-label">Invested</div>
                <div class="stat-value">${JARVIS.formatCurrency(inv.invested)}</div>
            </div>
            <div class="stat">
                <div class="stat-label">Current Value</div>
                <div class="stat-value">${JARVIS.formatCurrency(inv.currentValue)}</div>
            </div>
        `;
    } else if (inv.type === 'stock') {
        return `
            <div class="stat">
                <div class="stat-label">Shares</div>
                <div class="stat-value">${inv.quantity}</div>
            </div>
            <div class="stat">
                <div class="stat-label">Price</div>
                <div class="stat-value">₹${inv.currentPrice || inv.buyPrice}</div>
            </div>
            <div class="stat">
                <div class="stat-label">Invested</div>
                <div class="stat-value">${JARVIS.formatCurrency(inv.invested)}</div>
            </div>
            <div class="stat">
                <div class="stat-label">Current Value</div>
                <div class="stat-value">${JARVIS.formatCurrency(inv.currentValue)}</div>
            </div>
        `;
    } else if (inv.type === 'fd' || inv.type === 'rd') {
        return `
            <div class="stat">
                <div class="stat-label">Amount</div>
                <div class="stat-value">${JARVIS.formatCurrency(inv.amount)}</div>
            </div>
            <div class="stat">
                <div class="stat-label">Interest Rate</div>
                <div class="stat-value">${inv.interestRate}% p.a.</div>
            </div>
            <div class="stat">
                <div class="stat-label">Maturity Date</div>
                <div class="stat-value">${JARVIS.formatDate(inv.maturityDate)}</div>
            </div>
            <div class="stat">
                <div class="stat-label">Maturity Amount</div>
                <div class="stat-value">${JARVIS.formatCurrency(inv.maturityAmount)}</div>
            </div>
        `;
    } else if (inv.type === 'real_estate') {
        return `
            <div class="stat">
                <div class="stat-label">Purchase Price</div>
                <div class="stat-value">${JARVIS.formatCurrency(inv.purchasePrice)}</div>
            </div>
            <div class="stat">
                <div class="stat-label">Current Value</div>
                <div class="stat-value">${JARVIS.formatCurrency(inv.currentValue)}</div>
            </div>
            <div class="stat">
                <div class="stat-label">Purchase Date</div>
                <div class="stat-value">${JARVIS.formatDate(inv.purchaseDate)}</div>
            </div>
             <div class="stat">
                <div class="stat-label">Appreciation</div>
                <div class="stat-value">${JARVIS.formatCurrency(inv.currentValue - inv.purchasePrice)}</div>
            </div>
        `;
    }
    return '';
}

// Edit Modal Functions (Price Only)
function openEditModal(id) {
    const inv = allInvestments.find(i => i.id === id);
    if (!inv) return;

    document.getElementById('editId').value = inv.id;
    document.getElementById('editName').value = inv.name;

    // Populate fields based on type
    const priceInput = document.getElementById('editCurrentPrice');

    if (inv.type === 'mutual_fund') {
        priceInput.value = inv.currentPrice || inv.buyPrice;
    } else if (inv.type === 'stock') {
        priceInput.value = inv.currentPrice || inv.buyPrice;
    } else if (inv.type === 'real_estate') {
        priceInput.value = inv.currentValue;
    } else {
        // For FD/RD, maybe just allow name edit for now
        priceInput.parentElement.style.display = 'none';
    }

    document.getElementById('editModal').classList.add('active');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
}

function handleEditSubmit(event) {
    event.preventDefault();
    const id = parseInt(document.getElementById('editId').value);
    const inv = allInvestments.find(i => i.id === id);

    if (inv) {
        const newPrice = parseFloat(document.getElementById('editCurrentPrice').value);

        if (inv.type === 'mutual_fund') {
            inv.currentPrice = newPrice;
            inv.currentValue = inv.units * inv.currentPrice;
        } else if (inv.type === 'stock') {
            inv.currentPrice = newPrice;
            inv.currentValue = inv.quantity * inv.currentPrice;
        } else if (inv.type === 'real_estate') {
            inv.currentValue = newPrice;
        }

        JARVIS.update('investments', id, inv);
        closeEditModal();
        loadInvestments();
        showNotification('Investment value updated successfully', 'success');
    }
}

// Sell Modal Functions
function openSellModal(id) {
    const inv = allInvestments.find(i => i.id === id);
    if (!inv) return;

    document.getElementById('sellId').value = inv.id;
    document.getElementById('sellName').value = inv.name;

    const unitsInput = document.getElementById('sellUnits');
    const priceInput = document.getElementById('sellPrice');
    const availUnits = document.getElementById('availableUnits');

    if (inv.type === 'mutual_fund') {
        unitsInput.value = inv.units;
        availUnits.textContent = `Available Units: ${inv.units}`;
        priceInput.value = inv.currentPrice || inv.buyPrice;
        unitsInput.parentElement.style.display = 'block';
    } else if (inv.type === 'stock') {
        unitsInput.value = inv.quantity;
        availUnits.textContent = `Available Shares: ${inv.quantity}`;
        priceInput.value = inv.currentPrice || inv.buyPrice;
        unitsInput.parentElement.style.display = 'block';
    } else {
        // For FD/RD/Real Estate
        if (inv.type === 'fd' || inv.type === 'rd') {
            unitsInput.value = 1;
            availUnits.textContent = 'Full Withdrawal';
            priceInput.value = inv.maturityAmount;
            unitsInput.parentElement.style.display = 'none'; // Lock unit selection
        } else {
            unitsInput.value = 1;
            availUnits.textContent = 'Full Sale';
            priceInput.value = inv.currentValue;
            unitsInput.parentElement.style.display = 'none';
        }
    }

    // Load accounts
    const accounts = JARVIS.get('accounts') || [];
    const accSelect = document.getElementById('sellAccount');
    if (accSelect) {
        accSelect.innerHTML = accounts.map(a => `<option value="${a.id}">${a.name} (₹${a.balance})</option>`).join('');
    }

    document.getElementById('sellModal').classList.add('active');
}

function closeSellModal() {
    document.getElementById('sellModal').classList.remove('active');
}

function handleSellSubmit(event) {
    event.preventDefault();
    const id = parseInt(document.getElementById('sellId').value);
    const inv = allInvestments.find(i => i.id === id);
    let unitsToSell = parseFloat(document.getElementById('sellUnits').value);
    const sellPrice = parseFloat(document.getElementById('sellPrice').value);
    const accountId = parseInt(document.getElementById('sellAccount').value);
    const date = document.getElementById('sellDate').value;

    if (!inv) return;

    // For non-unit types, assume units = 1
    if (inv.type !== 'mutual_fund' && inv.type !== 'stock') {
        unitsToSell = 1;
    }

    // Calculate total amount
    const totalAmount = unitsToSell * sellPrice;

    // Update Investment
    if (inv.type === 'mutual_fund') {
        if (unitsToSell > inv.units) {
            showNotification('Cannot sell more units than available!', 'error');
            return;
        }
        inv.units -= unitsToSell;
        inv.currentValue = inv.units * (inv.currentPrice || inv.buyPrice);
        inv.invested = (inv.invested / (inv.units + unitsToSell)) * inv.units;
    } else if (inv.type === 'stock') {
        if (unitsToSell > inv.quantity) {
            showNotification('Cannot sell more shares than available!', 'error');
            return;
        }
        inv.quantity -= unitsToSell;
        inv.currentValue = inv.quantity * (inv.currentPrice || inv.buyPrice);
    } else {
        // Full sell for others, remove investment?
        JARVIS.delete('investments', id);
        // Skip update call for inv since it's deleted, but need to handle below logic.
        // Actually for this prototype logic, let's just delete it.
        const accounts = JARVIS.get('accounts') || [];
        const acc = accounts.find(a => a.id === accountId);
        if (acc) {
            acc.balance += totalAmount;
            JARVIS.update('accounts', accountId, acc);
        }
        const transaction = {
            date: date,
            type: 'income',
            category: 'Investment Return',
            amount: totalAmount,
            account: acc ? acc.name : 'Unknown',
            description: `Sold ${inv.name}`
        };
        JARVIS.add('transactions', transaction);

        closeSellModal();
        loadInvestments();
        showNotification('Investment sold and transaction recorded!', 'success');
        return;
    }

    JARVIS.update('investments', id, inv);

    // Update Account
    const accounts = JARVIS.get('accounts') || [];
    const acc = accounts.find(a => a.id === accountId);
    if (acc) {
        acc.balance += totalAmount;
        JARVIS.update('accounts', accountId, acc);
    }

    // Record Transaction
    const transaction = {
        date: date,
        type: 'income',
        category: 'Investment Return',
        amount: totalAmount,
        account: acc ? acc.name : 'Unknown',
        description: `Sold ${unitsToSell} units of ${inv.name} @ ₹${sellPrice}`
    };
    JARVIS.add('transactions', transaction);

    closeSellModal();
    loadInvestments();
    showNotification('Investment sold and transaction recorded!', 'success');
}

function setMaxUnits() {
    const id = parseInt(document.getElementById('sellId').value);
    const inv = allInvestments.find(i => i.id === id);
    if (inv) {
        if (inv.type === 'mutual_fund') {
            document.getElementById('sellUnits').value = inv.units;
        } else if (inv.type === 'stock') {
            document.getElementById('sellUnits').value = inv.quantity;
        }
    }
}
