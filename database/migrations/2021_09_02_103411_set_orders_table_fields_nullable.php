<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetOrdersTableFieldsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('orders', function($table) {
        $table->string('total_cost')->unsigned()->nullable()->change();
        $table->string('freight_cost')->unsigned()->nullable()->change();
        $table->string('duty_fee')->unsigned()->nullable()->change();
        $table->string('selling_price')->unsigned()->nullable()->change();
        $table->string('margin')->unsigned()->nullable()->change();
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
