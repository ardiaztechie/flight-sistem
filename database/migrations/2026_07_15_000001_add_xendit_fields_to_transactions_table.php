<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('payment_status');
            $table->string('xendit_invoice_id')->nullable()->after('payment_method');
            $table->string('xendit_invoice_url')->nullable()->after('xendit_invoice_id');
            $table->integer('discount')->nullable()->after('grandtotal');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'xendit_invoice_id', 'xendit_invoice_url', 'discount']);
        });
    }
};
