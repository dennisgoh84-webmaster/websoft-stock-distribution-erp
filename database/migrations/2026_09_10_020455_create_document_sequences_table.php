<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Backs App\Support\DocumentNumber: one row per document type, its
     * "next number" incremented under a row lock so concurrent requests
     * never generate the same PO/SO/invoice/etc. number.
     */
    public function up(): void
    {
        Schema::create('document_sequences', function (Blueprint $table) {
            $table->string('type')->primary();
            $table->unsignedBigInteger('next_number')->default(1);
            $table->timestamps();
        });

        // Pre-seed every known document type so DocumentNumber::generate()
        // only ever needs to lock-and-update an existing row, never insert
        // one — removing the "first caller creates the row" race entirely.
        $now = now();
        DB::table('document_sequences')->insert(
            collect(['purchase_order', 'sales_order', 'stock_transfer', 'stock_adjustment', 'invoice', 'payment'])
                ->map(fn ($type) => ['type' => $type, 'next_number' => 1, 'created_at' => $now, 'updated_at' => $now])
                ->all()
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('document_sequences');
    }
};
