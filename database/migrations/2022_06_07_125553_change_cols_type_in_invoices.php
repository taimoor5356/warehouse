<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColsTypeInInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            //
            $table->bigInteger('order_id')->nullable()->change();
            $table->string('invoice_number')->nullable()->change();
            $table->decimal('grand_total', 15,2)->default('0.00')->change();
            $table->integer('status')->nullable()->change();
            $table->bigInteger('customer_id')->nullable()->change();
            $table->integer('is_paid')->default('0')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            //
        });
    }
}
