<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');
            $table->string('unit')->default('Piece');
            $table->decimal('cost', 12, 2)->default(0.00);
            $table->decimal('price', 12, 2)->default(0.00);
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            $table->index('name');
            $table->index('is_available');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};