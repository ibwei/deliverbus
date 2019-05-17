<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddToBus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bus', function (Blueprint $table) {
            //$table->dropColumn('price'); //删除表的字段
            //$table->dropColumn('votes', 'avatar', 'location'); //删除多个字段
            $table->renameColumn('price', 'small_price');//修改表的字段
            $table->integer('small_count')->default(0)->comment('小件人数');//修改表的字段
            $table->integer('normall_count')->default(0)->comment('中件人数');//修改表的字段
            $table->integer('big_count')->default(0)->comment('大件人数');//修改表的字段
            $table->float('normall_price')->nullable(true)->default(2.00)->comment('中件票价');
            $table->float('big_price')->nullable(true)->default(4.00)->comment('大件票价');
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bus', function (Blueprint $table) {
            //
        });
    }
}
