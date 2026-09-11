<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount', 12, 2)->default(0.00);
            $table->decimal('tax', 12, 2)->default(0.00);
            $table->decimal('grand_total', 12, 2);
            $table->enum('payment_method', ['Cash', 'Card', 'eSewa', 'Khalti']);
            $table->enum('payment_status', ['Paid', 'Pending', 'Cancelled'])->default('Paid');
            $table->string('cashier_name');
            $table->string('customer_name')->default('Walk-in Customer');
            $table->string('restaurant_name');
            $table->string('restaurant_address');
            $table->string('restaurant_phone')->nullable();
            $table->string('restaurant_pan')->nullable();
            $table->string('restaurant_vat')->nullable();
            $table->text('invoice_footer')->nullable();
            $table->timestamps();

            $table->index('invoice_number');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
