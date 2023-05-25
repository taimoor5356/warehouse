<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_payments', function (Blueprint $table) {
            $table->id();
            // $table->integer('is_paid')->default('0');
            $table->integer('invoice_id')->nullable();
            // $table->integer('customer_id')->nullable();
            // $table->bigInteger('invoice_number')->nullable();
            // $table->double('total', 15, 2)->default('0.00')->nullable();
            $table->double('paid', 15, 2)->default('0.00')->nullable();
            $table->double('remaining', 15, 2)->default('0.00')->nullable();
            $table->string('paid_date')->nullable();
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
        Schema::dropIfExists('invoice_payments');
    }
}
