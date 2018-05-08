<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicket extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bus_id');
            $table->integer('user_id');
            $table->tinyInteger('type')->default(1)->comment('票价类型 1:小件,2:中件，3：大件');
            $table->float('price')->default(1.00)->comment('票价');
            $table->string('trade_sn')->comment('订单号,平台自己的');
            $table->string('pay_sn')->comment('交易号，微信的');
            $table->tinyInteger('status')->default(1)->comment('状态 1：待支付  2：已支付 3：支付失败');
            $table->softDeletes();
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
        Schema::dropIfExists('ticket');
    }
}
