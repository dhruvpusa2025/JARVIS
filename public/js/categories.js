document.addEventListener('DOMContentLoaded', async function () {
    await JARVIS.init();
    loadCategories();
});

const commonIcons = [
    'fa-briefcase', 'fa-utensils', 'fa-car', 'fa-shopping-bag', 'fa-file-invoice-dollar',
    'fa-film', 'fa-home', 'fa-plane', 'fa-heartbeat', 'fa-graduation-cap',
    'fa-gift', 'fa-gamepad', 'fa-mobile-alt', 'fa-wifi', 'fa-paw',
    'fa-tshirt', 'fa-tools', 'fa-book', 'fa-music', 'fa-dumbbell',
    'fa-coffee', 'fa-beer', 'fa-hamburger', 'fa-pizza-slice', 'fa-bus',
    'fa-taxi', 'fa-subway', 'fa-bicycle', 'fa-walking', 'fa-gas-pump'
];

function loadCategories() {
    const categories = JARVIS.get('categories') || [];
    const income = categories.filter(c => c.type === 'income');
    const expense = categories.filter(c => c.type === 'expense');

    document.getElementById('incomeCategories').innerHTML = income.length > 0 ? income.map(cat => `
        <div class="category-item">
            <div class="category-icon" style="background: ${cat.color}20; color: ${cat.color}">
                <i class="fas ${cat.icon}"></i>
            </div>
            <div class="category-info">
                <div style="font-weight: 600;">${cat.name}</div>
            </div>
            ${!cat.is_system ? `<button class="btn-icon-small" onclick="deleteCategory(${cat.id})"><i class="fas fa-trash"></i></button>` : ''}
        </div>
    `).join('') : '<p style="color: var(--text-muted); text-align: center; padding: 2rem;">No income categories</p>';

    document.getElementById('expenseCategories').innerHTML = expense.length > 0 ? expense.map(cat => `
        <div class="category-item">
            <div class="category-icon" style="background: ${cat.color}20; color: ${cat.color}">
                <i class="fas ${cat.icon}"></i>
            </div>
            <div class="category-info">
                <div style="font-weight: 600;">${cat.name}</div>
            </div>
            ${!cat.is_system ? `<button class="btn-icon-small" onclick="deleteCategory(${cat.id})"><i class="fas fa-trash"></i></button>` : ''}
        </div>
    `).join('') : '<p style="color: var(--text-muted); text-align: center; padding: 2rem;">No expense categories</p>';
}

function openAddCategoryModal() {
    const grid = document.getElementById('iconGrid');
    grid.innerHTML = commonIcons.map(icon => `
        <div class="icon-option" onclick="selectIcon('${icon}')">
            <i class="fas ${icon}"></i>
        </div>
    `).join('');
    document.getElementById('addCategoryModal').classList.add('active');
}

function selectIcon(icon) {
    document.getElementById('selectedIcon').value = icon;
    document.querySelectorAll('.icon-option').forEach(el => el.classList.remove('selected'));
    event.currentTarget.classList.add('selected');
}

function closeAddCategoryModal() {
    document.getElementById('addCategoryModal').classList.remove('active');
}

async function handleAddCategory(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    const category = {
        name: formData.get('name'),
        type: formData.get('type'),
        icon: formData.get('icon'),
        color: formData.get('color'),
        is_system: false
    };

    try {
        await JARVIS.add('categories', category);
        closeAddCategoryModal();
        loadCategories();
        showNotification('Category added successfully!', 'success');
        event.target.reset();
    } catch (error) {
        showNotification('Failed to add category', 'error');
    }
}

async function deleteCategory(id) {
    if (confirm('Are you sure you want to delete this category?')) {
        try {
            await JARVIS.delete('categories', id);
            loadCategories();
            showNotification('Category deleted!', 'success');
        } catch (error) {
            showNotification('Failed to delete category', 'error');
        }
    }
}
