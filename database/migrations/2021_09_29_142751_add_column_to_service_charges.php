<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToServiceCharges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_charges', function (Blueprint $table) {
            $table->renameColumn('postage', 'postage_cost_lt5')->default(0.0);
            $table->decimal('postage_cost_lt9')->after('postage')->default(0.0);
            $table->decimal('postage_cost_lt13')->after('postage_cost_lt9')->default(0.0);
            $table->decimal('postage_cost_gte13')->after('postage_cost_lt13')->default(0.0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_charges', function (Blueprint $table) {
            //
        });
    }
}
