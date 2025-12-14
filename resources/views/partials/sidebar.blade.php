<div class="sidebar" id="sidebar">
    <div class="logo"><i class="fas fa-robot"></i><span>JARVIS</span></div>
    <nav class="nav-menu">
        <a href="{{ route('dashboard') }}" class="nav-item {{ Request::routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i><span>Dashboard</span>
        </a>
        <a href="{{ route('accounts.index') }}" class="nav-item {{ Request::routeIs('accounts.*') ? 'active' : '' }}">
            <i class="fas fa-wallet"></i><span>Accounts</span>
        </a>
        <a href="{{ route('transactions.index') }}"
            class="nav-item {{ Request::routeIs('transactions.*') ? 'active' : '' }}">
            <i class="fas fa-exchange-alt"></i><span>Transactions</span>
        </a>
        <a href="{{ route('investments.index') }}"
            class="nav-item {{ Request::routeIs('investments.*') ? 'active' : '' }}">
            <i class="fas fa-chart-line"></i><span>Investments</span>
        </a>
        <a href="{{ route('investment-accounts.index') }}"
            class="nav-item {{ Request::routeIs('investment-accounts.*') ? 'active' : '' }}">
            <i class="fas fa-briefcase"></i><span>Inv. Accounts</span>
        </a>
        <a href="{{ route('loans.index') }}" class="nav-item {{ Request::routeIs('loans.*') ? 'active' : '' }}">
            <i class="fas fa-hand-holding-usd"></i><span>Loans</span>
        </a>
        <a href="{{ route('lendings.index') }}" class="nav-item {{ Request::routeIs('lendings.*') ? 'active' : '' }}">
            <i class="fas fa-handshake"></i><span>Lendings</span>
        </a>
        <a href="{{ route('transfer.index') }}" class="nav-item {{ Request::routeIs('transfer.*') ? 'active' : '' }}">
            <i class="fas fa-exchange-alt"></i><span>Transfer</span>
        </a>
        <a href="{{ route('categories.index') }}"
            class="nav-item {{ Request::routeIs('categories.*') ? 'active' : '' }}">
            <i class="fas fa-tags"></i><span>Categories</span>
        </a>
        <a href="{{ route('reports.index') }}" class="nav-item {{ Request::routeIs('reports.*') ? 'active' : '' }}">
            <i class="fas fa-chart-pie"></i><span>Reports</span>
        </a>
    </nav>
</div>