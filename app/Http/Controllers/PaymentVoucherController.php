<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PaymentVoucher;
use App\Models\Supplier;
use App\Services\PaymentVoucherService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class PaymentVoucherController extends Controller
{
    public function __construct(protected PaymentVoucherService $paymentVoucherService) {}

    public function index(Request $request): View
    {
        $vouchers = PaymentVoucher::with('supplier')
            ->when($request->filled('supplier_id'), fn ($q) => $q->where('supplier_id', $request->integer('supplier_id')))
            ->latest('payment_date')
            ->paginate(20)
            ->withQueryString();

        $suppliers = Supplier::orderBy('name')->get();

        return view('payment-vouchers.index', compact('vouchers', 'suppliers'));
    }

    public function create(): View
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        return view('payment-vouchers.create', compact('suppliers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date'],
            'method' => ['required', 'in:'.implode(',', [
                PaymentVoucher::METHOD_CASH,
                PaymentVoucher::METHOD_BANK_TRANSFER,
                PaymentVoucher::METHOD_CHEQUE,
                PaymentVoucher::METHOD_OTHER,
            ])],
            'reference_no' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $voucher = $this->paymentVoucherService->recordVoucher($data, auth()->id());

        return redirect()->route('payment-vouchers.show', $voucher)->with('status', 'Payment voucher recorded. Allocate it below to settle specific bills.');
    }

    public function show(PaymentVoucher $paymentVoucher): View
    {
        $paymentVoucher->load(['allocations.invoice', 'supplier']);

        $openInvoices = Invoice::where('type', Invoice::TYPE_PURCHASE)
            ->where('supplier_id', $paymentVoucher->supplier_id)
            ->whereIn('status', [Invoice::STATUS_UNPAID, Invoice::STATUS_PARTIALLY_PAID])
            ->orderBy('invoice_date')
            ->get();

        return view('payment-vouchers.show', ['voucher' => $paymentVoucher, 'openInvoices' => $openInvoices]);
    }

    public function allocate(Request $request, PaymentVoucher $paymentVoucher): RedirectResponse
    {
        $data = $request->validate([
            'invoice_id' => ['required', 'exists:invoices,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $invoice = Invoice::findOrFail($data['invoice_id']);

        try {
            $this->paymentVoucherService->allocate($paymentVoucher, $invoice, (float) $data['amount'], auth()->id());
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('payment-vouchers.show', $paymentVoucher)->with('status', 'Allocated.');
    }
}
