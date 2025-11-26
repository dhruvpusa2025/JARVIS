// Mock Data Storage using localStorage
const JARVIS = {
    // Initialize default data
    init() {
        if (!localStorage.getItem('jarvis_initialized')) {
            this.setDefaultData();
            localStorage.setItem('jarvis_initialized', 'true');
        } else {
            // Check for missing data (e.g. investments added later)
            const investments = this.get('investments');
            if (!investments || investments.length === 0) {
                const defaultData = {
                    investments: [
                        { id: 1, type: 'mutual_fund', name: 'HDFC Balanced Advantage Fund', units: 1250, buyPrice: 80, currentPrice: 85.50, invested: 100000, currentValue: 106875 },
                        { id: 2, type: 'stock', name: 'Reliance Industries', quantity: 10, buyPrice: 2500, currentPrice: 2650, invested: 25000, currentValue: 26500 },
                        { id: 3, type: 'fd', name: 'HDFC Fixed Deposit', amount: 100000, interestRate: 7, maturityDate: '2026-11-26', maturityAmount: 107000 },
                        { id: 4, type: 'real_estate', name: 'Plot in Sector 45', purchasePrice: 5000000, currentValue: 6000000, purchaseDate: '2024-01-01' }
                    ]
                };
                this.set('investments', defaultData.investments);
            }

            const loans = this.get('loans');
            if (!loans || loans.length === 0) {
                const defaultData = {
                    loans: [
                        { id: 1, type: 'home', lender: 'HDFC Bank', principal: 5000000, interestRate: 8.5, emiAmount: 43391, emiDay: 10, outstanding: 4950000, startDate: '2024-01-01' }
                    ]
                };
                this.set('loans', defaultData.loans);
            }

            const lendings = this.get('lendings');
            if (!lendings || lendings.length === 0) {
                const defaultData = {
                    lendings: [
                        { id: 1, borrower: 'Rajesh', amount: 50000, interestRate: 2, frequency: 'monthly', outstanding: 40000, startDate: '2025-06-01', nextInterestDate: '2025-12-01' },
                        { id: 2, borrower: 'Amit', amount: 15000, interestRate: 0, frequency: 'none', outstanding: 15000, startDate: '2025-11-15', returnDate: '2026-01-15' }
                    ]
                };
                this.set('lendings', defaultData.lendings);
            }
        }
    },

    // Set default mock data
    setDefaultData() {
        const defaultData = {
            accounts: [
                { id: 1, name: 'HDFC Bank', type: 'bank', balance: 45000, accountNumber: '****1234' },
                { id: 2, name: 'SBI Bank', type: 'bank', balance: 32000, accountNumber: '****5678' },
                { id: 3, name: 'Cash', type: 'cash', balance: 15000 },
                { id: 4, name: 'HDFC Credit Card', type: 'credit_card', balance: -8000, creditLimit: 50000 }
            ],
            transactions: [
                { id: 1, date: '2025-11-26', type: 'income', category: 'Salary', amount: 80000, account: 'HDFC Bank', description: 'Monthly salary' },
                { id: 2, date: '2025-11-25', type: 'expense', category: 'Food', amount: 1200, account: 'Cash', description: 'Dinner at restaurant' },
                { id: 3, date: '2025-11-24', type: 'expense', category: 'Transport', amount: 500, account: 'HDFC Bank', description: 'Uber ride' },
                { id: 4, date: '2025-11-23', type: 'expense', category: 'Shopping', amount: 3500, account: 'HDFC Credit Card', description: 'Clothes' },
                { id: 5, date: '2025-11-22', type: 'expense', category: 'Bills', amount: 2000, account: 'HDFC Bank', description: 'Electricity bill' },
                { id: 6, date: '2025-11-20', type: 'expense', category: 'Food', amount: 800, account: 'Cash', description: 'Groceries' },
                { id: 7, date: '2025-11-18', type: 'expense', category: 'Entertainment', amount: 1500, account: 'HDFC Credit Card', description: 'Movie tickets' },
                { id: 8, date: '2025-11-15', type: 'expense', category: 'Transport', amount: 2000, account: 'HDFC Bank', description: 'Fuel' }
            ],
            investments: [
                { id: 1, type: 'mutual_fund', name: 'HDFC Balanced Advantage Fund', units: 1250, buyPrice: 80, currentPrice: 85.50, invested: 100000, currentValue: 106875 },
                { id: 2, type: 'stock', name: 'Reliance Industries', quantity: 10, buyPrice: 2500, currentPrice: 2650, invested: 25000, currentValue: 26500 },
                { id: 3, type: 'fd', name: 'HDFC Fixed Deposit', amount: 100000, interestRate: 7, maturityDate: '2026-11-26', maturityAmount: 107000 },
                { id: 4, type: 'real_estate', name: 'Plot in Sector 45', purchasePrice: 5000000, currentValue: 6000000, purchaseDate: '2024-01-01' }
            ],
            loans: [
                { id: 1, type: 'home', lender: 'HDFC Bank', principal: 5000000, interestRate: 8.5, emiAmount: 43391, emiDay: 10, outstanding: 4950000, startDate: '2024-01-01' }
            ],
            lendings: [
                { id: 1, borrower: 'Rajesh', amount: 50000, interestRate: 2, frequency: 'monthly', outstanding: 40000, startDate: '2025-06-01', nextInterestDate: '2025-12-01' },
                { id: 2, borrower: 'Amit', amount: 15000, interestRate: 0, frequency: 'none', outstanding: 15000, startDate: '2025-11-15', returnDate: '2026-01-15' }
            ],
            categories: [
                { id: 1, name: 'Salary', type: 'income', icon: 'fa-briefcase', color: '#10b981' },
                { id: 2, name: 'Food', type: 'expense', icon: 'fa-utensils', color: '#f59e0b' },
                { id: 3, name: 'Transport', type: 'expense', icon: 'fa-car', color: '#3b82f6' },
                { id: 4, name: 'Shopping', type: 'expense', icon: 'fa-shopping-bag', color: '#8b5cf6' },
                { id: 5, name: 'Bills', type: 'expense', icon: 'fa-file-invoice', color: '#ef4444' },
                { id: 6, name: 'Entertainment', type: 'expense', icon: 'fa-film', color: '#ec4899' }
            ]
        };

        Object.keys(defaultData).forEach(key => {
            localStorage.setItem(`jarvis_${key}`, JSON.stringify(defaultData[key]));
        });
    },

    // Get data
    get(key) {
        const data = localStorage.getItem(`jarvis_${key}`);
        return data ? JSON.parse(data) : null;
    },

    // Set data
    set(key, value) {
        localStorage.setItem(`jarvis_${key}`, JSON.stringify(value));
    },

    // Add item
    add(key, item) {
        const data = this.get(key) || [];
        const newId = data.length > 0 ? Math.max(...data.map(d => d.id)) + 1 : 1;
        item.id = newId;
        data.push(item);
        this.set(key, data);
        return item;
    },

    // Update item
    update(key, id, updates) {
        const data = this.get(key) || [];
        const index = data.findIndex(item => item.id === id);
        if (index !== -1) {
            data[index] = { ...data[index], ...updates };
            this.set(key, data);
            return data[index];
        }
        return null;
    },

    // Delete item
    delete(key, id) {
        const data = this.get(key) || [];
        const filtered = data.filter(item => item.id !== id);
        this.set(key, filtered);
        return true;
    },

    // Calculate total balance
    getTotalBalance() {
        const accounts = this.get('accounts') || [];
        return accounts.reduce((sum, acc) => sum + acc.balance, 0);
    },

    // Get monthly income
    getMonthlyIncome() {
        const transactions = this.get('transactions') || [];
        const currentMonth = new Date().getMonth();
        const currentYear = new Date().getFullYear();

        return transactions
            .filter(t => {
                const date = new Date(t.date);
                return t.type === 'income' &&
                    date.getMonth() === currentMonth &&
                    date.getFullYear() === currentYear;
            })
            .reduce((sum, t) => sum + t.amount, 0);
    },

    // Get monthly expenses
    getMonthlyExpenses() {
        const transactions = this.get('transactions') || [];
        const currentMonth = new Date().getMonth();
        const currentYear = new Date().getFullYear();

        return transactions
            .filter(t => {
                const date = new Date(t.date);
                return t.type === 'expense' &&
                    date.getMonth() === currentMonth &&
                    date.getFullYear() === currentYear;
            })
            .reduce((sum, t) => sum + t.amount, 0);
    },

    // Get total investments value
    getTotalInvestments() {
        const investments = this.get('investments') || [];
        return investments.reduce((sum, inv) => sum + (inv.currentValue || inv.amount || 0), 0);
    },

    // Get expense breakdown by category
    getExpenseBreakdown() {
        const transactions = this.get('transactions') || [];
        const currentMonth = new Date().getMonth();
        const currentYear = new Date().getFullYear();

        const breakdown = {};
        transactions
            .filter(t => {
                const date = new Date(t.date);
                return t.type === 'expense' &&
                    date.getMonth() === currentMonth &&
                    date.getFullYear() === currentYear;
            })
            .forEach(t => {
                breakdown[t.category] = (breakdown[t.category] || 0) + t.amount;
            });

        return breakdown;
    },

    // Get recent transactions
    getRecentTransactions(limit = 5) {
        const transactions = this.get('transactions') || [];
        return transactions
            .sort((a, b) => new Date(b.date) - new Date(a.date))
            .slice(0, limit);
    },

    // Format currency
    formatCurrency(amount) {
        return new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: 'INR',
            maximumFractionDigits: 0
        }).format(amount);
    },

    // Format date
    formatDate(dateString) {
        const date = new Date(dateString);
        return new Intl.DateTimeFormat('en-IN', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        }).format(date);
    }
};

// Initialize on load
JARVIS.init();
