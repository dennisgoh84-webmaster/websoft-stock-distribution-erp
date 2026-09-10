<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Defense-in-depth: even though InvoiceService only ever generates one
     * invoice per order (guarded by an existence check inside the order's
     * own row-locked transaction), this constraint makes "one invoice per
     * source order" true at the database level too, so no future code path
     * can silently create a duplicate.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex('invoices_source_type_source_id_index');
            $table->unique(['source_type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropUnique(['source_type', 'source_id']);
            $table->index(['source_type', 'source_id']);
        });
    }
};
