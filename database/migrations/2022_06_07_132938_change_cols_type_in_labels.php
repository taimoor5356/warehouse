<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColsTypeInLabels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('labels', function (Blueprint $table) {
            //
            $table->bigInteger('customer_id')->nullable()->change();
            $table->string('brand')->nullable()->change();
            $table->bigInteger('qty')->default('0')->change();
            $table->decimal('mailer_cost', 15, 2)->default('0.00')->change();
            $table->dateTime('date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('labels', function (Blueprint $table) {
            //
        });
    }
}
