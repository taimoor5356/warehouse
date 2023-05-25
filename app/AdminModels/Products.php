<?php

namespace App\AdminModels;

use App\Models\SkuProducts;
use App\Models\CustomerUser;
use App\AdminModels\Category;
use App\AdminModels\Inventory;
use App\Models\CustomerProduct;
use App\Models\OrderReturnDetail;
use App\Models\CustomerHasProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Products extends Model
{
    use SoftDeletes;
    protected $table = 'products';
    // public $timestamps = false;
    protected $guarded = ['id'];


    public static function addproduct($insArr)
  	{
  		DB::table('products')->insert($insArr);
  	}

    public static function updateproduct($data, $id = 0)
    {
      $affected = DB::table('products')
            ->where('id', $id)
            ->update($data);
    }

    public function inventory()
    {
      return $this->hasOne(Inventory::class, 'product_id');
    }

    public function category()
    {
      return $this->belongsTo(Category::class);
    }

    public function sku()
    {
      return $this->hasMany(SkuProducts::class, 'product_id');
    }

    public function sku_products()
    {
      return $this->hasMany(SkuProducts::class, 'product_id');
    }

    public function product_unit(){
      return $this->belongsTo(Units::class);
    }

    public function customerHasProduct() {
      return $this->hasMany(CustomerHasProduct::class, 'product_id', 'id');
    }

    public function customer_Product() {
      return $this->hasMany(CustomerProduct::class, 'product_id', 'id');
    }

    public function customerProductLabel() {
      return $this->belongsTo(CustomerProductLabel::class, 'product_id', 'id');
    }

    public function orderReturnDetails() {
      return $this->hasMany(OrderReturnDetail::class, 'product_id', 'id');
    }
    
    public function fifteenHighest($col) {
      if (Auth::user()->can('report_view')) {
        $data = DB::table('products')
        ->join('sku_products', 'products.id', '=', 'sku_products.product_id')
        ->join('order_details', 'sku_products.sku_id', '=', 'order_details.sku_id')
        ->select('products.name as name', DB::raw('SUM(sku_products.selling_cost*order_details.qty) as price'))
        ->groupBy('sku_products.product_id')
        ->orderBy('products.id', 'ASC')
        ->get();
        return $data->sortBy('price')->reverse()->take(15)->pluck($col)->toArray();
      }
      if (Auth::user()->hasRole('customer')){
        $customerId = Auth::user()->id;
        $customerUser = CustomerUser::where('user_id', Auth::user()->id)->first();
        if (isset($customerUser)) {
          $customerId = $customerUser->customer_id;
        }
        $customerProducts = CustomerProduct::where('customer_id', $customerId)->get();
        $data = DB::table('products')
        ->join('customer_products', 'products.id', '=', 'customer_products.product_id')
        ->join('sku_products', 'products.id', '=', 'sku_products.product_id')
        ->join('order_details', 'sku_products.sku_id', '=', 'order_details.sku_id')
        ->select('products.name as name', DB::raw('SUM(sku_products.selling_cost*order_details.qty) as price'))
        ->where('customer_products.customer_id', $customerId)
        ->groupBy('sku_products.product_id')
        ->orderBy('products.id', 'ASC')
        ->get();
        return $data->sortBy('price')->reverse()->take(15)->pluck($col)->toArray();
      }
    }

    public function product_order()
    {
      return $this->hasManyThrough(OrderDetails::class, SkuProducts::class, 'product_id', 'sku_id', 'id', 'sku_id');
    }
}
