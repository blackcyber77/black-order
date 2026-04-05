<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // --- Orders Table: Add payment gateway columns ---
        Schema::table('orders', function (Blueprint $table) {
            // Payment gateway fields
            $table->string('payment_gateway')->nullable()->after('payment_proof')
                ->comment('Gateway provider: midtrans, xendit, doku, etc');
            $table->string('payment_gateway_ref')->nullable()->after('payment_gateway')
                ->comment('Transaction ID from payment gateway');
            $table->string('payment_gateway_url')->nullable()->after('payment_gateway_ref')
                ->comment('Payment URL from gateway (snap URL, invoice URL)');
            $table->string('payment_gateway_token')->nullable()->after('payment_gateway_url')
                ->comment('Token from gateway (snap token, etc)');
            $table->timestamp('payment_expiry')->nullable()->after('payment_gateway_token')
                ->comment('Payment expiry time from gateway');
            $table->timestamp('paid_at')->nullable()->after('payment_expiry')
                ->comment('Timestamp when payment was confirmed');
        });

        // --- Transactions Table: Add gateway fields ---
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('gateway_reference')->nullable()->after('payment_status')
                ->comment('Transaction ID from gateway');
            $table->json('gateway_response')->nullable()->after('gateway_reference')
                ->comment('Raw JSON response from gateway for audit');
        });

        // Update existing COD orders to 'tunai'
        DB::table('orders')->where('payment_method', 'cod')->update(['payment_method' => 'tunai']);
        DB::table('transactions')->where('payment_method', 'cod')->update(['payment_method' => 'tunai']);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_gateway',
                'payment_gateway_ref',
                'payment_gateway_url',
                'payment_gateway_token',
                'payment_expiry',
                'paid_at',
            ]);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'gateway_reference',
                'gateway_response',
            ]);
        });

        // Revert tunai back to cod
        DB::table('orders')->where('payment_method', 'tunai')->update(['payment_method' => 'cod']);
        DB::table('transactions')->where('payment_method', 'tunai')->update(['payment_method' => 'cod']);
    }
};
