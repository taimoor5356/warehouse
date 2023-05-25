<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPickPackFlatInSettings extends Migration
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
            $table->double('pick_pack_flat', 15,2)->nullable()->after('postage_cost_gte13');
            // $table->integer('pick_pack_flat_status')->nullable()->after('pick_pack_flat')->default('0');
            // $table->integer('default_pick_pack_flat_status')->nullable()->after('pick_pack_flat_status')->default('0');
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
