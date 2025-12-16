let accountsData = [];
let regularAccountsData = [];

document.addEventListener('DOMContentLoaded', async function () {
    await JARVIS.init();

    // Fetch regular accounts for dropdowns
    regularAccountsData = await JARVIS.request('GET', '/api/accounts') || [];

    loadAccounts();
    loadSIPs();

    // Check URL hash for tab
    const hash = window.location.hash;
    if (hash === '#sips') {
        switchTab('sips');
    }

    // Handle Add Account
    document.getElementById('add-account-form').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        JARVIS.post('/api/investment-accounts', data)
            .then(response => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('addAccountModal'));
                modal.hide();
                this.reset();
                JARVIS.showToast('Account created successfully', 'success');
                loadAccounts();
            })
            .catch(error => {
                console.error(error);
                JARVIS.showToast('Error creating account', 'error');
            });
    });

    // Handle Upload Holdings
    document.getElementById('upload-holdings-form').addEventListener('submit', function (e) {
        e.preventDefault();
        const accountId = document.getElementById('upload-account-id').value;
        const formData = new FormData(this);

        const statusDiv = document.getElementById('upload-status');
        const btn = this.querySelector('button[type="submit"]');

        statusDiv.classList.remove('d-none');
        statusDiv.textContent = 'Uploading and processing... This may take a moment.';
        btn.disabled = true;

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(`/api/investment-accounts/${accountId}/upload`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            }
        })
            .then(async response => {
                const res = await response.json();
                if (!response.ok) throw res;
                return res;
            })
            .then(data => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('uploadHoldingsModal'));
                modal.hide();
                this.reset();
                JARVIS.showToast(data.message || 'Holdings uploaded successfully', 'success');
                loadAccounts();
            })
            .catch(error => {
                console.error(error);
                JARVIS.showToast(error.message || 'Error uploading file', 'error');
            })
            .finally(() => {
                statusDiv.classList.add('d-none');
                btn.disabled = false;
            });
    });
});

function switchTab(tab) {
    // Buttons
    document.querySelectorAll('.nav-link').forEach(el => el.classList.remove('active'));

    // Panes
    document.querySelectorAll('.tab-pane').forEach(el => {
        el.classList.remove('show', 'active');
    });

    if (tab === 'accounts') {
        document.getElementById('pills-accounts-tab').classList.add('active');
        document.getElementById('pills-accounts').classList.add('show', 'active');
        window.location.hash = 'accounts';
    } else {
        document.getElementById('pills-sip-tab').classList.add('active');
        document.getElementById('pills-sip').classList.add('show', 'active');
        window.location.hash = 'sips';
        loadSIPs(); // Refresh SIPs when tab is clicked
    }
}

function loadAccounts() {
    const list = document.getElementById('investment-accounts-list');

    JARVIS.request('GET', '/api/investment-accounts')
        .then(accounts => {
            accountsData = accounts;
            list.innerHTML = '';
            if (accounts.length === 0) {
                list.innerHTML = '<div class="col-12 text-center text-muted">No investment accounts found. Create one to get started.</div>';
                return;
            }

            // Using Group Card Style (Grid similar to Investments Page)
            // But adapted for Accounts
            list.innerHTML = accounts.map(account => {
                const invested = parseFloat(account.total_invested) || 0;
                const current = parseFloat(account.current_value) || 0;
                const count = account.investments_count || 0;

                return `
                <div class="col-12 col-md-6 col-lg-4 mb-4">
                    <div class="investment-card">
                        <div class="investment-header">
                            <div>
                                <div class="investment-type">${account.broker}</div>
                                <div class="investment-name">${account.name}</div>
                                <div style="font-size: 0.8em; color: var(--text-muted);">${account.account_number || ''}</div>
                            </div>
                            <div class="dropdown">
                                <button class="action-btn" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#" onclick="openUploadModal(${account.id})"><i class="fas fa-file-upload me-2"></i> Upload Holdings</a></li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="investment-stats">
                            <div class="stat">
                                <span class="stat-label">Invested</span>
                                <span class="stat-value">${JARVIS.formatCurrency(invested)}</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label">Current Value</span>
                                <span class="stat-value" style="color: var(--success);">${JARVIS.formatCurrency(current)}</span>
                            </div>
                        </div>

                        <div style="margin-top: 1rem; padding-top: 0.5rem; border-top: 1px solid var(--bg-tertiary); display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.8rem; color: var(--text-muted);">${count} Investments</span>
                             <button class="btn btn-sm btn-outline-primary" onclick="openUploadModal(${account.id})">
                                <i class="fas fa-sync-alt"></i> Sync
                            </button>
                        </div>
                    </div>
                </div>
                `;
            }).join('');
        })
        .catch(error => {
            console.error(error);
            list.innerHTML = '<div class="col-12 text-center text-danger">Error loading accounts.</div>';
        });
}

