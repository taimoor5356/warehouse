<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReturnColsInInvoicesMergeds extends Migration
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
            $table->integer('product_return_qty')->default('0')->after('mailer_unit_cost');
            $table->decimal('product_return_cost', 8,2)->default('0.00')->after('product_return_qty');
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
            $table->dropColumn('product_return_qty');
            $table->dropColumn('product_return_cost');
        });
    }
}
