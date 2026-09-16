<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            // Extend the payment method enums so 'Online' is accepted alongside the
            // historical methods (keeps existing rows valid).
            DB::statement("ALTER TABLE sales MODIFY payment_method ENUM('Cash','Online','Card','eSewa','Khalti') NOT NULL DEFAULT 'Cash'");
            DB::statement("ALTER TABLE invoices MODIFY payment_method ENUM('Cash','Online','Card','eSewa','Khalti') NOT NULL");
            DB::statement("ALTER TABLE payments MODIFY payment_method ENUM('Cash','Online','Card','eSewa','Khalti') NOT NULL");
            return;
        }

        // SQLite (and other drivers) store enums as text columns with CHECK
        // constraints, so rebuilding them is enough to accept 'Online'.
        Schema::table('sales', function (Blueprint $table) {
            $table->enum('payment_method', ['Cash', 'Online', 'Card', 'eSewa', 'Khalti'])->default('Cash')->change();
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('payment_method', ['Cash', 'Online', 'Card', 'eSewa', 'Khalti'])->change();
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('payment_method', ['Cash', 'Online', 'Card', 'eSewa', 'Khalti'])->change();
        });
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        // Convert 'Online' rows back to 'Cash' before narrowing the enum again
        DB::statement("UPDATE payments SET payment_method = 'Cash' WHERE payment_method = 'Online'");
        DB::statement("UPDATE invoices SET payment_method = 'Cash' WHERE payment_method = 'Online'");
        DB::statement("UPDATE sales SET payment_method = 'Cash' WHERE payment_method = 'Online'");

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement("ALTER TABLE sales MODIFY payment_method ENUM('Cash','Card','eSewa','Khalti') NOT NULL DEFAULT 'Cash'");
            DB::statement("ALTER TABLE invoices MODIFY payment_method ENUM('Cash','Card','eSewa','Khalti') NOT NULL");
            DB::statement("ALTER TABLE payments MODIFY payment_method ENUM('Cash','Card','eSewa','Khalti') NOT NULL");
            return;
        }

        Schema::table('sales', function (Blueprint $table) {
            $table->enum('payment_method', ['Cash', 'Card', 'eSewa', 'Khalti'])->default('Cash')->change();
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('payment_method', ['Cash', 'Card', 'eSewa', 'Khalti'])->change();
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('payment_method', ['Cash', 'Card', 'eSewa', 'Khalti'])->change();
        });
    }
};