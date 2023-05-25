<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColsTypeInSkuProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sku_products', function (Blueprint $table) {
            //
            $table->bigInteger('sku_id')->nullable()->change();
            $table->bigInteger('product_id')->nullable()->change();
            $table->bigInteger('quantity')->default('0')->change();
            $table->decimal('purchasing_cost', 15,2)->default('0.00')->change();
            $table->decimal('selling_cost', 15,2)->default('0.00')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sku_products', function (Blueprint $table) {
            //
        });
    }
}
