<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Receipt;
use App\Services\ReceiptService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class ReceiptController extends Controller
{
    public function __construct(protected ReceiptService $receiptService) {}

    public function index(Request $request): View
    {
        $receipts = Receipt::with('customer')
            ->when($request->filled('customer_id'), fn ($q) => $q->where('customer_id', $request->integer('customer_id')))
            ->latest('receipt_date')
            ->paginate(20)
            ->withQueryString();

        $customers = Customer::orderBy('name')->get();

        return view('receipts.index', compact('receipts', 'customers'));
    }

    public function create(): View
    {
        $customers = Customer::where('is_active', true)->orderBy('name')->get();

        return view('receipts.create', compact('customers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'receipt_date' => ['required', 'date'],
            'method' => ['required', 'in:'.implode(',', [
                Receipt::METHOD_CASH,
                Receipt::METHOD_BANK_TRANSFER,
                Receipt::METHOD_CHEQUE,
                Receipt::METHOD_OTHER,
            ])],
            'reference_no' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $receipt = $this->receiptService->recordReceipt($data, auth()->id());

        return redirect()->route('receipts.show', $receipt)->with('status', 'Receipt recorded. Allocate it below to settle specific invoices.');
    }

    public function show(Receipt $receipt): View
    {
        $receipt->load(['allocations.invoice', 'customer']);

        $openInvoices = Invoice::where('type', Invoice::TYPE_SALES)
            ->where('customer_id', $receipt->customer_id)
            ->whereIn('status', [Invoice::STATUS_UNPAID, Invoice::STATUS_PARTIALLY_PAID])
            ->orderBy('invoice_date')
            ->get();

        return view('receipts.show', compact('receipt', 'openInvoices'));
    }

    public function allocate(Request $request, Receipt $receipt): RedirectResponse
    {
        $data = $request->validate([
            'invoice_id' => ['required', 'exists:invoices,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $invoice = Invoice::findOrFail($data['invoice_id']);

        try {
            $this->receiptService->allocate($receipt, $invoice, (float) $data['amount'], auth()->id());
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('receipts.show', $receipt)->with('status', 'Allocated.');
    }
}