async function loadSIPs() {
    const list = document.getElementById('sipList');
    if (!list) return;

    try {
        const investments = await JARVIS.request('GET', '/api/investments') || [];
        const sips = investments.filter(inv => inv.is_sip);

        if (sips.length === 0) {
            list.innerHTML = '<tr><td colspan="6" class="text-center p-4 text-muted">No active SIPs found.</td></tr>';
            return;
        }

        list.innerHTML = sips.map(sip => {
            const account = regularAccountsData.find(a => a.id === sip.source_account_id);
            const accountName = account ? account.name : 'Unknown Account';

            return `
                <tr style="background: var(--bg-secondary); transition: background 0.2s;">
                    <td style="padding: 1rem; border-top: 1px solid var(--border-color);">
                        <div style="font-weight: 600;">${sip.name}</div>
                        <small style="color: var(--text-muted);">${sip.symbol || '-'}</small>
                    </td>
                    <td style="padding: 1rem; border-top: 1px solid var(--border-color); color: var(--text-muted);">
                        ${accountName}
                    </td>
                    <td style="padding: 1rem; border-top: 1px solid var(--border-color); font-weight: 500;">
                        ${JARVIS.formatCurrency(sip.sip_amount || 0)}
                    </td>
                     <td style="padding: 1rem; border-top: 1px solid var(--border-color);">
                        Day ${sip.sip_date || '?'}
                    </td>
                    <td style="padding: 1rem; border-top: 1px solid var(--border-color);">
                         <span class="badge ${sip.sip_status === 'ACTIVE' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'}" style="font-size: 0.7em;">${sip.sip_status}</span>
                    </td>
                    <td style="padding: 1rem; border-top: 1px solid var(--border-color);">
                         <button class="action-btn" onclick="openEditSipModal(${sip.id})" title="Edit SIP">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                        <button class="action-btn" onclick="toggleSip(${sip.id}, '${sip.sip_status === 'ACTIVE' ? 'STOPPED' : 'ACTIVE'}')" title="${sip.sip_status === 'ACTIVE' ? 'Stop' : 'Start'} SIP">
                            <i class="fas fa-${sip.sip_status === 'ACTIVE' ? 'stop-circle' : 'play-circle'}" style="color: ${sip.sip_status === 'ACTIVE' ? 'var(--danger)' : 'var(--success)'}"></i>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');

    } catch (e) {
        console.error("Failed to load SIPs", e);
        list.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading SIPs.</td></tr>';
    }
}

window.openUploadModal = function (accountId) {
    document.getElementById('upload-account-id').value = accountId;
    const modal = new bootstrap.Modal(document.getElementById('uploadHoldingsModal'));
    modal.show();
}

// SIP Management Functions (Reused/Adapted)
let allSips = []; // Cache if needed, currently fetching fresh

function openEditSipModal(id) {
    // We need to fetch the single investment details or find from cache
    // Since we don't have a global cache here like investments.js, fetch fresh or find from list if possible
    // Let's simplified fetch single
    JARVIS.request('GET', `/api/investments/${id}`).then(inv => {
        document.getElementById('editSipId').value = inv.id;
        document.getElementById('editSipName').value = inv.name;
        document.getElementById('editSipAmount').value = inv.sip_amount;
        document.getElementById('editSipDate').value = inv.sip_date;

        const accountSelect = document.getElementById('editSipAccount');
        accountSelect.innerHTML = regularAccountsData
            .filter(acc => acc.type === 'bank' || acc.type === 'savings')
            .map(acc => `<option value="${acc.id}" ${inv.source_account_id === acc.id ? 'selected' : ''}>${acc.name} (₹${JARVIS.formatCurrency(acc.balance)})</option>`)
            .join('');

        document.getElementById('editSipModal').classList.add('active');
    });
}

function closeEditSipModal() {
    document.getElementById('editSipModal').classList.remove('active');
}

async function handleEditSipSubmit(event) {
    event.preventDefault();
    const id = document.getElementById('editSipId').value;
    const amount = document.getElementById('editSipAmount').value;
    const date = document.getElementById('editSipDate').value;
    const accountId = document.getElementById('editSipAccount').value;

    try {
        await JARVIS.request('PUT', `/api/investments/${id}`, {
            sip_amount: amount,
            sip_date: date,
            source_account_id: accountId
        });

        closeEditSipModal();
        loadSIPs();
        JARVIS.showToast('SIP Updated Successfully', 'success');
    } catch (e) {
        console.error(e);
        JARVIS.showToast('Failed to update SIP', 'error');
    }
}

async function toggleSip(id, status) {
    if (!confirm(`Are you sure you want to ${status === 'ACTIVE' ? 'restart' : 'stop'} this SIP?`)) return;

    try {
        await JARVIS.request('PUT', `/api/investments/${id}`, {
            sip_status: status
        });
        loadSIPs();
        JARVIS.showToast(`SIP ${status === 'ACTIVE' ? 'Restarted' : 'Stopped'}`, 'success');
    } catch (e) {
        console.error(e);
        JARVIS.showToast('Failed to update SIP status', 'error');
    }
}
