<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropForeign(['inventory_item_id']);
            $table->dropColumn('inventory_item_id');

            $table->foreignId('menu_item_id')->nullable()->after('sale_id')
                ->constrained('menu_items')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropForeign(['menu_item_id']);
            $table->dropColumn('menu_item_id');

            $table->foreignId('inventory_item_id')->nullable()->after('sale_id')
                ->constrained('inventory_items')->nullOnDelete();
        });
    }
};