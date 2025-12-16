let currentFilter = null; // null = No selection, 'all' = All, number = Account ID
let allInvestments = [];
let allAccounts = [];
let searchQuery = '';
let sortBy = 'value_desc';

document.addEventListener('DOMContentLoaded', async function () {
    await JARVIS.init();
    loadData();

    // Event Listeners
    document.getElementById('investmentSearch').addEventListener('input', (e) => {
        searchQuery = e.target.value.toLowerCase();
        renderTable();
    });

    document.getElementById('sortBy').addEventListener('change', (e) => {
        sortBy = e.target.value;
        renderTable();
    });

    const sellDateInput = document.getElementById('sellDate');
    if (sellDateInput) {
        sellDateInput.valueAsDate = new Date();
    }
});

async function loadData() {
    try {
        // Fetch accounts first to map names
        allAccounts = await JARVIS.request('GET', '/api/investment-accounts');
    } catch (e) {
        console.error("Failed to load investment accounts", e);
        allAccounts = [];
    }

    // Then fetch investments, now including account relation if possible, or we map manually
    // The API returns 'investment_account_id', we can map it to account object
    let investmentsData = await JARVIS.get('investments') || [];

    // Manual mapping of account object to investment for display
    allInvestments = investmentsData.map(inv => {
        inv.account = allAccounts.find(a => a.id === inv.investment_account_id);
        return inv;
    });

    updateSummary(allInvestments);
    renderGroupCards();
    renderTable();
}

