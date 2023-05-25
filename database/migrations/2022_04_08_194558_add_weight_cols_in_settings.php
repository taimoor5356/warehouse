<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWeightColsInSettings extends Migration
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
            $table->double('lbs1_1_99', 15, 2)->nullable()->after('postage_cost_gte13');
            $table->double('lbs1_1_2', 15, 2)->nullable()->after('lbs1_1_99');
            $table->double('lbs2_1_3', 15, 2)->nullable()->after('lbs1_1_2');
            $table->double('lbs3_1_4', 15, 2)->nullable()->after('lbs2_1_3');
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
            $table->dropColumn('lbs1_1_99');
            $table->dropColumn('lbs1_2');
            $table->dropColumn('lbs2_3');
            $table->dropColumn('lbs3_4');
        });
    }
}
