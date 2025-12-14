// JARVIS API Client Adapter
const JARVIS = {
    state: {
        accounts: [],
        transactions: [],
        investments: [],
        loans: [],
        lendings: [],
        categories: []
    },

    async init() {
        const endpoints = ['accounts', 'transactions', 'investments', 'categories', 'loans', 'lendings'];

        await Promise.all(endpoints.map(async (endpoint) => {
            try {
                this.state[endpoint] = await this.fetchData(endpoint);
            } catch (error) {
                console.error(`Failed to load ${endpoint}:`, error);
                // Keep default empty array
            }
        }));

        // Trigger any listeners if we add them later
        console.log('JARVIS Initialized with API Data');
        return true;
    },

    async fetchData(endpoint) {
        const response = await fetch(`/api/${endpoint}`, {
            headers: {
                'Accept': 'application/json'
            }
        });
        if (!response.ok) throw new Error(`Failed to fetch ${endpoint}`);
        return await response.json();
    },

    // Synchronous Get (reads from state)
    get(key) {
        return this.state[key] || [];
    },

    // Async Add
    async add(key, item) {
        try {
            const response = await fetch(`/api/${key}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(item)
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message || 'Failed to add item');
            }

            const newItem = await response.json();
            this.state[key].push(newItem);
            return newItem;
        } catch (error) {
            console.error(`Error adding to ${key}:`, error);
            showNotification(error.message, 'error');
            throw error;
        }
    },

    // Async Update
    async update(key, id, updates) {
        try {
            const response = await fetch(`/api/${key}/${id}`, {
                method: 'PUT', // or PATCH
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(updates)
            });

            if (!response.ok) throw new Error('Failed to update item');

            const updatedItem = await response.json();
            const index = this.state[key].findIndex(item => item.id === id);
            if (index !== -1) {
                this.state[key][index] = updatedItem;
            }
            return updatedItem;
        } catch (error) {
            console.error(`Error updating ${key}:`, error);
            showNotification(error.message, 'error');
            throw error;
        }
    },

    // Async Delete
    async delete(key, id) {
        try {
            const response = await fetch(`/api/${key}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Failed to delete item');

            this.state[key] = this.state[key].filter(item => item.id !== id);
            return true;
        } catch (error) {
            console.error(`Error deleting from ${key}:`, error);
            showNotification(error.message, 'error');
            throw error;
        }
    },

    // Helper: Formatter
    formatCurrency(amount) {
        return new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: 'INR',
            maximumFractionDigits: 0
        }).format(amount);
    },

    formatDate(dateString) {
        const date = new Date(dateString);
        return new Intl.DateTimeFormat('en-IN', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        }).format(date);
    },

    // Calculations (Sync, based on state)
    getTotalBalance() {
        return this.state.accounts.reduce((sum, acc) => sum + parseFloat(acc.balance), 0);
    },

    getMonthlyIncome() {
        const currentMonth = new Date().getMonth();
        return this.state.transactions
            .filter(t => {
                const date = new Date(t.date);
                return t.type === 'income' && date.getMonth() === currentMonth;
            })
            .reduce((sum, t) => sum + parseFloat(t.amount), 0);
    },

    getMonthlyExpenses() {
        const currentMonth = new Date().getMonth();
        return this.state.transactions
            .filter(t => {
                const date = new Date(t.date);
                return t.type === 'expense' && date.getMonth() === currentMonth;
            })
            .reduce((sum, t) => sum + parseFloat(t.amount), 0);
    },

    getTotalInvestments() {
        return this.state.investments.reduce((sum, inv) => sum + (parseFloat(inv.current_value) || parseFloat(inv.invested_amount) || 0), 0);
    },

    getExpenseBreakdown() {
        const breakdown = {};
        const currentMonth = new Date().getMonth();
        this.state.transactions
            .filter(t => t.type === 'expense' && new Date(t.date).getMonth() === currentMonth)
            .forEach(t => {
                const catName = t.category ? t.category.name : (t.category_id || 'Uncategorized');
                breakdown[catName] = (breakdown[catName] || 0) + parseFloat(t.amount);
            });
        return breakdown;
    },

    getRecentTransactions(limit = 5) {
        return [...this.state.transactions]
            .sort((a, b) => new Date(b.date) - new Date(a.date))
            .slice(0, limit);
    },
    async request(method, url, data = null) {
        const options = {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        };
        if (data) options.body = JSON.stringify(data);

        const response = await fetch(url, options);
        if (!response.ok) {
            const res = await response.json().catch(() => ({}));
            throw new Error(res.message || 'Request failed');
        }
        return await response.json();
    },

    post(url, data) {
        return this.request('POST', url, data);
    }
};
