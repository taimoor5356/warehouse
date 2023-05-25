<?php

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\FloatType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetCogShippingCostInProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            //
            if (!Type::hasType('double')) {
                Type::addType('double', FloatType::class);
            }
            $table->double('cog', 15, 2)->nullable()->change();
            $table->double('shipping_cost', 15, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
}
