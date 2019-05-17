<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAllBustables  extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        //用户表
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nickname', 100)->nullable(false)->default('')->comment('用户名(同微信用户名)');
            $table->tinyInteger('gender')->unsigned()->nullable(false)->default(1)->comment('性别(1:男,2:女),同微信');
            $table->string('avatar', 255)->nullable(false)->default('')->comment('同微信头像链接');
            $table->string('tel', 50)->nullable(false)->default('')->comment('电话');
            $table->string('description', 255)->nullable(true)->default('')->comment('用户个人介绍');
            $table->integer('point')->nullable(true)->default(0)->comment('用户个人积分');
            $table->string('unionid', 50)->nullable(true)->default('')->comment('微信的unionid');
            $table->string('openid', 255)->nullable(true)->default('')->comment('用户唯一标识openid');
            $table->date('login_time')->nullable(true)->comment('记录用户本次登录时间');
            $table->string('login_ip')->nullable(true)->comment('记录用户本次登录ip');
            $table->tinyInteger('deleted')->default(0)->comment("软删除标记");
            $table->timestamps();
        });

        //用户收获地址
        Schema::create('addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable(false)->default(0)->comment('用户ID(关联用户表)');
            $table->string('consignee', 10)->nullable(false)->default('')->comment('收货人姓名');
            $table->string('tel', 13)->nullable(false)->default('')->comment('收货人电话号码');
            $table->integer('school_id')->nullable(false)->default(0)->comment('学校ID');
            $table->string('address', 255)->nullable(false)->default('')->comment('具体的宿舍楼栋号');
            $table->tinyInteger('is_default')->default(0)->comment('是否是默认收获地址');
            $table->tinyInteger('deleted')->default(0)->comment("软删除标记");
            $table->timestamps();
        });

        //学校表
        Schema::create('schools', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 30)->nullable(false)->comment('学校名');
            $table->tinyInteger('deleted')->default(0)->comment("软删除标记");
            $table->timestamps();
        });

        //站点名
        Schema::create('sites', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 30)->nullable(false)->comment('站点名(即起点)');
            $table->integer('school_id')->nullable(false)->comment('关联学校ID');
            $table->tinyInteger('deleted')->default(0)->comment("软删除标记");
            $table->timestamps();
        });


        //老司机表
        Schema::create('drivers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable(false)->comment('关联用户表');
            $table->string('name', 30)->nullable(false)->comment('真实姓名');
            $table->integer('school_id')->nullable(false)->comment('所在学校ID');
            $table->string('address',50)->nullable(false)->comment('宿舍楼和房间号');
            $table->string('card_img',100)->nullable(false)->comment('证件图片地址');
            $table->string('card_number',100)->nullable(false)->comment('证件号码');
            $table->integer('point')->nullable(true)->default(0)->comment('驾照积分');
            $table->tinyInteger('deleted')->default(0)->comment("软删除标记");
            $table->timestamps();
        });

        //老司机发车表
        Schema::create('bus', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('driver_id')->nullable(false)->comment('关联老司机id');
            $table->integer('school_id')->nullable(false)->comment('关联所在学校id');
            $table->string('driver_line')->nullable(false)->comment('发车专线');
            $table->integer('site_id')->nullable(false)->comment('关联所在学校的站点id(即起点)');
            $table->string('end_site')->nullable(false)->comment('终点');
            $table->float('price')->nullable(false)->default(1.00)->comment('票价');
            $table->string('note',100)->nullable(false)->comment('备注(大件补差价等信息在此备注)');
            $table->tinyInteger('status')->default(0)->comment('任务状态,0:未发车,1:发车中,2:已到站,3:全部确认');
            $table->tinyInteger('count')->default(4)->comment('bus乘客数量(最大不能超过8)');
            $table->dateTime('start_time')->nullable(true)->comment('发车时间');
            $table->dateTime('end_time')->nullable(false)->comment('预计到站时间');
            $table->tinyInteger('deleted')->default(0)->comment("软删除标记");
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
        //
        Schema::drop('users');
        Schema::drop('addresses');
        Schema::drop('schools');
        Schema::drop('sites');
        Schema::drop('drivers');
        Schema::drop('bus');
    }
}
