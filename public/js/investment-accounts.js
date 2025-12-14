document.addEventListener('DOMContentLoaded', function () {
    loadAccounts();

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

        // Use fetch directly for file upload to handle FormData correctly if JARVIS wrapper has issues with plain FormData
        // But JARVIS.post usually handles JSON. For file upload we need multipart.
        // Let's assume we need a raw fetch or extend JARVIS.

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
                loadAccounts(); // Refresh stats
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

function loadAccounts() {
    const list = document.getElementById('investment-accounts-list');

    JARVIS.get('/api/investment-accounts')
        .then(accounts => {
            list.innerHTML = '';
            if (accounts.length === 0) {
                list.innerHTML = '<div class="col-12 text-center text-muted">No investment accounts found. Create one to get started.</div>';
                return;
            }

            accounts.forEach(account => {
                const card = document.createElement('div');
                card.className = 'col-md-4 mb-3';
                card.innerHTML = `
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title d-flex justify-content-between">
                                ${account.name}
                                <span class="badge bg-secondary">${account.broker}</span>
                            </h5>
                            <p class="card-text text-muted mb-1">${account.account_number || 'No Account ID'}</p>
                            <p class="card-text">
                                <strong>${account.investments_count || 0}</strong> Investments
                            </p>
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary btn-sm" onclick="openUploadModal(${account.id})">
                                    <i class="fas fa-file-upload"></i> Upload Holdings
                                </button>
                                <!-- <a href="/investments?account=${account.id}" class="btn btn-outline-secondary btn-sm">View Investments</a> -->
                            </div>
                        </div>
                    </div>
                `;
                list.appendChild(card);
            });
        })
        .catch(error => {
            console.error(error);
            list.innerHTML = '<div class="col-12 text-center text-danger">Error loading accounts.</div>';
        });
}

window.openUploadModal = function (accountId) {
    document.getElementById('upload-account-id').value = accountId;
    const modal = new bootstrap.Modal(document.getElementById('uploadHoldingsModal'));
    modal.show();
}
