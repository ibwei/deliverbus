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
            $table->integer('address_id')->nullable(false)->comment('用户收获地址的id');
            $table->tinyInteger('type')->default(1)->comment('票价类型 1:小件,2:中件，3：大件');
            $table->float('price')->comment('票价');
            $table->string('trade_sn')->comment('订单号,平台自己的');
            $table->string('pay_sn')->comment('交易号，微信的');
            $table->string('deliver_number')->comment('取货码');
            $table->string('express_name')->comment('快递公司');
            $table->string('consignee_name')->comment('取货人姓名');
            $table->string('consignee_tel')->comment('取货人电话');
            $table->string('memo')->comment('备注')->nullable(true);
            $table->tinyInteger('status')->default(1)->comment('状态 1：待支付  2：已支付 3：支付失败' );
            $table->tinyInteger('received')->default(0)->comment('状态 1:已收到  0:未收到' );
            $table->timestamp('pay_date')->comment('支付时间');
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('feedback', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('用户id');;
            $table->string('message')->comment('反馈消息');
            $table->tinyInteger('status')->default(0)->nullable(true)->comment('状态 0：未读 1：已读 2：已处理');
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
