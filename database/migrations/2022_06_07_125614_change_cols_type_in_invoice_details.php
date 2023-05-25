<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColsTypeInInvoiceDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_details', function (Blueprint $table) {
            //
            $table->bigInteger('sku_id')->nullable()->change();
            $table->bigInteger('invoice_id')->nullable()->change();
            $table->bigInteger('qty')->default('0')->change();
            $table->decimal('cost_of_good', 15,2)->default('0.00')->change();
            $table->decimal('service_charges', 8,2)->default('0.00')->change();
            $table->json('service_charges_detail')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_details', function (Blueprint $table) {
            //
        });
    }
}
