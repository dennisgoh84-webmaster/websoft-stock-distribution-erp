<nav x-data="{ open: false }" class="bg-white border-b border-slate-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center gap-2.5">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                        <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-600">
                            <x-application-logo class="w-4 h-4 text-white" />
                        </span>
                        <span class="font-bold text-slate-900 tracking-tight hidden sm:block">{{ __('Websoft') }}</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-4 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <x-nav-dropdown title="Inventory" :active="request()->routeIs(['products.*', 'categories.*', 'units.*', 'warehouses.*', 'stock-transfers.*', 'stock-adjustments.*', 'stock-movements.*'])">
                        <x-dropdown-link :href="route('products.index')">{{ __('Products') }}</x-dropdown-link>
                        <x-dropdown-link :href="route('categories.index')">{{ __('Categories') }}</x-dropdown-link>
                        <x-dropdown-link :href="route('units.index')">{{ __('Units') }}</x-dropdown-link>
                        <x-dropdown-link :href="route('warehouses.index')">{{ __('Warehouses') }}</x-dropdown-link>
                        <x-dropdown-link :href="route('stock-transfers.index')">{{ __('Stock Transfers') }}</x-dropdown-link>
                        <x-dropdown-link :href="route('stock-adjustments.index')">{{ __('Stock Adjustments') }}</x-dropdown-link>
                        <x-dropdown-link :href="route('stock-movements.index')">{{ __('Stock Ledger') }}</x-dropdown-link>
                    </x-nav-dropdown>

                    <x-nav-dropdown title="Purchasing" :active="request()->routeIs(['purchase-orders.*', 'suppliers.*', 'payment-vouchers.*'])">
                        <x-dropdown-link :href="route('purchase-orders.index')">{{ __('Purchase Orders') }}</x-dropdown-link>
                        <x-dropdown-link :href="route('suppliers.index')">{{ __('Suppliers') }}</x-dropdown-link>
                        <x-dropdown-link :href="route('payment-vouchers.index')">{{ __('Payment Vouchers') }}</x-dropdown-link>
                    </x-nav-dropdown>

                    <x-nav-dropdown title="Sales" :active="request()->routeIs(['sales-orders.*', 'customers.*', 'receipts.*'])">
                        <x-dropdown-link :href="route('sales-orders.index')">{{ __('Sales Orders') }}</x-dropdown-link>
                        <x-dropdown-link :href="route('customers.index')">{{ __('Customers') }}</x-dropdown-link>
                        <x-dropdown-link :href="route('receipts.index')">{{ __('Receipts') }}</x-dropdown-link>
                    </x-nav-dropdown>

                    <x-nav-link :href="route('invoices.index')" :active="request()->routeIs('invoices.*')">
                        {{ __('Invoices') }}
                    </x-nav-link>

                    @role('Admin')
                        <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                            {{ __('Users') }}
                        </x-nav-link>
                    @endrole
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-2 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-lg text-slate-600 bg-white hover:bg-slate-50 focus:outline-none transition ease-in-out duration-150">
                            <span class="flex items-center justify-center w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 text-xs font-semibold uppercase">
                                {{ Str::substr(Auth::user()->name, 0, 1) }}
                            </span>
                            <span>{{ Auth::user()->name }}</span>

                            <svg class="fill-current h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus:outline-none focus:bg-slate-100 focus:text-slate-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <div class="px-4 pt-3 pb-1 text-xs font-semibold text-slate-400 uppercase">{{ __('Inventory') }}</div>
            <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">{{ __('Products') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">{{ __('Categories') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('units.index')" :active="request()->routeIs('units.*')">{{ __('Units') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('warehouses.index')" :active="request()->routeIs('warehouses.*')">{{ __('Warehouses') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('stock-transfers.index')" :active="request()->routeIs('stock-transfers.*')">{{ __('Stock Transfers') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('stock-adjustments.index')" :active="request()->routeIs('stock-adjustments.*')">{{ __('Stock Adjustments') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('stock-movements.index')" :active="request()->routeIs('stock-movements.*')">{{ __('Stock Ledger') }}</x-responsive-nav-link>

            <div class="px-4 pt-3 pb-1 text-xs font-semibold text-slate-400 uppercase">{{ __('Purchasing') }}</div>
            <x-responsive-nav-link :href="route('purchase-orders.index')" :active="request()->routeIs('purchase-orders.*')">{{ __('Purchase Orders') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')">{{ __('Suppliers') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('payment-vouchers.index')" :active="request()->routeIs('payment-vouchers.*')">{{ __('Payment Vouchers') }}</x-responsive-nav-link>

            <div class="px-4 pt-3 pb-1 text-xs font-semibold text-slate-400 uppercase">{{ __('Sales') }}</div>
            <x-responsive-nav-link :href="route('sales-orders.index')" :active="request()->routeIs('sales-orders.*')">{{ __('Sales Orders') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('customers.index')" :active="request()->routeIs('customers.*')">{{ __('Customers') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('receipts.index')" :active="request()->routeIs('receipts.*')">{{ __('Receipts') }}</x-responsive-nav-link>

            <div class="px-4 pt-3 pb-1 text-xs font-semibold text-slate-400 uppercase">{{ __('Finance') }}</div>
            <x-responsive-nav-link :href="route('invoices.index')" :active="request()->routeIs('invoices.*')">{{ __('Invoices') }}</x-responsive-nav-link>

            @role('Admin')
                <div class="px-4 pt-3 pb-1 text-xs font-semibold text-slate-400 uppercase">{{ __('Admin') }}</div>
                <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">{{ __('Users') }}</x-responsive-nav-link>
            @endrole
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-slate-200">
            <div class="px-4">
                <div class="font-medium text-base text-slate-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-slate-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
