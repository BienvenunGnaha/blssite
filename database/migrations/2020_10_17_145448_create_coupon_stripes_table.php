<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponStripesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon_stripes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('id_coupon')->nullable();
            $table->integer('amount_off')->nullable();
            $table->string('currency')->nullable();
            $table->string('duration');
            $table->integer('duration_in_months')->nullable();
            $table->string('name');
            $table->decimal('percent_off')->nullable();
            $table->integer('max_redemptions')->nullable();
            $table->integer('times_redeemed')->default(0);
            $table->boolean('valid')->default(1);
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
        Schema::dropIfExists('coupon_stripes');
    }
}
