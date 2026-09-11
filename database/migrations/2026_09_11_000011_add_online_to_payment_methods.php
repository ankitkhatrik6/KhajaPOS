<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Extend the payment method enums so 'Online' is accepted alongside the
        // historical methods (keeps existing rows valid).
        DB::statement("ALTER TABLE sales MODIFY payment_method ENUM('Cash','Online','Card','eSewa','Khalti') NOT NULL DEFAULT 'Cash'");
        DB::statement("ALTER TABLE invoices MODIFY payment_method ENUM('Cash','Online','Card','eSewa','Khalti') NOT NULL");
        DB::statement("ALTER TABLE payments MODIFY payment_method ENUM('Cash','Online','Card','eSewa','Khalti') NOT NULL");
    }

    public function down(): void
    {
        // Convert 'Online' rows back to 'Cash' before narrowing the enum again
        DB::statement("UPDATE payments SET payment_method = 'Cash' WHERE payment_method = 'Online'");
        DB::statement("UPDATE invoices SET payment_method = 'Cash' WHERE payment_method = 'Online'");
        DB::statement("UPDATE sales SET payment_method = 'Cash' WHERE payment_method = 'Online'");
        DB::statement("ALTER TABLE sales MODIFY payment_method ENUM('Cash','Card','eSewa','Khalti') NOT NULL DEFAULT 'Cash'");
        DB::statement("ALTER TABLE invoices MODIFY payment_method ENUM('Cash','Card','eSewa','Khalti') NOT NULL");
        DB::statement("ALTER TABLE payments MODIFY payment_method ENUM('Cash','Card','eSewa','Khalti') NOT NULL");
    }
};