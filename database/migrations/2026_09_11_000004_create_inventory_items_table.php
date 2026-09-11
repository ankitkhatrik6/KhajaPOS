<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');
            $table->enum('unit', [
                'Piece', 'Kg', 'Gram', 'Liter', 'Ml', 'Packet', 'Box', 'Bottle', 'Dozen'
            ])->default('Piece');
            $table->decimal('purchase_price', 12, 2)->default(0.00);
            $table->decimal('selling_price', 12, 2)->default(0.00);
            $table->decimal('current_quantity', 12, 2)->default(0.00);
            $table->decimal('minimum_stock', 12, 2)->default(5.00);
            $table->string('supplier')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->index('name');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
