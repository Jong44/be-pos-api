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
        Schema::create('open_bills', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('outlet_id');
            $table->uuid('cashier_id');
            $table->string('code')->unique();
            $table->string('customer_name');
            $table->timestamp('open_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->string('status')->default('open')->enum('open', 'closed');
            $table->uuid('voucher_id')->nullable();
            $table->double('discount_price')->default(0);
            $table->integer('total_qty')->default(0);
            $table->integer('total_price')->default(0);
            $table->timestamps();

            $table->foreign('outlet_id')->references('id')->on('outlets')->onDelete('cascade');
            $table->foreign('cashier_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('open_bills');
    }
};
