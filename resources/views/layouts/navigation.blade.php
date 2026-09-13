<!-- Brand -->
<div class="flex items-center justify-between h-16 px-5 shrink-0 border-b border-slate-800">
    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
        <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-amber-600 shrink-0">
            <x-application-logo class="w-4 h-4 text-white" />
        </span>
        <span class="font-bold text-white tracking-tight">{{ __('Websoft') }}</span>
    </a>

    <!-- Close (mobile drawer only) -->
    <button type="button" @click="sidebarOpen = false" class="lg:hidden p-1 rounded-md text-slate-400 hover:text-white hover:bg-slate-800 focus:outline-none">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>

<!-- Navigation -->
<nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
    <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        {{ __('Dashboard') }}
    </x-sidebar-link>

    <x-sidebar-heading>{{ __('Inventory') }}</x-sidebar-heading>
    <x-sidebar-link :href="route('products.index')" :active="request()->routeIs('products.*')">{{ __('Products') }}</x-sidebar-link>
    <x-sidebar-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">{{ __('Categories') }}</x-sidebar-link>
    <x-sidebar-link :href="route('units.index')" :active="request()->routeIs('units.*')">{{ __('Units') }}</x-sidebar-link>
    <x-sidebar-link :href="route('warehouses.index')" :active="request()->routeIs('warehouses.*')">{{ __('Warehouses') }}</x-sidebar-link>
    <x-sidebar-link :href="route('stock-transfers.index')" :active="request()->routeIs('stock-transfers.*')">{{ __('Stock Transfers') }}</x-sidebar-link>
    <x-sidebar-link :href="route('stock-adjustments.index')" :active="request()->routeIs('stock-adjustments.*')">{{ __('Stock Adjustments') }}</x-sidebar-link>
    <x-sidebar-link :href="route('stock-movements.index')" :active="request()->routeIs('stock-movements.*')">{{ __('Stock Ledger') }}</x-sidebar-link>

    <x-sidebar-heading>{{ __('Purchasing') }}</x-sidebar-heading>
    <x-sidebar-link :href="route('purchase-orders.index')" :active="request()->routeIs('purchase-orders.*')">{{ __('Purchase Orders') }}</x-sidebar-link>
    <x-sidebar-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')">{{ __('Suppliers') }}</x-sidebar-link>
    <x-sidebar-link :href="route('payment-vouchers.index')" :active="request()->routeIs('payment-vouchers.*')">{{ __('Payment Vouchers') }}</x-sidebar-link>

    <x-sidebar-heading>{{ __('Sales') }}</x-sidebar-heading>
    <x-sidebar-link :href="route('sales-orders.index')" :active="request()->routeIs('sales-orders.*')">{{ __('Sales Orders') }}</x-sidebar-link>
    <x-sidebar-link :href="route('customers.index')" :active="request()->routeIs('customers.*')">{{ __('Customers') }}</x-sidebar-link>
    <x-sidebar-link :href="route('receipts.index')" :active="request()->routeIs('receipts.*')">{{ __('Receipts') }}</x-sidebar-link>

    <x-sidebar-heading>{{ __('Finance') }}</x-sidebar-heading>
    <x-sidebar-link :href="route('invoices.index')" :active="request()->routeIs('invoices.*')">{{ __('Invoices') }}</x-sidebar-link>

    @role('Admin')
        <x-sidebar-heading>{{ __('Admin') }}</x-sidebar-heading>
        <x-sidebar-link :href="route('users.index')" :active="request()->routeIs('users.*')">{{ __('Users') }}</x-sidebar-link>
    @endrole
</nav>
