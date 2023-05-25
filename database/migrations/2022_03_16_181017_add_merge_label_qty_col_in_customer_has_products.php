<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMergeLabelQtyColInCustomerHasProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_has_products', function (Blueprint $table) {
            //
            $table->integer('merged_customer_id')->nullable()->after('alert_days');
            $table->string('merged_qty')->nullable()->after('merged_customer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_has_products', function (Blueprint $table) {
            //
            $table->dropColumn('merged_customer_id');
            $table->dropColumn('merged_qty');
        });
    }
}
