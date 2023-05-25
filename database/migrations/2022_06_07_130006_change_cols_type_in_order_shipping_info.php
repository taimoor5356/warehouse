<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColsTypeInOrderShippingInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_shipping_info', function (Blueprint $table) {
            //
            $table->bigInteger('order_id')->nullable()->change();
            $table->string('first_name')->nullable()->change();
            $table->string('last_name')->nullable()->change();
            $table->string('phone')->nullable()->change();
            $table->longText('address_1')->nullable()->change();
            $table->longText('address_2')->nullable()->change();
            $table->bigInteger('country_id')->nullable()->change();
            $table->bigInteger('state_id')->nullable()->change();
            $table->bigInteger('city_id')->nullable()->change();
            $table->string('postal_code')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_shipping_info', function (Blueprint $table) {
            //
        });
    }
}
