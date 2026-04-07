<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('cashier_id')->nullable()->after('customer_email')->constrained('users')->nullOnDelete();
            $table->timestamp('held_at')->nullable()->after('paid_at');
            $table->timestamp('voided_at')->nullable()->after('held_at');
            $table->text('void_reason')->nullable()->after('voided_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cashier_id');
            $table->dropColumn(['held_at', 'voided_at', 'void_reason']);
        });
    }
};

