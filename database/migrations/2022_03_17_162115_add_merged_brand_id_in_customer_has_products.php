<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMergedBrandIdInCustomerHasProducts extends Migration
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
            $table->string('merged_brand_id')->nullable()->after('merged_customer_id');
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
            $table->dropColumn('merged_brand_id');
        });
    }
}
