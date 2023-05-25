<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSellingCostColInInvoicesMergeds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices_mergeds', function (Blueprint $table) {
            //
            $table->double('selling_cost', 15, 2)->default(0)->after('product_qty');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices_mergeds', function (Blueprint $table) {
            //
        });
    }
}
