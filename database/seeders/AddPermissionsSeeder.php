<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class AddPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $arr = ['Order Update' => 'order_update', 'Order Delete' => 'order_delete'];
        $arr = [
            'Sales Report' => 'report_sales_view',
            'Profit Report' => 'report_profit_view',
            'Inventory Forecast Report' => 'report_inventory_forecast_view',
            'Inventory History Report' => 'report_inventory_history_view',
            'Brands Report' => 'report_brands_view',
            'Labels Forecast' => 'report_labels_forecast_view',
            'All Product Report' => 'report_all_products_view',
            'Product In/Out Report' => 'report_products_in_out_view',
            'Products Brand Report' => 'report_products_brand_view',
            'Labels Report' => 'report_labels_view',
            'Postage Report' => 'report_postage_view',
            'Returned Report' => 'report_returned_view'
        ];
        //
        foreach ($arr as $key => $permission) {
            Permission::create([
                'name' => $permission,
                'slug' => $key,
                'guard_name' => 'web'
            ]);
        }
    }
}
