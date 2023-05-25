<?php

namespace Database\Seeders;

use App\AdminModels\Inventory;
use App\AdminModels\InventoryHistory;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        InventoryHistory::where('id', 291991)->where('product_id', 77)->update([
            'manual_reduce' => 2094,
        ]);
        InventoryHistory::where('id', 291995)->where('product_id', 78)->update([
            'manual_reduce' => 891,
        ]);
        InventoryHistory::where('id', 292001)->where('product_id', 81)->update([
            'manual_reduce' => 63,
        ]);
        InventoryHistory::where('id', 292004)->where('product_id', 87)->update([
            'manual_add' => 15,
        ]);

        return 'Inventory History Updated';
        // InventoryHistory::where('id', '=', 285556)->update([
        //     'return_add' => 1
        // ]);
        // $products = DB::table('products')->orderBy('id', 'ASC')->get();
        // foreach ($products as $key => $product) {
        //     if (isset($product)) {
        //         $inventoryHistory = DB::table('inventory_history')->where('product_id', $product->id)->whereDate('created_at', '>=', '2022-07-25')->orderBy('id', 'ASC')->orderBy('created_at', 'ASC');
        //         if ($inventoryHistory->exists()) {
        //             $inventoryHistory = $inventoryHistory->get();
        //             $productPreviousInventory = 0;
        //             $getOnePreviousValue = DB::table('inventory_history')->where('product_id', $product->id)->whereDate('created_at', '>=', Carbon::parse($inventoryHistory->first()->created_at)->subDay())->orderBy('id', 'ASC')->orderBy('created_at', 'ASC')->first();
        //             if (isset($getOnePreviousValue)) {
        //                 $productPreviousInventory = $getOnePreviousValue->total;
        //             }
        //             $prevExists = DB::table('inventory_history')->where('product_id', $product->id)->whereDate('created_at', '<=', Carbon::parse($inventoryHistory->first()->created_at)->subDay())->orderBy('id', 'ASC')->orderBy('created_at', 'ASC')->exists();
        //             if (!($prevExists)) {
        //                 $productPreviousInventory = 0;
        //             }
        //             echo $product->id.", ";
        //             foreach ($inventoryHistory as $invHiskey => $history) {
        //                 if (isset($history)) {
        //                     $manualAdd = $history->manual_add;
        //                     $editBatchQty = $history->edit_batch_qty;
        //                     $cancelOrder = $history->cancel_order_add;
        //                     $supplierReceived = $history->supplier_inventory_received;
        //                     $returnAdd = $history->return_add;
        //                     $returnEdited = $history->return_edited;
        //                     $manualReduce = $history->manual_reduce;
        //                     $sales = $history->sales;
        //                     // $total = $history->total;
        //                     $productPreviousInventory =  $productPreviousInventory + ($manualAdd + $editBatchQty + $cancelOrder + $supplierReceived + $returnAdd) - $returnEdited - $manualReduce - $sales;
        //                     InventoryHistory::where('product_id', $history->product_id)->where('id', $history->id)->update([
        //                         'total' => $productPreviousInventory
        //                     ]);
        //                     Inventory::where('product_id', $product->id)->update([
        //                         'qty' => $productPreviousInventory
        //                     ]);
        //                 }
        //             }
        //         }
        //     }
        // }

        //
        // InventoryHistory::where('sales', '<', 0)->update([
        //     'sales' => 0
        // ]);
        // $products = DB::table('products')->orderBy('id', 'ASC')->where('id', 19)->get();
        // foreach ($products as $key => $product) {
        //     if (isset($product)) {
        //         $inventoryHistory = DB::table('inventory_history')->where('product_id', $product->id)->whereDate('created_at', '<=', '2022-07-24')->orderBy('created_at', 'DESC');
        //         if ($inventoryHistory->exists()) {
        //             $inventoryHistory = $inventoryHistory->get();
        //             $productPreviousInventory = 0;
        //             $getOnePreviousValue = DB::table('inventory_history')
        //                                 ->where('id', '>', $inventoryHistory->first()->id)
        //                                 ->where('product_id', $product->id)
        //                                 ->orderBy('id', 'ASC')
        //                                 ->first();
        //             if (isset($getOnePreviousValue)) {
        //                 $productPreviousInventory = $getOnePreviousValue->total + $getOnePreviousValue->return_edited + $getOnePreviousValue->manual_reduce + $getOnePreviousValue->sales - $getOnePreviousValue->manual_add - $getOnePreviousValue->edit_batch_qty - $getOnePreviousValue->cancel_order_add - $getOnePreviousValue->supplier_inventory_received - $getOnePreviousValue->return_add;
        //             }
        //             $prevExists = DB::table('inventory_history')
        //                         ->where('id', '>', $inventoryHistory->first()->id)
        //                         ->where('product_id', $product->id)
        //                         ->orderBy('id', 'ASC')
        //                         ->exists();
        //             if (!($prevExists)) {
        //                 $productPreviousInventory = 0;
        //             }
        //             // dd($productPreviousInventory);
        //             echo $product->id.", ";
        //             foreach ($inventoryHistory as $invHiskey => $history) {
        //                 if (isset($history)) {
        //                     // echo $history->total."<br>";
        //                     InventoryHistory::where('product_id', $history->product_id)->where('id', $history->id)->update([
        //                         'total' => $productPreviousInventory
        //                     ]);
        //                     $manualAdd = $history->manual_add;
        //                     $editBatchQty = $history->edit_batch_qty;
        //                     $cancelOrder = $history->cancel_order_add;
        //                     $supplierReceived = $history->supplier_inventory_received;
        //                     $returnAdd = $history->return_add;
        //                     $returnEdited = $history->return_edited;
        //                     $manualReduce = $history->manual_reduce;
        //                     $sales = $history->sales;
        //                     $total = $history->total;
        //                     $productPreviousInventory =  $productPreviousInventory + ($returnEdited + $manualReduce + $sales) - $manualAdd - $editBatchQty - $cancelOrder - $supplierReceived - $returnAdd;
        //                     // InventoryHistory::where('product_id', $history->product_id)->where('id', $history->id)->update([
        //                     //     'total' => $productPreviousInventory
        //                     // ]);
        //                     // Inventory::where('product_id', $product->id)->update([
        //                     //     'qty' => $productPreviousInventory
        //                     // ]);
        //                 }
        //             }
        //         }
        //     }
        // }
    }
}
