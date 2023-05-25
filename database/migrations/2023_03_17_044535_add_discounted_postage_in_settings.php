<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountedPostageInSettings extends Migration
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
            $table->double('discounted_postage_cost_lt5', 8,2)->default(0.00)->after('postage_cost_lt5');
            $table->double('discounted_postage_cost_lt9', 8,2)->default(0.00)->after('postage_cost_lt9');
            $table->double('discounted_postage_cost_lt13', 8,2)->default(0.00)->after('postage_cost_lt13');
            $table->double('discounted_postage_cost_gte13', 8,2)->default(0.00)->after('postage_cost_gte13');
            $table->double('discounted_lbs1_1_99', 8,2)->default(0.00)->after('lbs1_1_99');
            $table->double('discounted_lbs1_1_2', 8,2)->default(0.00)->after('lbs1_1_2');
            $table->double('discounted_lbs2_1_3', 8,2)->default(0.00)->after('lbs2_1_3');
            $table->double('discounted_lbs3_1_4', 8,2)->default(0.00)->after('lbs3_1_4');
            $table->double('discounted_postage_cost', 8,2)->default(0.00)->after('postage_cost');
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
            $table->dropColumn('discounted_postage_cost_lt5');
            $table->dropColumn('discounted_postage_cost_lt9');
            $table->dropColumn('discounted_postage_cost_lt13');
            $table->dropColumn('discounted_postage_cost_gte13');
            $table->dropColumn('discounted_lbs1_1_99');
            $table->dropColumn('discounted_lbs1_1_2');
            $table->dropColumn('discounted_lbs2_1_3');
            $table->dropColumn('discounted_lbs3_1_4');
            $table->dropColumn('discounted_postage_cost');
        });
    }
}
