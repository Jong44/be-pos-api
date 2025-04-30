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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id()->primary();
            $table->uuid('outlet_id')->index();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('type')->enum('nominal', 'percentage');
            $table->double('nominal');
            $table->date('start_date');
            $table->date('expired_date');
            $table->double('minimum_buying');
            $table->string('status')->enum('active', 'inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
