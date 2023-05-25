<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColsTypeInProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            //
            $table->bigInteger('category_id')->nullable()->change();
            $table->string('name')->nullable()->change();
            $table->string('image')->nullable()->change();
            $table->bigInteger('product_unit_id')->nullable()->change();
            $table->integer('is_active')->nullable()->change();
            $table->string('value')->nullable()->change();
            $table->decimal('price', 15,2)->default('0.00')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
}
