<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A Receipt/Payment Voucher is money recorded from a Customer or to a
     * Supplier independently of any one invoice -- it is then allocated
     * ("knocked off") against one or more of that party's outstanding
     * invoices via *_allocations, same shape both sides. This sits
     * alongside the existing invoices.pay (Payment model) direct
     * single-invoice flow rather than replacing it: that one stays for
     * the common case of paying exactly one invoice in full immediately;
     * this one is for a receipt/payment that needs to be split across
     * several invoices, or that arrives before it's decided what it
     * settles (an unallocated balance is normal, not an error -- see
     * Receipt::unallocatedAmount()).
     */
    public function up(): void
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 14, 2);
            $table->date('receipt_date');
            $table->string('method')->default('cash');
            $table->string('reference_no')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('receipt_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receipt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 14, 2);
            $table->timestamps();
        });

        Schema::create('payment_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_number')->unique();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 14, 2);
            $table->date('payment_date');
            $table->string('method')->default('cash');
            $table->string('reference_no')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_voucher_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_voucher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 14, 2);
            $table->timestamps();
        });

        // Pre-seed the two new document types, same as every existing
        // type in the create_document_sequences_table migration -- so
        // DocumentNumber::generate() only ever locks-and-updates an
        // existing row on the sqlite (test) fallback path, never inserts
        // one.
        $now = now();
        DB::table('document_sequences')->insert([
            ['type' => 'receipt', 'next_number' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['type' => 'payment_voucher', 'next_number' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_voucher_allocations');
        Schema::dropIfExists('payment_vouchers');
        Schema::dropIfExists('receipt_allocations');
        Schema::dropIfExists('receipts');

        DB::table('document_sequences')->whereIn('type', ['receipt', 'payment_voucher'])->delete();
    }
};
