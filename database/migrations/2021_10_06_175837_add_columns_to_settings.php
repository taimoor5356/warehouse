<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            //
            $table->integer('threshold_val')->default('0')->after('forecast_days');
            $table->decimal('labels', 8,2)->default('0')->after('threshold_val');
            $table->decimal('pick', 8,2)->default('0')->after('labels');
            $table->decimal('pack', 8,2)->default('0')->after('pick');
            $table->decimal('mailer', 8,2)->default('0')->after('pack');
            $table->decimal('postage_cost', 8,2)->default('0')->after('mailer');
            $table->decimal('postage_cost_lt5', 8,2)->default('0')->after('postage_cost');
            $table->decimal('postage_cost_lt9', 8,2)->default('0')->after('postage_cost_lt5');
            $table->decimal('postage_cost_lt13', 8,2)->default('0')->after('postage_cost_lt9');
            $table->decimal('postage_cost_gte13', 8,2)->default('0')->after('postage_cost_lt13');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            //
        });
    }
}