function updateSummary(investments) {
    const totalInvested = investments.reduce((sum, inv) => sum + (parseFloat(inv.invested_amount) || parseFloat(inv.amount) || 0), 0);
    const currentValue = investments.reduce((sum, inv) => sum + (parseFloat(inv.current_value) || parseFloat(inv.amount) || 0), 0);

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

function renderGroupCards() {
    // Group by Accounts
    const accountStats = {
        'all': { name: 'All Accounts', amount: 0, count: 0, id: 'all' }
    };

    // Initialize all accounts with 0
    allAccounts.forEach(acc => {
        accountStats[acc.id] = { name: acc.name, amount: 0, count: 0, id: acc.id };
    });

    // Aggregate stats
    allInvestments.forEach(inv => {
        const val = parseFloat(inv.current_value) || parseFloat(inv.amount) || 0;

        // Add to global
        accountStats.all.amount += val;
        accountStats.all.count++;

        // Add to specific account if exists
        if (inv.investment_account_id && accountStats[inv.investment_account_id]) {
            accountStats[inv.investment_account_id].amount += val;
            accountStats[inv.investment_account_id].count++;
        }
    });

    const container = document.getElementById('groupCards');
    if (container) {
        // Order: Specific Accounts first, then "All Accounts" at last
        const specificCards = allAccounts.map(acc => accountStats[acc.id]);
        const cards = [...specificCards, accountStats.all];

        container.innerHTML = cards.map(data => {
            return `
            <div class="group-card ${currentFilter == data.id ? 'active' : ''}" onclick="filterByAccount('${data.id}')">
                <h3>${data.name}</h3>
                <div class="amount">${JARVIS.formatCurrency(data.amount)}</div>
                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">${data.count} items</div>
            </div>
            `;
        }).join('');
    }
}

function filterByAccount(accountId) {
    let newFilter = accountId === 'all' ? 'all' : parseInt(accountId);
    currentFilter = newFilter;
    renderGroupCards();
    renderTable();
}

function renderTable() {
    const list = document.getElementById('investmentsList');
    const summaryCard = document.getElementById('filteredSummary');

    if (!list) return;

    if (currentFilter === null) {
        list.innerHTML = '<tr><td colspan="9" class="text-center p-5 text-muted"><i class="fas fa-arrow-up mb-3" style="font-size: 2rem;"></i><br>Please select an <strong>Investment Account</strong> above to view holdings.</td></tr>';
        if (summaryCard) summaryCard.style.display = 'none';
        return;
    }

    // Filter
    let filtered = allInvestments.filter(inv => {
        // Account Filter
        if (currentFilter !== 'all' && inv.investment_account_id !== currentFilter) {
            return false;
        }

        // Search Filter
        if (searchQuery) {
            const name = (inv.name || '').toLowerCase();
            const symbol = (inv.symbol || '').toLowerCase();
            const accountName = (inv.account ? inv.account.name : '').toLowerCase();
            return name.includes(searchQuery) || symbol.includes(searchQuery) || accountName.includes(searchQuery);
        }

        return true;
    });

    // --- Calculate Filtered Summary ---
    if (summaryCard) {
        summaryCard.style.display = 'block';

        const fInvested = filtered.reduce((s, i) => s + (parseFloat(i.invested_amount) || 0), 0);
        const fCurrent = filtered.reduce((s, i) => s + (parseFloat(i.current_value) || 0), 0);
        const fReturn = fCurrent - fInvested;
        const fReturnPct = fInvested > 0 ? (fReturn / fInvested) * 100 : 0;

        document.getElementById('fsInvested').textContent = JARVIS.formatCurrency(fInvested);
        document.getElementById('fsCurrent').textContent = JARVIS.formatCurrency(fCurrent);

        const elReturn = document.getElementById('fsReturn');
        elReturn.textContent = `${fReturn >= 0 ? '+' : ''}${JARVIS.formatCurrency(fReturn)}`;
        elReturn.className = fReturn >= 0 ? 'text-success' : 'text-danger';

        const elReturnPct = document.getElementById('fsReturnPct');
        elReturnPct.textContent = `${fReturn >= 0 ? '+' : ''}${fReturnPct.toFixed(2)}%`;
        elReturnPct.className = fReturn >= 0 ? 'text-success' : 'text-danger';
    }
    // ----------------------------------

    // Sort
    filtered.sort((a, b) => {
        const valA = parseFloat(a.current_value) || 0;
        const valB = parseFloat(b.current_value) || 0;
        const retA = (parseFloat(a.current_value) || 0) - (parseFloat(a.invested_amount) || 0);
        const retB = (parseFloat(b.current_value) || 0) - (parseFloat(b.invested_amount) || 0);
        const nameA = (a.name || '').toLowerCase();
        const nameB = (b.name || '').toLowerCase();

        switch (sortBy) {
            case 'name': return nameA.localeCompare(nameB);
            case 'value_desc': return valB - valA;
            case 'value_asc': return valA - valB;
            case 'return_desc': return retB - retA;
            case 'return_asc': return retA - retB;
            default: return 0;
        }
    });

    if (filtered.length === 0) {
        list.innerHTML = '<tr><td colspan="9" class="text-center p-4 text-muted">No investments found matching your criteria.</td></tr>';
        return;
    }

    list.innerHTML = filtered.map(inv => {
        const invested = parseFloat(inv.invested_amount) || 0;
        const current = parseFloat(inv.current_value) || 0;
        const profit = current - invested;
        const profitPct = invested > 0 ? (profit / invested) * 100 : 0;
        const units = parseFloat(inv.units) || parseFloat(inv.quantity) || 0;

        const buyPrice = parseFloat(inv.buy_price) || 0;
        const currentPrice = parseFloat(inv.current_price) || 0;

        const accountName = inv.account ? inv.account.name : '-';
        const sipBadge = inv.is_sip ?
            `<span class="badge ${inv.sip_status === 'ACTIVE' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'}" style="font-size: 0.7em;">SIP ${inv.sip_status}</span>`
            : '';

        return `
            <tr style="background: var(--bg-secondary); transition: background 0.2s;">
                <td style="padding: 1rem; border-top: 1px solid var(--border-color); border-radius: var(--radius-md) 0 0 var(--radius-md);">
                    <a href="/investments/${inv.id}" style="font-weight: 600; text-decoration: none; color: inherit;">${inv.name}</a>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <small style="color: var(--text-muted);">${inv.symbol || formatType(inv.type)}</small>
                        ${sipBadge}
                    </div>
                </td>
                <td style="padding: 1rem; border-top: 1px solid var(--border-color); font-size: 0.9em; color: var(--text-muted);">${accountName}</td>
                <td style="padding: 1rem; border-top: 1px solid var(--border-color);">${units > 0 ? units.toFixed(2) : '-'}</td>
                <td style="padding: 1rem; border-top: 1px solid var(--border-color);">₹${buyPrice > 0 ? buyPrice.toFixed(2) : '-'}</td>
                <td style="padding: 1rem; border-top: 1px solid var(--border-color);">₹${currentPrice > 0 ? currentPrice.toFixed(2) : '-'}</td>
                <td style="padding: 1rem; border-top: 1px solid var(--border-color);">${JARVIS.formatCurrency(invested)}</td>
                <td style="padding: 1rem; border-top: 1px solid var(--border-color); font-weight: 500;">${JARVIS.formatCurrency(current)}</td>
                <td style="padding: 1rem; border-top: 1px solid var(--border-color);">
                    <span class="${profit >= 0 ? 'text-success' : 'text-danger'}">
                        ${profit >= 0 ? '+' : ''}${JARVIS.formatCurrency(profit)}
                    </span>
                </td>
                <td style="padding: 1rem; border-top: 1px solid var(--border-color);">
                    <span class="badge ${profit >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'}" style="padding: 0.25rem 0.5rem; border-radius: 4px;">
                        ${profit >= 0 ? '+' : ''}${profitPct.toFixed(2)}%
                    </span>
                </td>
                <td style="padding: 1rem; border-top: 1px solid var(--border-color); border-radius: 0 var(--radius-md) var(--radius-md) 0;">
                    <button class="action-btn" onclick="openEditModal(${inv.id})"><i class="fas fa-pencil-alt"></i></button>
                    ${inv.is_sip ? `
                        <button class="action-btn" onclick="toggleSip(${inv.id}, '${inv.sip_status === 'ACTIVE' ? 'STOPPED' : 'ACTIVE'}')" title="${inv.sip_status === 'ACTIVE' ? 'Stop' : 'Start'} SIP">
                            <i class="fas fa-${inv.sip_status === 'ACTIVE' ? 'stop-circle' : 'play-circle'}" style="color: ${inv.sip_status === 'ACTIVE' ? 'var(--danger)' : 'var(--success)'}"></i>
                        </button>
                    ` : `
                        <button class="action-btn" onclick="openSipModal(${inv.id})" title="Start SIP">
                           <i class="fas fa-calendar-plus" style="color: var(--primary-color);"></i>
                        </button>
                    `}
                </td>
            </tr>
        `;
    }).join('');
}


function formatType(type) {
    const types = {
        'mutual_fund': 'Mutual Fund',
        'stock': 'Stock',
        'fd': 'Fixed Deposit',
        'rd': 'Recurring Deposit',
        'real_estate': 'Real Estate',
        'MF': 'Mutual Fund',
        'STOCK': 'Stock'
    };
    return types[type] || type;
}

// Edit Modal Functions (Price Only)
function openEditModal(id) {
    const inv = allInvestments.find(i => i.id === id);
    if (!inv) return;

    document.getElementById('editId').value = inv.id;
    document.getElementById('editName').value = inv.name;

    // Populate fields based on type
    const priceInput = document.getElementById('editCurrentPrice');
    priceInput.value = inv.current_price || inv.buy_price || inv.current_value;

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

        // Optimistic update logic
        if (inv.type === 'mutual_fund' || inv.type === 'MF') {
            inv.current_price = newPrice;
            inv.current_value = inv.units * inv.current_price;
        } else if (inv.type === 'stock' || inv.type === 'STOCK') {
            inv.current_price = newPrice;
            inv.current_value = inv.units * inv.current_price;
        } else {
            inv.current_value = newPrice;
        }

        // Send snake_case to API
        const updates = {
            current_price: inv.current_price,
            current_value: inv.current_value
        };

        JARVIS.update('investments', id, updates);
        closeEditModal();
        loadData(); // Reload to refresh table
        JARVIS.showToast('Investment value updated successfully', 'success');
    }
}

