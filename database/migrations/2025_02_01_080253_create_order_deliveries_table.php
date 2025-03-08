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
        // order resi session
        Schema::create('order_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('file');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('order_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_session_id')->nullable()->constrained('order_sessions')->onDelete('cascade');
            $table->string('awb_number');
            $table->string('shipping_address')->nullable();
            $table->boolean('cod')->nullable();
            $table->string('shipping_phone')->nullable();
            $table->string('receiver_name')->nullable();
            $table->string('receiver_address')->nullable();
            $table->string('shipping_name')->nullable();
            $table->string('delivery_date')->nullable();
            $table->string('weight')->nullable();
            $table->string('shipping_code1')->nullable();
            $table->string('shipping_code2')->nullable();
            $table->string('shipping_service')->nullable();
            $table->string('receiver_place')->nullable();
            $table->string('receiver_city')->nullable();
            $table->string('receiver_district')->nullable();
            $table->string('receiver_village')->nullable();
            $table->string('no_reference')->nullable();
            $table->boolean('cod_check')->nullable();
            $table->string('status')->default('pending');
            $table->string('note')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('order_delivery_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_delivery_id')->constrained('order_deliveries')->onDelete('cascade');
            $table->string('name');
            $table->string('sku');
            $table->string('variant')->nullable();
            $table->integer('qty')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_delivery_products');
        Schema::dropIfExists('order_deliveries');
        Schema::dropIfExists('order_sessions');
    }
};
