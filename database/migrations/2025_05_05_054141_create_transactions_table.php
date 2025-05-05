<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->uuid('cashier_id');
            $table->uuid('outlet_id');
            $table->date('date');
            $table->string('note')->nullable();
            $table->uuid('voucher_id')->nullable();
            $table->string('discout_price')->nullable();
            $table->string('code')->unique();
            $table->double('payed_money');
            $table->double('money_changes');
            $table->double('total_price');
            $table->double('total_cost');
            $table->uuid('payment_method_id');
            $table->double('tax')->default(0);
            $table->double('tax_price')->default(0);
            $table->double('total_qty');
            $table->timestamps();

            $table->foreign('cashier_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('outlet_id')->references('id')->on('outlets')->onDelete('cascade');
            $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('cascade');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
