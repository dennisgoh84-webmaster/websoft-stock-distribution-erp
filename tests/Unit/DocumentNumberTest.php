<?php

namespace Tests\Unit;

use App\Support\DocumentNumber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class DocumentNumberTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_sequential_padded_numbers(): void
    {
        $this->assertSame('PO-000001', DocumentNumber::generate('purchase_order', 'PO'));
        $this->assertSame('PO-000002', DocumentNumber::generate('purchase_order', 'PO'));
        $this->assertSame('PO-000003', DocumentNumber::generate('purchase_order', 'PO'));
    }

    public function test_different_types_have_independent_sequences(): void
    {
        DocumentNumber::generate('purchase_order', 'PO');
        DocumentNumber::generate('purchase_order', 'PO');

        $this->assertSame('SO-000001', DocumentNumber::generate('sales_order', 'SO'));
    }

    public function test_an_unknown_sequence_type_throws(): void
    {
        $this->expectException(RuntimeException::class);

        DocumentNumber::generate('not_a_real_type', 'XX');
    }
}
