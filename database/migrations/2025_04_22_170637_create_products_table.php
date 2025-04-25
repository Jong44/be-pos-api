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
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('outlet_id');
            $table->uuid('category_id');
            $table->string('name')->unique();
            $table->integer('stock')->default(0);
            $table->boolean('is_non_stock')->default(false);
            $table->double('initial_price')->default(0);
            $table->double('price')->default(0);
            $table->string('unit')->default('pcs');
            $table->text('hero_images')->nullable();
            $table->timestamps();

            $table->foreign('outlet_id')->references('id')->on('outlets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
