<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeletedAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('sites', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('drivers', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('bus', function (Blueprint $table) {
            $table->softDeletes();
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
    }
}
