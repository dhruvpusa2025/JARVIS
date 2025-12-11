<div class="mobile-nav">
    <a href="{{ route('dashboard') }}" class="mobile-nav-item {{ Request::routeIs('dashboard') ? 'active' : '' }}">
        <i class="fas fa-home"></i><span>Home</span>
    </a>
    <a href="{{ route('transactions.create') }}"
        class="mobile-nav-item {{ Request::routeIs('transactions.create') ? 'active' : '' }}">
        <i class="fas fa-plus-circle"></i><span>Add</span>
    </a>
    <a href="{{ route('investments.index') }}"
        class="mobile-nav-item {{ Request::routeIs('investments.*') ? 'active' : '' }}">
        <i class="fas fa-chart-line"></i><span>Invest</span>
    </a>
    <a href="{{ route('reports.index') }}" class="mobile-nav-item {{ Request::routeIs('reports.*') ? 'active' : '' }}">
        <i class="fas fa-chart-pie"></i><span>Reports</span>
    </a>
</div>