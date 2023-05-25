<?php

namespace App\Repositories;

use DataTables;
use App\Traits\Forecast;
use App\AdminModels\Units;
use App\AdminModels\Inventory;
use App\AdminModels\OtwInventory;
use Illuminate\Support\Facades\Auth;
use App\AdminModels\UpcomingInventory;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Contracts\DataTable;
use App\Interfaces\InventoryRepositoryInterface;

class InventoryRepository implements InventoryRepositoryInterface 
{
    use Forecast;
    public function inventoryDetails($request) 
    {
          $data = $this->forecastData($request->category_id);
          function getQtyAlerts($row)
          {
            if ($row->forecast_status == 1) {
              if ($row->invent_qty < $row->manual_threshold) {
                return '<span class="badge rounded-pill me-1" style="background-color: red; color: white">' . $row->invent_qty . '</span>';
              } else {
                return $row->invent_qty;
              }
            } else {
              return $row->invent_qty;
            }
          }
          $totalCost = 0;
          $totalQty = 0;
          foreach ($data as $key => $rowData) {
            $totalCost += $rowData->price * $rowData->invent_qty;
            $totalQty += $rowData->invent_qty;
          }
          return ['data' => Datatables::of($data)
          ->addIndexColumn()
          ->addColumn('btn', function ($row) {
              return '<div class="text-center"><img src="/images/details_open.png" class="details-control tbl_clr" style="cursor: pointer"></div>';
          })
          ->addColumn('category_name', function ($row) {
            if (isset($row->category)) {
              return ucwords($row->category->name);
            } else {
              if (!is_null($row->category_name)) {
                return ucwords($row->category_name);
              } else {
                return '';
              }
            }
          })
          ->addColumn('name', function ($row) {
            return ucwords($row->name);
          }) 
          ->addColumn('unit_cost', function ($row) {
              return $row->price;
          })
          ->addColumn('pqty', function ($row) {
            return $row->invent_qty;
          })
          ->addColumn('total_cost', function ($row) {
              return $row->price * $row->invent_qty;
          })
          ->rawColumns(['name', 'action', 'is_active', 'image', 'forecast_val', 'pqty', 'forecast_statuses', 'btn'])
          ->make(true), 'total_qty' => $totalQty, 'total_cost' => $totalCost];
    }

    public function getProductById($productId) 
    {
        return Inventory::findOrFail($productId);
    }

    public function deleteProduct($productId) 
    {
        Inventory::destroy($productId);
    }

    public function createProduct(array $productDetails) 
    {
        return Inventory::create($productDetails);
    }

    public function updateProduct($productId, array $newDetails) 
    {
        return Inventory::whereId($productId)->update($newDetails);
    }

    public function getFulfilledProducts() 
    {
        return Inventory::where('is_fulfilled', true);
    }
}
