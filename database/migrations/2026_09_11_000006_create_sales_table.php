<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('sale_number')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->string('customer_name')->default('Walk-in Customer');
            $table->decimal('subtotal', 12, 2)->default(0.00);
            $table->decimal('discount', 12, 2)->default(0.00);
            $table->decimal('tax', 12, 2)->default(0.00);
            $table->decimal('total', 12, 2)->default(0.00);
            $table->decimal('cost', 12, 2)->default(0.00);
            $table->decimal('profit', 12, 2)->default(0.00);
            $table->enum('payment_method', ['Cash', 'Card', 'eSewa', 'Khalti'])->default('Cash');
            $table->enum('payment_status', ['Paid', 'Pending', 'Cancelled'])->default('Paid');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('sale_number');
            $table->index('created_at');
            $table->index('payment_method');
            $table->index('payment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
