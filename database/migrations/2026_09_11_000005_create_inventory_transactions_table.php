<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->enum('transaction_type', [
                'Purchase', 'Sale', 'Adjustment', 'Damage', 'Return', 'Manual Increase', 'Manual Decrease'
            ]);
            $table->decimal('quantity', 12, 2);
            $table->decimal('previous_quantity', 12, 2);
            $table->decimal('new_quantity', 12, 2);
            $table->decimal('purchase_price', 12, 2)->nullable();
            $table->string('reason')->nullable();
            $table->string('reference_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('transaction_type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
