<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->integer('value');
            $table->integer('shipping_fee');
            $table->string('payment_type');
            $table->string('customer_name');
            $table->string('customer_mail');
            $table->string('customer_country')->nullable();
            $table->string('customer_state')->nullable();
            $table->string('customer_address')->nullable();
            $table->string('customer_city');
            $table->string('customer_zip');
            $table->string('customer_phone');
            $table->boolean('cancelled')->default(false);
            $table->boolean('shipped')->default(false);
            $table->date('shipping_date')->nullable();
            $table->string('promo_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchases');
    }
}
