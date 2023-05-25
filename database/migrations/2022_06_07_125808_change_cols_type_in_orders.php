<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColsTypeInOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            //
            $table->bigInteger('customer_id')->nullable()->change();
            $table->bigInteger('brand_id')->nullable()->change();
            $table->decimal('total_cost', 15,2)->default('0.00')->change();
            $table->decimal('freight_cost', 15,2)->default('0.00')->change();
            $table->decimal('duty_fee', 15,2)->default('0.00')->change();
            $table->decimal('selling_price', 15,2)->default('0.00')->change();
            $table->integer('status')->nullable()->change();
            $table->bigInteger('labelqty')->default('0')->change();
            $table->bigInteger('pickqty')->default('0')->change();
            $table->bigInteger('packqty')->default('0')->change();
            $table->bigInteger('mailerqty')->default('0')->change();
            $table->bigInteger('postageqty')->default('0')->change();
            $table->decimal('pick_pack_flat_price', 15,2)->default('0.00')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
}