function closeSipModal() {
    document.getElementById('sipModal').classList.remove('active');
}

function openSipModal(id) {
    const inv = allInvestments.find(i => i.id === id);
    if (!inv) return;

    document.getElementById('sipId').value = inv.id;
    document.getElementById('sipName').value = inv.name;
    document.getElementById('sipAmount').value = inv.sip_amount || 1000;

    // Populate Accounts
    const accountSelect = document.getElementById('sipAccount');
    accountSelect.innerHTML = allAccounts
        .filter(acc => acc.type === 'bank' || acc.type === 'savings')
        .map(acc => `<option value="${acc.id}" ${inv.source_account_id === acc.id ? 'selected' : ''}>${acc.name} (₹${JARVIS.formatCurrency(acc.balance)})</option>`)
        .join('');

    document.getElementById('sipModal').classList.add('active');
}

async function handleSipSubmit(event) {
    event.preventDefault();
    const id = document.getElementById('sipId').value;
    const amount = document.getElementById('sipAmount').value;
    const accountId = document.getElementById('sipAccount').value;
    const date = document.getElementById('sipDate').value;

    try {
        await JARVIS.request('PUT', `/api/investments/${id}`, {
            is_sip: true,
            sip_status: 'ACTIVE',
            sip_amount: amount,
            source_account_id: accountId,
            sip_date: date,
            sip_frequency: 'MONTHLY'
        });

        closeSipModal();
        loadData();
        JARVIS.showToast('SIP Setup Successfully!', 'success');
    } catch (e) {
        console.error(e);
        JARVIS.showToast('Failed to setup SIP', 'error');
    }
}

async function toggleSip(id, status) {
    if (!confirm(`Are you sure you want to ${status === 'ACTIVE' ? 'restart' : 'stop'} this SIP?`)) return;

    try {
        await JARVIS.request('PUT', `/api/investments/${id}`, {
            sip_status: status
        });
        loadData(); // Refresh list
        JARVIS.showToast(`SIP ${status === 'ACTIVE' ? 'Restarted' : 'Stopped'}`, 'success');
    } catch (e) {
        console.error(e);
        JARVIS.showToast('Failed to update SIP status', 'error');
    }
}
