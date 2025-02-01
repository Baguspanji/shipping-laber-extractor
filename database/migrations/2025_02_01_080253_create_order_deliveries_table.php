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
        Schema::create('order_deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('awb_number');
            $table->string('shipping_address');
            $table->boolean('cod');
            $table->string('shipping_phone');
            $table->string('receiver_name');
            $table->string('receiver_address');
            $table->string('shipping_name');
            $table->string('delivery_date');
            $table->string('weight');
            $table->json('products');
            $table->string('shipping_code1');
            $table->string('shipping_code2');
            $table->string('shipping_service');
            $table->string('receiver_place');
            $table->string('receiver_city');
            $table->string('receiver_district');
            $table->string('receiver_village');
            $table->string('no_reference');
            $table->boolean('cod_check');
            $table->string('status')->default('pending');
            $table->string('note')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_deliveries');
    }
};
