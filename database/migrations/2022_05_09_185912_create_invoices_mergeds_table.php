<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesMergedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices_mergeds', function (Blueprint $table) {
            $table->id();
            $table->integer('merged_invoice_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->BigInteger('product_qty')->default(0);
            $table->double('product_price', 15, 2)->default('0.00');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices_mergeds');
    }
}
