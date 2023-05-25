<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesInTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('customer_has_products', function (Blueprint $table) {
        //     //
        //     $table->bigInteger('customer_id')->index()->change();
        //     $table->bigInteger('product_id')->index()->change();
        //     $table->bigInteger('brand_id')->index()->change();
        //     $table->bigInteger('merged_customer_id')->index()->change();
        //     $table->bigInteger('merged_brand_id')->index()->change();
        //     // $table->timestamp('created_at')->index()->change();
        // });
        // Schema::table('customer_has_skus', function (Blueprint $table) {
        //     //
        //     $table->bigInteger('customer_id')->index()->change();
        //     $table->bigInteger('sku_id')->index()->change();
        //     $table->bigInteger('brand_id')->index()->change();
        //     // $table->timestamp('created_at')->index()->change();
        // });
        // Schema::table('customer_ledgers', function (Blueprint $table) {
        //     //
        //     $table->bigInteger('customer_id')->index()->change();
        //     $table->bigInteger('order_id')->index()->change();
        //     $table->bigInteger('product_id')->index()->change();
        //     // $table->timestamp('created_at')->index()->change();
        // });
        // Schema::table('customer_products', function (Blueprint $table) {
        //     //
        //     $table->bigInteger('merged_customer_id')->index()->change();
        // });
        // Schema::table('customer_product_labels', function (Blueprint $table) {
        //     //
        //     $table->bigInteger('customer_id')->index()->change();
        //     $table->bigInteger('product_id')->index()->change();
        //     $table->bigInteger('brand_id')->index()->change();
        //     // $table->timestamp('created_at')->index()->change();
        // });
        // Schema::table('customer_users', function (Blueprint $table) {
        //     //
        //     $table->bigInteger('customer_id')->index()->change();
        //     $table->bigInteger('user_id')->index()->change();
        //     // $table->timestamp('created_at')->index()->change();
        // });
        // Schema::table('inventory', function (Blueprint $table) {
        //     //
        //     $table->bigInteger('product_id')->index()->change();
        //     // $table->timestamp('created_at')->index()->change();
        // });
        // Schema::table('inventory_history', function (Blueprint $table) {
        //     //
        //     // $table->bigInteger('product_id')->index()->change();
        //     $table->bigInteger('customer_id')->index()->change();
        //     $table->bigInteger('return_order_id')->index()->change();
        //     $table->bigInteger('order_id')->index()->change();
        //     $table->bigInteger('sku_id')->index()->change();
        //     // $table->timestamp('created_at')->index()->change();
        // });
        // Schema::table('invoices', function (Blueprint $table) {
        //     //
        //     $table->bigInteger('order_id')->index()->change();
        //     $table->bigInteger('inv_no')->index()->change();
        //     $table->bigInteger('customer_id')->index()->change();
        //     // $table->timestamp('created_at')->index()->change();
        // });
        // Schema::table('invoices_mergeds', function (Blueprint $table) {
        //     //
        //     $table->bigInteger('inv_no')->index()->change();
        //     $table->bigInteger('invoice_id')->index()->change();
        //     $table->bigInteger('order_id')->index()->change();
        //     $table->bigInteger('merged_invoice_id')->index()->change();
        //     $table->bigInteger('product_id')->index()->change();
        //     // $table->timestamp('created_at')->index()->change();
        // });
        // Schema::table('invoice_details', function (Blueprint $table) {
        //     //
        //     $table->bigInteger('sku_id')->index()->change();
        //     $table->bigInteger('invoice_id')->index()->change();
        //     // $table->timestamp('created_at')->index()->change();
        // });
        // Schema::table('invoice_payments', function (Blueprint $table) {
        //     //
        //     $table->bigInteger('invoice_id')->index()->change();
        //     // $table->timestamp('created_at')->index()->change();
        // });
        // Schema::table('labels', function (Blueprint $table) {
        //     //
        //     $table->bigInteger('customer_id')->index()->change();
        //     // $table->timestamp('created_at')->index()->change();
        // });
        // Schema::table('labels_history', function (Blueprint $table) {
        //     //
        //     $table->bigInteger('customer_id')->index()->change();
        //     $table->bigInteger('brand_id')->index()->change();
        //     // $table->timestamp('created_at')->index()->change();
        // });
        // Schema::table('merged_brand_products', function (Blueprint $table) {
        //     //
        //     $table->bigInteger('customer_id')->index()->change();
        //     $table->bigInteger('selected_brand')->index()->change();
        //     $table->bigInteger('merged_brand')->index()->change();
        //     $table->bigInteger('product_id')->index()->change();
        //     // $table->timestamp('created_at')->index()->change();
        // });
        // Schema::table('merged_invoices', function (Blueprint $table) {
        //     //
        //     $table->bigInteger('customer_id')->index()->change();
        //     // $table->timestamp('created_at')->index()->change();
        // });
        // Schema::table('orders', function (Blueprint $table) {
        //     //
        //     $table->bigInteger('brand_id')->index()->change();
        //     $table->bigInteger('status')->index()->change();
        //     $table->bigInteger('merged')->index()->change();
        //     $table->bigInteger('merge_running')->index()->change();
        // });
        // Schema::table('order_returns', function (Blueprint $table) {
        //     //
        //     $table->bigInteger('order_id')->index()->change();
        //     $table->bigInteger('customer_id')->index()->change();
        //     $table->bigInteger('brand_id')->index()->change();
        //     // $table->timestamp('created_at')->index()->change();
        // });
        // Schema::table('order_return_details', function (Blueprint $table) {
        //     //
        //     $table->bigInteger('order_return_id')->index()->change();
        //     $table->bigInteger('brand_id')->index()->change();
        //     $table->bigInteger('product_id')->index()->change();
        //     // $table->timestamp('created_at')->index()->change();
        // });
        // Schema::table('otw_inventory', function (Blueprint $table) {
        //     //
        //     $table->bigInteger('product_id')->index()->change();
        //     // $table->timestamp('created_at')->index()->change();
        // });
        // Schema::table('products', function (Blueprint $table) {
        //     //
        //     $table->bigInteger('category_id')->index()->change();
        //     $table->bigInteger('product_unit_id')->index()->change();
        //     // $table->timestamp('created_at')->index()->change();
        // });
        // Schema::table('product_label_orders', function (Blueprint $table) {
        //     //
        //     $table->bigInteger('order_id')->index()->change();
        //     $table->bigInteger('sku_id')->index()->change();
        // });
        // Schema::table('product_order_details', function (Blueprint $table) {
        //     //
        //     $table->bigInteger('order_id')->index()->change();
        //     $table->bigInteger('product_id')->index()->change();
        //     $table->bigInteger('sku_id')->index()->change();
        //     // $table->timestamp('created_at')->index()->change();
        // });
        // Schema::table('skus', function (Blueprint $table) {
        //     //
        //     $table->bigInteger('brand_id')->index()->change();
        //     // $table->timestamp('created_at')->index()->change();
        // });
        // Schema::table('sku_orders', function (Blueprint $table) {
        //     //
        //     $table->bigInteger('order_id')->index()->change();
        //     $table->bigInteger('customer_id')->index()->change();
        //     $table->bigInteger('sku_id')->index()->change();
        //     $table->bigInteger('brand_id')->index()->change();
        //     // $table->timestamp('created_at')->index()->change();
        // });
        // Schema::table('sku_order_details', function (Blueprint $table) {
        //     //
        //     $table->bigInteger('sku_id')->index()->change();
        //     $table->bigInteger('sku_product_id')->index()->change();
        //     $table->bigInteger('order_id')->index()->change();
        //     $table->bigInteger('sku_order_id')->index()->change();
        //     $table->bigInteger('customer_id')->index()->change();
        //     $table->bigInteger('brand_id')->index()->change();
        //     $table->bigInteger('product_id')->index()->change();
        //     // $table->timestamp('created_at')->index()->change();
        // });
        // Schema::table('sku_products', function (Blueprint $table) {
        //     //
        //     // $table->timestamp('created_at')->index()->change();
        // });
        // Schema::table('upcoming_inventory', function (Blueprint $table) {
        //     //
        //     $table->bigInteger('product_id')->index()->change();
        //     // $table->timestamp('created_at')->index()->change();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
