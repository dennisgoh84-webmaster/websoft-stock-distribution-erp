<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight tracking-tight">{{ __('Record Payment Voucher') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl p-6">
                <p class="text-sm text-slate-500 mb-6">
                    {{ __('Money paid to a supplier, recorded before deciding which bill(s) it settles. Allocate it on the next screen — a voucher can settle one bill, several, or sit unallocated.') }}
                </p>

                <form method="POST" action="{{ route('payment-vouchers.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="supplier_id" :value="__('Supplier')" />
                            <select id="supplier_id" name="supplier_id" class="mt-1 block w-full border-slate-300 focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm" required>
                                <option value="">{{ __('Select supplier') }}</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('supplier_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="amount" :value="__('Amount')" />
                            <x-text-input id="amount" name="amount" type="number" step="0.01" min="0.01" class="mt-1 block w-full" value="{{ old('amount') }}" required />
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="payment_date" :value="__('Payment Date')" />
                            <x-text-input id="payment_date" name="payment_date" type="date" class="mt-1 block w-full" value="{{ old('payment_date', now()->toDateString()) }}" required />
                            <x-input-error :messages="$errors->get('payment_date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="method" :value="__('Method')" />
                            <select id="method" name="method" class="mt-1 block w-full border-slate-300 focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm">
                                <option value="cash">{{ __('Cash') }}</option>
                                <option value="bank_transfer">{{ __('Bank Transfer') }}</option>
                                <option value="cheque">{{ __('Cheque') }}</option>
                                <option value="other">{{ __('Other') }}</option>
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <x-input-label for="reference_no" :value="__('Reference No.')" />
                            <x-text-input id="reference_no" name="reference_no" type="text" class="mt-1 block w-full" value="{{ old('reference_no') }}" />
                        </div>

                        <div class="sm:col-span-2">
                            <x-input-label for="notes" :value="__('Notes')" />
                            <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full border-slate-300 focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <x-primary-button>{{ __('Record Payment Voucher') }}</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
