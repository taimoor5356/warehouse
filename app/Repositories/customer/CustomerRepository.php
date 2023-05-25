<?php

namespace App\Repositories\customer;

use DateTime;
use Exception;
use DataTables;
use Carbon\Carbon;
use App\Models\Sku;
use App\Models\User;
use App\Models\Setting;
use App\AdminModels\Labels;
use App\Models\SkuProducts;
use App\Models\CustomerUser;
use Illuminate\Http\Request;
use App\AdminModels\Products;
use App\AdminModels\Customers;
use App\Models\CustomerHasSku;
use App\Models\ServiceCharges;
use App\Models\CustomerProduct;
use Illuminate\Validation\Rule;
use App\AdminModels\OrderDetails;
use App\Models\ProductLabelOrder;
use App\AdminModels\LabelsHistory;
use App\Models\CustomerHasProduct;
use App\Models\MergedBrandProduct;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Repositories\CategoryInterface;

class CustomerRepository implements CustomerInterface
{
    //
  public function index(Request $request)
  {
    if ($request->ajax()) {
      $columns = $request->get('columns');
      $orders = $request->get('order');
      // $orderbyColAndDirection = [];
      // foreach ($orders as $key => $value) {
      //   array_push($orderbyColAndDirection, $columns[$value['column']]['data'] . ' ' . $value['dir']);
      // }
      // $orderbyColAndDirection = implode(", ", $orderbyColAndDirection);
      $data = array();
      if (Auth::user()->can('customer_view')) {
        $data = Customers::with('customer_product', 'brands', 'service_charges', 'sku', 'brands.cust_has_prod.products')->latest('updated_at')->get();
        if (Auth::user()->hasRole('customer')) {
          $customerUser = CustomerUser::where('user_id', Auth::user()->id)->first();
          $customerId = Auth::user()->id;
          if (isset($customerUser)) {
            $customerId = $customerUser->customer_id;
          }
          $data = Customers::with('customer_product', 'brands', 'product', 'service_charges', 'sku', 'brands.cust_has_prod.products')->where('id', $customerId)->latest('updated_at')->get();
        }
      } else {
        $customerUser = CustomerUser::where('user_id', Auth::user()->id)->first();
        $customerId = Auth::user()->id;
        if (isset($customerUser)) {
          $customerId = $customerUser->customer_id;
        }
        $data = Customers::with('customer_product', 'brands', 'product', 'service_charges', 'sku', 'brands.cust_has_prod.products')->where('id', $customerId)->latest('updated_at')->get();
      }
      return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('customer_name', function ($row) {
          return ucwords($row->customer_name);
        })
        ->addColumn('phone', function ($row) {
          return ucwords($row->phone);
        })
        ->addColumn('email', function ($row) {
          return ($row->email);
        })
        ->addColumn('address', function ($row) {
          return ucwords($row->address);
        })
        ->addColumn('charges', function ($row) {
          $button = '<i data-feather="eye" data-bs-toggle="modal" data-bs-target="#charges_' . $row->id . '_' . $row->charges_id . '"></i>';
          $modal = '<div class="modal fade text-start show" id="charges_' . $row->id . '_' . $row->charges_id . '" tabindex="-1" aria-labelledby="myModalLabel33" aria-modal="true" role="dialog">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="myModalLabel33">Product Details</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                ';
          $modal .= '<form action="" method="post">
                        <div class="modal-body">';
          if ($row->customer_product != null) {
            $modal .= '
                <div class="col-md-12">
                  <div class="container">
                    <h5>Customer Products</h5>
                      <table class="table table-borderd customer_products_table">
                        <thead>
                        <th>Product</th>
                        <th>Purchasing Cost</th>
                        <th>Weight</th>
                        <th>Selling Cost</th>
                        </thead>
                        <tbody>
                          ';
            foreach ($row->customer_product as $cProduct) {
              $prodId = '';
              $prodName = '';
              $purchasePrice = '';
              $weight = '';
              $product = Products::where('id', $cProduct->product_id)->first();
              if (isset($product)) {
                $prodId = $product->id;
                $prodName = $product->name;
                $purchasePrice = $product->price;
                $weight = $product->weight;
              }
              $modal .= '
                            <tr>
                              <td>' . $prodName . '</td>
                              <td>$ ' . $purchasePrice . '</td>
                              <td>' . $weight . '</td>
                              <td>$ ' . number_format($cProduct->selling_price, 2) . '</td>
                            </tr>
                            ';
            }
            $modal .= '
                      </tbody>
                    </table>
                </div>
              </div>
              <hr>';
          }
          if ($row->brands != null) {
            $modal .= '
              <div class="col-md-12">
                <div class="container">
                  <h5>Customer Brands</h5>
                    <table class="table table-borderd cell-border customer_products_table">
                      <thead>
                      <th>Brand</th>
                      <th>Total SKUs</th>
                      </thead>
                      <tbody>
                        ';
            foreach ($row->brands as $cBrand) {
              $brandName = '';
              $brandDate = '';
              $getBrand = Labels::with('sku')->where('id', $cBrand->id)->first();
              if (isset($getBrand)) {
                $brandName = $getBrand->brand;
                $brandDate = $getBrand->date;
              }
              $modal .= '
                          <tr>
                            <td>' . $brandName . '</td>
                            <td>' . $getBrand->sku->count() . '</td>
                          </tr>
                          ';
            }
            $modal .= '
                    </tbody>
                  </table>
              </div>
              </div>
              <hr>';
          }
          $modal .= '
            <div class="col-md-12">
                <div class="container">
                  <h5>Service Charges</h5>
                    <table class="table table-bordered">
                    <thead>
                      <th>
                        Service Charges
                      </th>
                      <th>
                        Price
                      </th>
                    </thead>
                      <tbody>';
          if ($row->service_charges != null) {
            $modal .= '<tr>
                            <th>Labels Charges</th>
                            <td>$ ' . $row->service_charges->labels . '</td>
                          </tr>';
            $modal .= '<tr>
                          <th>Pick Charges</th>
                          <td>$ ' . $row->service_charges->pick . '</td>
                        </tr>';
            $modal .= '<tr>
                          <th>Pack Charges</th>
                          <td>$ ' . $row->service_charges->pack . '</td>
                        </tr>';
            $modal .= '<tr>
                          <th>Pick / Pack Flat Rates</th>
                          <td>$ ' . number_format($row->service_charges->pick_pack_flat, 2) . '</td>
                        </tr>';
            $modal .= '<tr>
                          <th>Mailer Charges</th>
                          <td>$ ' . $row->service_charges->mailer . '</td>
                        </tr>';
            $modal .= '<tr>
                          <th>Custom Postage Charges</th>
                          <td>$ ' . $row->service_charges->postage_cost . '</td>
                        </tr>';
            $modal .= '<tr>
                        <th>Return Service Charges</th>
                        <td>$ ' . $row->service_charges->return_service_charges . '</td>
                      </tr>';
          } else {
            $modal .= "<tr>
                <td>No Services Charges added yet</td>
              </tr>";
          }
          $modal .= '                    </tbody>
                                        </table></div></div>';
          $modal .= '
            <hr>
            <div class="col-md-12">
                <div class="container">
                  <h5>Postage Charges</h5>
                    <table class="table table-bordered">
                    <thead>
                      <th>
                        Postage Charges
                      </th>
                      <th>
                        Price
                      </th>
                    </thead>
                      <tbody>';
          if ($row->service_charges != null) {
            $modal .= '<tr>
                          <th>&nbsp;&nbsp;&nbsp;1 - 4 oz</th>
                          <td>$ ' . $row->service_charges->postage_cost_lt5 . '</td>
                        </tr>';
            $modal .= '<tr>
                        <th>&nbsp;&nbsp;&nbsp;5 - 8 oz</th>
                        <td>$ ' . $row->service_charges->postage_cost_lt9 . '</td>
                      </tr>';
            $modal .= '<tr>
                        <th>&nbsp;&nbsp;&nbsp;9 - 12 oz</th>
                        <td>$ ' . $row->service_charges->postage_cost_lt13 . '</td>
                      </tr>';
            $modal .= '<tr>
                      <th>&nbsp;&nbsp;&nbsp;13 - 15.99 oz</th>
                      <td>$ ' . $row->service_charges->postage_cost_gte13 . '</td>
                    </tr>';
                    $modal .= '<tr>
                              <th>&nbsp;&nbsp;&nbsp;1 lbs</th>
                              <td>$ ' . $row->service_charges->lbs1_1_99 . '</td>
                            </tr>';
                            $modal .= '<tr>
                                      <th>&nbsp;&nbsp;&nbsp;1.01 - 2 lbs</th>
                                      <td>$ ' . $row->service_charges->lbs1_1_2 . '</td>
                                    </tr>';
                                    $modal .= '<tr>
                                              <th>&nbsp;&nbsp;&nbsp;2.01 - 3 lbs</th>
                                              <td>$ ' . $row->service_charges->lbs2_1_3 . '</td>
                                            </tr>';
                                            $modal .= '<tr>
                                                      <th>&nbsp;&nbsp;&nbsp;3.01 - 4 lbs</th>
                                                      <td>$ ' . $row->service_charges->lbs3_1_4 . '</td>
                                                    </tr>';
          } else {
            $modal .= "<tr>
                <td>No Services Charges added yet</td>
              </tr>";
          }
          $modal .= '                    </tbody>
                                        </table></div></div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-dark waves-effect waves-float waves-light" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>';
          return $button . $modal;
        })
        ->addColumn('is_active', function ($row) {
          if ($row->is_active == 0) return '<span class="badge rounded-pill badge-light-danger me-1">Not Active</span>';
          if ($row->is_active == 1) return '<span class="badge rounded-pill badge-light-success me-1">Active</span>';
        })
        ->addColumn('action', function ($row) {
          $customerName = ucwords($row->customer_name);
          $btn = '<div class="dropdown">
                      <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                          <i data-feather="more-vertical"></i>
                      </button>
                      <div class="dropdown-menu">';
            if (Auth::user()->can('customer_create')) {
              $btn .= '<a class="dropdown-item" id="" href="/customer/' . $row->id . '/show_all" data-brand_id="" data-customer_id="' . $row->id . '" data-product-id="">
                        <i data-feather="settings"></i>
                        <span>Quick Setup</span>
                    </a>';
            if (Auth::user()->can('customer_update')) {
              $btn .= '<a class="dropdown-item" href="/customers/' . $row->id . '/edit">
                              <i data-feather="edit-2"></i>
                              <span>Edit Customer Info</span>
                          </a>';
            }
            if (Auth::user()->can('customer_create')) {
              $btn .= '<a class="dropdown-item view_cust_products" id="" href="#" data-brand_id="" data-bs-target="#show_cust_product_modal" data-customer_id="' . $row->id . '" data-product-id="" data-bs-toggle="modal">
                          <i data-feather="plus"></i>
                          <span>Add Products</span>
                      </a>';
            }
            if (Auth::user()->can('customer_view')) {
              $btn .= '<a class="dropdown-item" id="" href="/customer_products/'.$row->id.'" data-customer_id="' . $row->id . '">
                          <i data-feather="eye"></i>
                          <span>Customer Products</span>
                      </a>';
            }
            if (Auth::user()->can('customer_create')) {
              $btn .= '<a class="dropdown-item" onclick="window.open(`/customer/' . $row->id . '/brand/create`, ``, `width=1300, height=700`);" data-customer_id="' . $row->id . '" data-bs-toggle="modal" data-customer_name="' . $customerName . '">
                                <i data-feather="plus"></i>
                                <span>Add Brands</span>
                            </a>';
            }
            if (Auth::user()->can('customer_create')) {
              $btn .= '<a class="dropdown-item" onclick="window.open(`/add_brand_products/' . $row->id . '`, ``, `width=1300, height=700`);" data-customer_id="' . $row->id . '" data-bs-toggle="modal" data-customer_name="' . $customerName . '">
                                      <i data-feather="plus"></i>
                                      <span>Add Brand Products</span>
                                  </a>';
            }
            if (Auth::user()->can('customer_create')) {
              $btn .= '<a class="dropdown-item" onclick="window.open(`/add_customer_label/' . $row->id . '`, ``, `width=1200, height=700`);" data-customer_id="">
                                      <i data-feather="plus"></i>
                                      <span>Add Labels</span>
                                  </a>';
            }
            if (Auth::user()->can('customer_create')) {
              $btn .= '<a class="dropdown-item" onclick="window.open(`/sku/create?customer_id=' . $row->id . '`, ``, `width=1200, height=700`);">
                                            <i data-feather="plus"></i>
                                            <span>Add SKUs</span>
                                        </a>';
            }
            if (Auth::user()->can('customer_delete')) {
              $btn .= '<a class="dropdown-item" onclick="window.open(`customer_trashed_products/' . $row->id . '`, ``, `width=1200, height=700`);">
                                            <i data-feather="plus"></i>
                                            <span>Trashed Products</span>
                                        </a>';
            }
            if (Auth::user()->can('customer_create')) {
              $btn .= '<a class="dropdown-item" onclick="window.open(`agent-commission/' . $row->id . '`, ``, `width=1200, height=700`);">
                                            <i data-feather="edit-2"></i>
                                            <span>Manage Commission</span>
                                        </a>';
            }
            if (Auth::user()->can('customer_delete')) {
              $btn .= '<a class="dropdown-item enter-pincode" data-title_name="' . $row->customer_name . '"  href="/delete/customer/' . $row->id . '" data-type="delete"  data-bs-toggle="modal" data-bs-target="#enter_pin_Modal">
                        <i data-feather="trash"></i>
                        <span>Delete Customer</span>
                    </a>';
            }
          }
          $btn .= '</div></div>';
          return $btn;
        })
        ->rawColumns(['action', 'is_active', 'charges'])
        ->make(true);
    }
    $data = Customers::with('service_charges', 'sku', 'brands.cust_has_prod.products')->get();
    return view('admin.customers.customers', compact('data'));
  }
  public function getCustBrandProd(Request $request)
  {
    if ($request->ajax()) {
      $custBrandProds = CustomerHasProduct::where('customer_id', $request->customer_id)->where('brand_id', $request->brand_id)->get();
      return Datatables::of($custBrandProds)
        ->addIndexColumn()
        ->addColumn('product', function ($row) {
          $product = Products::where('id', $row->product_id)->first();
          if (isset($product)) {
            return ucwords($product->name);
          }
        })
        ->addColumn('label_qty', function ($row) {
          $labelQty = $row->label_qty;
          if (MergedBrandProduct::where('customer_id', $row->customer_id)->where('merged_brand', $row->brand_id)->where('product_id', $row->product_id)->exists()) {
            $mergedProduct = MergedBrandProduct::where('customer_id', $row->customer_id)->where('merged_brand', $row->brand_id)->where('product_id', $row->product_id)->first();
            if (isset($mergedProduct)) {
              $labelQty = MergedBrandProduct::where('customer_id', $row->customer_id)->where('merged_brand', $row->brand_id)->where('product_id', $row->product_id)->sum('merged_qty');
            }
          } else if (MergedBrandProduct::where('customer_id', $row->customer_id)->where('selected_brand', $row->brand_id)->where('product_id', $row->product_id)->exists()) {
            $mergedProduct = MergedBrandProduct::where('customer_id', $row->customer_id)->where('selected_brand', $row->brand_id)->where('product_id', $row->product_id)->first();
            if (isset($mergedProduct)) {
              $labelQty = MergedBrandProduct::where('customer_id', $row->customer_id)->where('selected_brand', $row->brand_id)->where('product_id', $row->product_id)->sum('merged_qty');
            }
          }
          // if ($row->merged_qty != null) {
          //   $labelQty = $row->merged_qty;
          // }
          return number_format($labelQty);
        })
        ->addColumn('label_cost', function ($row) use ($request) {
          $labelCost = number_format($row->label_cost, 2);
          if ($labelCost == 0 || $labelCost == '') {
            $serviceCharges = ServiceCharges::where('customer_id', $request->customer_id)->first();
            if (isset($serviceCharges)) {
              $labelCost = $serviceCharges->labels;
            }
          }
          return '$' . number_format($labelCost, 2);
        })
        ->addColumn('date', function ($row) {
          return $row->updated_at;
        })
        ->addColumn('status', function ($row) {
          if ($row->is_active == 0) {
            $status = '<center><span class="badge rounded-pill badge-light-success me-1" style="background-color: lightgreen; color: darkgreen">On</span></center>';
          } else {
            $status = '<center><span class="badge rounded-pill badge-light-danger me-1" style="background-color: pink; color: red">Off</span></center>';
          }
          return $status;
        })
        ->addColumn('action', function ($row) {
          $btn = '<input type="hidden" class="productId" value="' . $row->product_id . '" name="prodId[]">
                  <input type="hidden" class="brandId" value="' . $row->brand_id . '">
                  <div class="dropdown">
                    <button type="button" class="btn btn-sm dropdown-toggle hide-arrow more_btn" data-customer_id="' . $row->customer_id . '" data-product-id="" data-bs-toggle="dropdown">
                      . . .
                    </button>
                    <div class="dropdown-menu">';
          $btn .= '<a class="dropdown-item add-labels" href="#" data-brand-id="' . $row->brand_id . '" data-bs-target="#labelsModal" data-customer_id="" data-product-id=""';
          if ($row->is_active != 0) {
            $btn .= 'style="cursor: not-allowed" onclick="alert(`Please turn on the label status from customer info page`)"';
          } else {
            $btn .= 'data-bs-toggle="modal"';
          }
          $btn .= '>
            <span>Add Labels</span>
            </a>
            <a class="dropdown-item reduce-labels" href="#" data-brand-id="' . $row->brand_id . '" data-customer_id="" data-bs-target="#reducelabelsModal" data-product-id=""';
          if ($row->is_active != 0) {
            $btn .= 'style="cursor: not-allowed" onclick="alert(`Please turn on the label status from customer info page`)"';
          } else {
            $btn .= 'data-bs-toggle="modal"';
          }
          $btn .= '>
                <span>Reduce Labels</span>
            </a>
            <a class="dropdown-item add-label-cost" href="#" data-brand-id="' . $row->brand_id . '" data-bs-target="#labelCostModal" data-customer_id="' . $row->customer_id . '" data-product-id=""';
          if ($row->is_active != 0) {
            $btn .= 'style="cursor: not-allowed" onclick="alert(`Please turn on the label status from customer info page`)"';
          } else {
            $btn .= 'data-bs-toggle="modal"';
          }
          $btn .= '<span>Add Label Charges</span>
            </a>';
          $btn .= '</div></div>';
          return $btn;
        })
        ->rawColumns(['action', 'status'])
        ->make(true);
    }
  }
  public function brandProducts(Request $request)
  {
    if ($request->ajax()) {
      $custBrandProds = CustomerHasProduct::where('customer_id', $request->customer_id)->where('brand_id', $request->brand_id)->get();
      return Datatables::of($custBrandProds)
        ->addIndexColumn()
        ->addColumn('product', function ($row) {
          $product = Products::where('id', $row->product_id)->first();
          if (isset($product)) {
            return ucwords($product->name);
          }
        })
        ->addColumn('label_qty', function ($row) {
          $labelQty = $row->label_qty;
          if (MergedBrandProduct::where('customer_id', $row->customer_id)->where('merged_brand', $row->brand_id)->where('product_id', $row->product_id)->exists()) {
            $mergedProduct = MergedBrandProduct::where('customer_id', $row->customer_id)->where('merged_brand', $row->brand_id)->where('product_id', $row->product_id)->first();
            if (isset($mergedProduct)) {
              $labelQty = MergedBrandProduct::where('customer_id', $row->customer_id)->where('merged_brand', $row->brand_id)->where('product_id', $row->product_id)->sum('merged_qty');
            }
          } else if (MergedBrandProduct::where('customer_id', $row->customer_id)->where('selected_brand', $row->brand_id)->where('product_id', $row->product_id)->exists()) {
            $mergedProduct = MergedBrandProduct::where('customer_id', $row->customer_id)->where('selected_brand', $row->brand_id)->where('product_id', $row->product_id)->first();
            if (isset($mergedProduct)) {
              $labelQty = MergedBrandProduct::where('customer_id', $row->customer_id)->where('selected_brand', $row->brand_id)->where('product_id', $row->product_id)->sum('merged_qty');
            }
          }
          // if ($row->merged_qty != null) {
          //   $labelQty = $row->merged_qty;
          // }
          return number_format($labelQty);
          // return number_format($row->label_qty);
        })
        ->addColumn('label_cost', function ($row) use ($request) {
          $labelCost = number_format($row->label_cost, 2);
          if ($labelCost == 0 || $labelCost == '') {
            $serviceCharges = ServiceCharges::where('customer_id', $request->customer_id)->first();
            if (isset($serviceCharges)) {
              $labelCost = $serviceCharges->labels;
            }
          }
          return '$' . number_format($labelCost, 2);
        })
        ->addColumn('date', function ($row) {
          return $row->updated_at;
        })
        ->addColumn('status', function ($row) {
          if ($row->is_active == 0) {
            $status = '<center><span class="badge rounded-pill badge-light-success me-1" style="background-color: lightgreen; color: darkgreen">On</span></center>';
          } else {
            $status = '<center><span class="badge rounded-pill badge-light-danger me-1" style="background-color: pink; color: red">Off</span></center>';
          }
          return $status;
        })
        ->addColumn('action', function ($row) {
          $btn = '<input type="hidden" class="productId" value="' . $row->product_id . '" name="prodId[]">
                  <input type="hidden" class="brandId" value="' . $row->brand_id . '">
                  <div class="dropdown">
                    <button type="button" class="btn btn-sm dropdown-toggle hide-arrow more_btn" data-customer_id="' . $row->customer_id . '" data-product-id="" data-bs-toggle="dropdown">
                      . . .
                    </button>
                    <div class="dropdown-menu">';
          $btn .= '<a class="dropdown-item add-labels" href="#" data-brand-id="' . $row->brand_id . '" data-bs-target="#labelsModal" data-customer_id="" data-product-id=""';
          if ($row->is_active != 0) {
            $btn .= 'style="cursor: not-allowed" onclick="alert(`Please turn on the label status from customer info page`)"';
          } else {
            $btn .= 'data-bs-toggle="modal"';
          }
          $btn .= '>
            <span>Add Labels</span>
            </a>
            <a class="dropdown-item reduce-labels" href="#" data-brand-id="' . $row->brand_id . '" data-customer_id="" data-bs-target="#reducelabelsModal" data-product-id=""';
          if ($row->is_active != 0) {
            $btn .= 'style="cursor: not-allowed" onclick="alert(`Please turn on the label status from customer info page`)"';
          } else {
            $btn .= 'data-bs-toggle="modal"';
          }
          $btn .= '>
                <span>Reduce Labels</span>
            </a>
            <a class="dropdown-item add-label-cost" href="#" data-brand-id="' . $row->brand_id . '" data-bs-target="#labelCostModal" data-customer_id="' . $row->customer_id . '" data-product-id=""';
          if ($row->is_active != 0) {
            $btn .= 'style="cursor: not-allowed" onclick="alert(`Please turn on the label status from customer info page`)"';
          } else {
            $btn .= 'data-bs-toggle="modal"';
          }
          $btn .= '<span>Add Label Charges</span>
              </a>';
          $btn .= '</div></div>';
          return $btn;
        })
        ->rawColumns(['action', 'status'])
        ->make(true);
    }
    if (Auth::user()->can('customer_view')) {
      $customers = Customers::get();
    } else {
      $customerUser = CustomerUser::where('user_id', Auth::user()->id)->first();
      $customerId = Auth::user()->id;
      if (isset($customerUser)) {
        $customerId = $customerUser->customer_id;
      }
      $customers = Customers::where('id', $customerId)->get();
    }
    return view('admin.customers.brand_products', compact('customers'));
  }
  public function getCustBrandProds(Request $request)
  {
    if ($request->ajax()) {
      $custBrandProds = CustomerHasProduct::where('customer_id', $request->customer_id)->where('brand_id', $request->brand_id)->get();
      return Datatables::of($custBrandProds)
        ->addIndexColumn()
        ->addColumn('item_status', function ($row) {
          $btn = '';
          $btn .= '<select name="item_status[]"  data-product-id="' . $row->product_id . '" class="form-control item_returned_satus">
                      <option value="0">-Item Status-</option>
                      <option value="1">Return</option>
                      <option value="2">Damaged</option>
                      <option value="3">Opened</option>
                    </select>';
          $btn .= '<p class="text-danger d-none item_status_not_selected_error">Must Be Selected</p>';
          return $btn;
        })
        ->addColumn('order_number', function ($row) {
          $btn = '<input type="text" class="form-control order_number" name="order_number[]" placeholder="Enter Order Number">';
          return $btn;
        })
        ->addColumn('name', function ($row) {
          $btn = '<input type="text" class="form-control name" name="name[]" placeholder="Enter Name">';
          return $btn;
        })
        ->addColumn('state', function ($row) {
          $btn = '<input type="text" class="form-control state" name="state[]" placeholder="Enter State">';
          return $btn;
        })
        ->addColumn('product', function ($row) {
          $product = Products::where('id', $row->product_id)->first();
          if (isset($product)) {
            return '<input type="hidden" name="product_id[]" value="' . $product->id . '"><span>' . $product->name . '</span>';
          }
        })
        ->addColumn('image', function ($row) {
          $product = Products::where('id', $row->product_id)->first();
          if (isset($product)) {
            $url = asset('images/products/' . $product->image);
            return '<img src="' . $url . '" border="0" width="40" class="img-rounded" align="center" />';
          }
        })
        // ->addColumn('order_status', function ($row) {
        //   $btn = '';
        //   if ($row->status == 4) {
        //     $btn .= '<p>Cancelled</p>';
        //   } else {
        //     $btn .= '<select name="status[]" data-order-id="' . $row->id . '" class="form-control order_status_required order_status_id">
        //               <option value=""> - Return Type - </option>
        //               <option value="1">Return to Sender</option>
        //               <option value="2">Customer Return</option>
        //             </select>';
        //   }
        //   return $btn;
        // })
        ->addColumn('input_field', function ($row) {
          $product = Products::with('inventory')->where('id', $row->product_id)->first();
          $price = 0;
          $qtyAvailable = 0;
          if (isset($product)) {
            $price = $product->price;
            $qtyAvailable = $product->inventory->qty;
          }
          return '
                  <input type="text" class="form-control deduction_qty returnQuantity" data-price="' . $price . '" data-qty="' . $qtyAvailable . '" name="return_qty[]" value="0">
                  <input type="hidden" class="form-control total_amount_field" value="0" name="deducted_price[]">
                  <input type="hidden" class="form-control remaining_qty" name="remaining_qty[]" value="' . $qtyAvailable . '">
                  <p class="text-danger d-none return_qty_error">Must be greater than 0</p>
          ';
        })
        ->addColumn('description', function ($row) {
          return '<textarea class="form-control" name="description[]" placeholder="Enter Special Notes"></textarea>';
        })
        /////////////////////////////////////////
        ->addColumn('product_qty', function ($row) {
          $product = Products::with('inventory')->where('id', $row->product_id)->first();
          if (isset($product)) {
            return ($product->inventory->qty);
          }
        })
        ->addColumn('price_field', function ($row) {
          return '<input type="hidden" class="form-control total_amount_field" value="0" name="deducted_price[]">';
        })
        ->addColumn('remaining_qty', function ($row) {
          $product = Products::with('inventory')->where('id', $row->product_id)->first();
          $price = 0;
          $qtyAvailable = 0;
          if (isset($product)) {
            $price = $product->price;
            $qtyAvailable = $product->inventory->qty;
          }
          return '<input type="hidden" class="form-control remaining_qty" name="remaining_qty[]" value="' . $qtyAvailable . '">';
        })
        ->addColumn('product_price', function ($row) {
          $product = Products::where('id', $row->product_id)->first();
          if (isset($product)) {
            return '$' . number_format($product->price, 2);
          }
        })
        /////////////////////////////////////////
        ->addColumn('label_qty', function ($row) {
          $labelQty = $row->label_qty;
          if (MergedBrandProduct::where('customer_id', $row->customer_id)->where('merged_brand', $row->brand_id)->where('product_id', $row->product_id)->exists()) {
            $mergedProduct = MergedBrandProduct::where('customer_id', $row->customer_id)->where('merged_brand', $row->brand_id)->where('product_id', $row->product_id)->first();
            if (isset($mergedProduct)) {
              $labelQty = MergedBrandProduct::where('customer_id', $row->customer_id)->where('merged_brand', $row->brand_id)->where('product_id', $row->product_id)->sum('merged_qty');
            }
          } else if (MergedBrandProduct::where('customer_id', $row->customer_id)->where('selected_brand', $row->brand_id)->where('product_id', $row->product_id)->exists()) {
            $mergedProduct = MergedBrandProduct::where('customer_id', $row->customer_id)->where('selected_brand', $row->brand_id)->where('product_id', $row->product_id)->first();
            if (isset($mergedProduct)) {
              $labelQty = MergedBrandProduct::where('customer_id', $row->customer_id)->where('selected_brand', $row->brand_id)->where('product_id', $row->product_id)->sum('merged_qty');
            }
          }
          // if ($row->merged_qty != null) {
          //   $labelQty = $row->merged_qty;
          // }
          return number_format($labelQty);
          // return number_format($row->label_qty);
        })
        ->addColumn('label_cost', function ($row) use ($request) {
          $labelCost = number_format($row->label_cost, 2);
          if ($labelCost == 0 || $labelCost == '') {
            $service_charges = ServiceCharges::where('customer_id', $request->customer_id)->first();
            if (isset($service_charges)) {
              $labelCost = $service_charges->labels;
            }
          }
          return '$' . number_format($labelCost, 2);
        })
        ->addColumn('date', function ($row) {
          return $row->updated_at;
        })
        ->addColumn('status', function ($row) {
          if ($row->is_active == 0) {
            $status = '<center><span class="badge rounded-pill badge-light-success me-1" style="background-color: lightgreen; color: darkgreen">On</span></center>';
          } else {
            $status = '<center><span class="badge rounded-pill badge-light-danger me-1" style="background-color: pink; color: red">Off</span></center>';
          }
          return $status;
        })
        ->addColumn('action', function ($row) use ($request) {
          $btn = '<div class="dropdown">
                        <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i data-feather="more-vertical"></i>
                        </button>
                        <div class="dropdown-menu">';
          // if (Auth::user()->can('labels_create')) {
          $btn .= '<a class="dropdown-item" href="/delete_customer_brand_product/' . $request->customer_id . '/' . $request->brand_id . '/' . $row->product_id . '">
                            <i data-feather="trash"></i>
                            <span>Delete</span>
                        </a>';
          // }
          $btn .= '</div></div>';
          return $btn;
        })
        ->rawColumns(['status', 'input_field', 'product', 'price_field', 'remaining_qty', 'order_status', 'description', 'image', 'action', 'item_status', 'order_number', 'name', 'state'])
        ->make(true);
    }
  }
  public function getCustBrandRemainingProds(Request $request)
  {
    $getAllProducts = CustomerProduct::where('customer_id', $request->customer_id)->get();
    $products = array();
    $keys = array('prod_id', 'prod_name', 'purchasing_cost', 'weight', 'selling_cost');
    foreach ($getAllProducts as $product) {
      if (CustomerHasProduct::where('customer_id', $request->customer_id)->where('brand_id', $request->brand_id)->where('product_id', $product->product_id)->exists()) {
      } else {
        $prod = Products::where('id', $product->product_id)->first();
        if (isset($prod)) {
          array_push($products, array_combine($keys, [$prod->id, $prod->name, number_format($prod->price, 2), number_format($prod->weight, 2), number_format($prod->price, 2)]));
        }
      }
    }
    return response()->json($products);
  }
  public function customerProducts(Request $request, $id)
  {
    return view('admin.customers.customer_products', compact('id'));
  }
  public function showAll(Request $request, $id)
  {
    if ($request->ajax()) {
      $customerProducts = CustomerProduct::where('customer_id', $id)->get();
      return Datatables::of($customerProducts)
        ->addIndexColumn()
        ->addColumn('c_name', function ($row) use ($id) {
          $customer = Customers::where('id', $id)->first();
          if (isset($customer)) {
            return ucwords($customer->customer_name);
          } else {
              return 'Customer does not exist';
          }
        })
        ->addColumn('product_name', function ($row) {
          $product = Products::where('id', $row->product_id)->first();
          if (isset($product)) {
            return ucwords($product->name);
          }
        })
        ->addColumn('purchasing_cost', function ($row) {
          $product = Products::where('id', $row->product_id)->first();
          if (isset($product)) {
            return '$ ' . number_format($product->price, 2);
          }
        })
        ->addColumn('weight', function ($row) {
          $product = Products::where('id', $row->product_id)->first();
          if (isset($product)) {
            return number_format($product->weight, 2);
          }
        })
        ->addColumn('selling_cost', function ($row) {
          if ($row->seller_cost_status == 1) {
            return '$ ' . number_format($row->selling_price, 2);
          } else {
            return '$ 0.00';
          }
        })
        ->addColumn('seller_cost_status', function ($row) use ($id) {
          $html = '
                  <div class="form-check form-switch form-check-primary">
                      <input type="checkbox"';
          if ($row->seller_cost_status == 1) {
            $html .= ' checked ';
          }
          $html .= 'name="seller_cost_status[]" class="form-check-input sellerCostSwitch" data-customer_id="' . $id . '" data-labelPid="' . $row->product_id . '" style="font-size: 30px">
                      <label class="form-check-label">
                          <span class="switch-icon-left" style="font-size: 9px; margin-top: 6px">ON</span>
                          <span class="switch-icon-right" style="font-size: 9px; margin-top: 6px; margin-left: -6px; color: black">OFF</span>
                      </label>
                  </div>';
          return $html;
        })
        ->addColumn('label_status', function ($row) use ($id) {
          $html = '<div class="form-check form-check-primary form-switch">
                  <input type="checkbox"';
          if ($row->is_active == 0) {
            $html .= 'checked ';
          }
          $html .= 'name="is_active" class="form-check-input labelSwitch" data-customer_id="' . $id . '" data-labelPid="' . $row->product_id . '">
                  </div>';
          return $html;
        })
        ->addColumn('action', function ($row) {
          $btn = '
                  <div class="dropdown">
                      <button type="button" class="btn btn-sm dropdown-toggle hide-arrow more_btn" data-customer_id="' . $row->customer_id . '" data-product_id="' . $row->product_id . '" data-bs-toggle="dropdown">
                      . . .
                      </button>
                      <div class="dropdown-menu">';
          $btn .= '
                          <a class="dropdown-item edit_product_selling_price" href="#" data-bs-target="#cust_prod_selling_price" data-customer_id="' . $row->customer_id . '" data-product_id="' . $row->product_id . '" data-selling_price="' . number_format($row->selling_price, 2) . '"';
          if ($row->seller_cost_status == 0) {
            $btn .= 'style="cursor: not-allowed"';
          } else {
            $btn .= 'data-bs-toggle="modal">';
          }
          $btn .= '<span>Edit Selling Price</span>
                          </a>
                          <a href="delete_customer_prod/' . $row->customer_id . '/' . $row->product_id . '" onclick="confirmDelete(event)" class="dropdown-item">Delete Product</a>
                          ';
          $btn .= '</div>
                  </div>';
          return $btn;
        })
        ->rawColumns(['action', 'label_status', 'seller_cost_status'])
        ->make(true);
    }
    $getAllProducts = Products::get();
    $products = array();
    $keys = array('prod_id', 'prod_name', 'purchasing_cost', 'weight', 'selling_cost');
    foreach ($getAllProducts as $product) {
      if (CustomerProduct::where('customer_id', $id)->where('product_id', $product->id)->exists()) {
      } else {
        array_push($products, array_combine($keys, [$product->id, $product->name, $product->price, $product->weight, $product->price]));
      }
    }
    $customerProducts = CustomerProduct::where('customer_id', $id)->get();
    return view('admin.customers.show_all', compact('customerProducts', 'products'));
  }
  public function showAllProducts(Request $request, $id)
  {
    $getAllProducts = Products::get();
    $products = array();
    $keys = array('prod_id', 'prod_name', 'purchasing_cost', 'weight', 'selling_cost');
    foreach ($getAllProducts as $product) {
      if (CustomerProduct::where('customer_id', $id)->where('product_id', $product->id)->exists()) {
      } else {
        array_push($products, array_combine($keys, [$product->id, $product->name, number_format($product->price, 2), number_format($product->weight, 2), number_format($product->price, 2)]));
      }
    }
    return response()->json($products);
  }
  public function editCustomerBrand(Request $request)
  {
    if ($request->ajax()) {
      $brandId = $request->brand_id;
      $customerId = $request->customer_id;
      $brand = Labels::with('customer')->where('id', $brandId)->first();
      $customerBrandsProducts = CustomerHasProduct::where('customer_id', $customerId)->where('brand_id', $brandId)->get();
      $customer = Customers::with('brands')->where('id', $customerId)->first();
      $id = $customer->id;
      $brandId = $brandId;
      $brand = Labels::where('id', $brandId)->first();
      $brands = array();
      $keys1 = array('prod_id', 'prod_name', 'labels_qty', 'label_cost', 'status', 'forecast_labels');
      $keys2 = array('brand_name', 'brand_id', 'products');
      $products = array();
      $customerProducts = CustomerHasProduct::where('customer_id', $id)->where('brand_id', $brandId)->get();
      foreach ($customerProducts as $cProduct) {
        $product = Products::where('id', $cProduct->product_id)->first();
        $threshold = Setting::where('id', 1)->first();
        $available = CustomerHasProduct::where('customer_id', $customerId)
          ->where('brand_id', $brandId)
          ->where('product_id', $cProduct->product_id)
          ->sum('label_qty');
        $lastTenDaysLabelDeduction = ProductLabelOrder::where('customer_id', $id)
          ->where('brand_id', $brandId)
          ->where('product_id', $cProduct->product_id)
          ->where('created_at', '>', Carbon::now()->subDays(10))
          ->sum('label_deduction');
        $perDayDeduction = $lastTenDaysLabelDeduction / 10;
        if ($perDayDeduction == 0) {
          $perDayDeduction = 1;
        }
        $forecastVals = $available / $perDayDeduction;
        $forecastVals = round($forecastVals);
        $startDate = new DateTime(date("Y/m/d"));
        $endDate = new DateTime(date("Y/m/d", strtotime("+$forecastVals days")));
        $dd = date_diff($startDate, $endDate);
        $html = '';
        if (isset($threshold)) {
          $threshold = $threshold->threshold_val;
        } else {
          $threshold = 0;
        }
        if ($threshold == 0) {
          $html .= '<center><span class="badge rounded-pill badge-light-danger me-1">Set Threshold</span></center>';
        } else {
          if ($forecastVals > $threshold) {
            if ($dd->y < 1) {
              $y = '';
            } else {
              $y = $dd->y . 'y ';
            }
            if ($dd->m < 1) {
              $m = '';
            } else {
              $m = $dd->m . 'm ';
            }
            $html .= '<center><span class="badge rounded-pill badge-light-success me-1" style="background-color: lightgreen; color: darkgreen">' . $y . $m . $dd->d . 'd' . '</span></center>';
          } else {
            if ($dd->y < 1) {
              $y = '';
            } else {
              $y = $dd->y . 'y ';
            }
            if ($dd->m < 1) {
              $m = '';
            } else {
              $m = $dd->m . 'm ';
            }
            $html .= '<center><span class="badge rounded-pill badge-light-danger me-1" style="background-color: pink; color: red">' . $y . $m . $dd->d . 'd' . '</span></center>';
          }
        }
        if (isset($product)) {
          $customerProduct = CustomerHasProduct::where('customer_id', $customer->id)
            ->where('brand_id', $brandId)
            ->where('product_id', $product->id)
            ->first();
          $labelcost = number_format($cProduct->label_cost, 2);
          $labelStatus = $cProduct->is_active;
          if ($labelStatus == 0) {
            $stat = '<center><span class="badge rounded-pill badge-light-success me-1" style="background-color: lightgreen; color: darkgreen">On</span></center>';
          } else {
            $stat = '<center><span class="badge rounded-pill badge-light-danger me-1" style="background-color: pink; color: red">Off</span></center>';
          }
          $labelQty = $cProduct->label_qty;
          if (MergedBrandProduct::where('customer_id', $cProduct->customer_id)->where('merged_brand', $cProduct->brand_id)->where('product_id', $cProduct->product_id)->exists()) {
            $mergedProduct = MergedBrandProduct::where('customer_id', $cProduct->customer_id)->where('merged_brand', $cProduct->brand_id)->where('product_id', $cProduct->product_id)->first();
            if (isset($mergedProduct)) {
              $labelQty = MergedBrandProduct::where('customer_id', $cProduct->customer_id)->where('merged_brand', $cProduct->brand_id)->where('product_id', $cProduct->product_id)->sum('merged_qty');
            }
          } else if (MergedBrandProduct::where('customer_id', $cProduct->customer_id)->where('selected_brand', $cProduct->brand_id)->where('product_id', $cProduct->product_id)->exists()) {
            $mergedProduct = MergedBrandProduct::where('customer_id', $cProduct->customer_id)->where('selected_brand', $cProduct->brand_id)->where('product_id', $cProduct->product_id)->first();
            if (isset($mergedProduct)) {
              $labelQty = MergedBrandProduct::where('customer_id', $cProduct->customer_id)->where('selected_brand', $cProduct->brand_id)->where('product_id', $cProduct->product_id)->sum('merged_qty');
            }
          }
          // if ($cProduct->merged_qty != null) {
          //   $labelQty = $cProduct->merged_qty;
          // }
          array_push($products, array_combine($keys1, [$product->id, $product->name, number_format($labelQty), '$ ' . $labelcost, $stat, $html]));
        }
      }
      array_push($brands, array_combine($keys2, [$brand->brand, $brand->id, $products]));
      return Datatables::of($brands[0]['products'])
        ->addIndexColumn()
        ->addColumn('product_name', function ($row) {
          return ucwords($row['prod_name']);
        })
        ->addColumn('label_cost', function ($row) {
          return ucwords($row['label_cost']);
        })
        ->addColumn('labels_qty', function ($row) {
          return ucwords($row['labels_qty']);
        })
        ->addColumn('forecast_labels', function ($row) {
          return ucwords($row['forecast_labels']);
        })
        ->addColumn('action', function ($row) use ($brands, $customerId) {
          $btn = '<input type="hidden" class="productId" value="' . $row['prod_id'] . '" name="prodId[]">
                    <input type="hidden" class="brandId" value="' . $brands[0]['brand_id'] . '">
                    <div class="dropdown">
                      <button type="button" class="btn btn-sm dropdown-toggle hide-arrow more_btn" data-customer_id="' . $customerId . '" data-product-id="" data-bs-toggle="dropdown">
                        . . .
                      </button>
                      <div class="dropdown-menu">';
          $btn .= '<a class="dropdown-item add-labels" href="#labelsModal" data-brand-id="' . $brands[0]['brand_id'] . '" data-bs-target="#labelsModal" data-customer_id="" data-product-id="" data-bs-toggle="modal">
                      <span>Add Labels</span>
                      </a>
                      <a class="dropdown-item reduce-labels" href="#reducelabelsModal" data-brand-id="' . $brands[0]['brand_id'] . '" data-customer_id="" data-bs-target="#reducelabelsModal" data-product-id="" data-bs-toggle="modal">
                          <span>Reduce Labels</span>
                      </a>
                      <a class="dropdown-item add-label-cost" href="#labelCostModal" data-brand-id="' . $brands[0]['brand_id'] . '" data-bs-target="#labelCostModal" data-customer_id="' . $customerId . '" data-product-id="" data-bs-toggle="modal">
                          <span>Add Label Charges</span>
                      </a>';
          $btn .= '</div></div>';
          return $btn;
        })
        ->rawColumns(['action', 'forecast_labels'])
        ->make(true);
      $getAllProducts = CustomerProduct::where('customer_id', $customerId)->get();
      $products = array();
      $keys = array('prod_id', 'prod_name', 'purchasing_cost', 'weight', 'selling_cost');
      foreach ($getAllProducts as $getproduct) {
        $product = Products::where('id', $getproduct->product_id)->first();
        if (isset($product)) {
          if (!CustomerHasProduct::where('customer_id', $customerId)->where('brand_id', $brandId)->where('product_id', $getproduct->product_id)->exists()) {
            array_push($products, array_combine($keys, [$product->id, $product->name, $product->price, $product->weight, $product->price]));
          }
        }
      }
      return response()->json($products);
    }
  }
  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
    return view('admin.customers.add_customer');
  }
  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $validatedData = $request->validate([
      'customer_name' => 'required',
      'phone' => 'required',
      'email' => 'required|email|unique:customers',
      'password' => 'required'
    ]);
    DB::beginTransaction();
    try {
      $isActive = $request->input('is_active');
      if ($isActive == 1) {
        $isActive = 1;
      } else {
        $isActive = 0;
      }
      if (Customers::withTrashed()->where('email', $request->input('email'))->exists()) {
        return redirect()->back()->withInput()->withError('Email Already Exists');
      }
      if (User::withTrashed()->where('email', $request->input('email'))->exists()) {
        return redirect()->back()->withInput()->withError('Email Already Exists');
      } else {
        $customer = Customers::create([
          'customer_name' => $request->input('customer_name'),
          'is_active' => $isActive,
          'phone' => $request->input('phone'),
          'email' => $request->input('email'),
          'address' => $request->input('address'),
          'po_box_number' => $request->input('po_box_number'),
          'password' => Hash::make($request->password),
        ]);
        $customerId = $customer->id;
        // $custUser = CustomerUser::where('customer_id', $customerId)->first();
        $charges = ServiceCharges::create([
          'customer_id' => $customer->id,
          'labels' => 0.00,
          'pick' => 0.00,
          'pack' => 0.00,
          'mailer' => 0.00,
          'postage_cost' => 0.00,
          'postage_cost_lt5' => 0.00,
          'postage_cost_lt9' => 0.00,
          'postage_cost_lt13' => 0.00,
          'postage_cost_gte13' => 0.00,
        ]);
        $user = User::create([
          'name' => $request->customer_name,
          'email' => $request->email,
          'password' => Hash::make($request->password),
          'customer_status' => 1,
        ]);
        if (!CustomerUser::where('customer_id', $customer->id)->where('user_id', $user->id)->exists()) {
          $customerUser = CustomerUser::create([
            'customer_id' => $customer->id,
            'user_id' => $user->id
          ]);
        }
        $user->assignRole('customer');
        DB::commit();
        return redirect('/customer/' . $customerId . '/show_all')->withSuccess('Customer Added Successfully');
      }
    } catch (\Exception $e) {
      DB::rollback();
      return redirect()->back()->withError('Something went wrong');
    }
  }
  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    //
  }
  public function editCustomerDetails($id)
  {
    $data['dataSet'] = Customers::with('service_charges', 'brands')->find($id);
    $custServiceCharges = ServiceCharges::where('customer_id', $id)->latest('updated_at')->first();
    $skus = array();
    $keys = array('sku_id', 'sku_name', 'selling_cost', 'skuProducts');
    $customerSkus = CustomerHasSku::where('customer_id', $id)->get();
    foreach ($customerSkus as $c_sku) {
      $sku = Sku::with('sku_product.product', 'brand')->where('id', $c_sku->sku_id)->first();
      if (isset($sku)) {
        $skuProducts = array();
        if ($sku->sku_product != NULL || $sku->sku_product != '') {
          $skuProducts = $sku->sku_product;
        }
        array_push($skus, array_combine($keys, [$sku->id, $sku->name, $c_sku->selling_price, $skuProducts]));
      }
    }
    $brands = array();
    $keys1 = array('prod_id', 'prod_name', 'labels_qty', 'label_cost', 'status', 'forecast_labels');
    $keys2 = array('brand_name', 'brand_id', 'products');
    $customerBrands = $data['dataSet']->brands;
    foreach ($customerBrands as $brand) {
      $products = array();
      $customerProducts = CustomerHasProduct::where('customer_id', $id)->where('brand_id', $brand->id)->get();
      foreach ($customerProducts as $cProduct) {
        $product = Products::where('id', $cProduct->product_id)->first();
        $threshold = Setting::where('id', 1)->first();
        $available = CustomerHasProduct::where('customer_id', $id)
          ->where('brand_id', $brand->id)
          ->where('product_id', $cProduct->product_id)
          ->sum('label_qty');
        $lastTenDaysLabelDeduction = ProductLabelOrder::where('customer_id', $id)
          ->where('brand_id', $brand->id)
          ->where('product_id', $cProduct->product_id)
          ->where('created_at', '>', Carbon::now()->subDays(10))->sum('label_deduction');
        $perDayDeduction = $lastTenDaysLabelDeduction / 10;
        if ($perDayDeduction == 0) {
          $perDayDeduction = 1;
        }
        $forecastVals = $available / $perDayDeduction;
        $forecastVals = round($forecastVals);
        $startDate = new DateTime(date("Y/m/d"));
        $endDate = new DateTime(date("Y/m/d", strtotime("+$forecastVals days")));
        $dd = date_diff($startDate, $endDate);
        $html = '';
        if (isset($threshold)) {
          $threshold = $threshold->threshold_val;
        } else {
          $threshold = 0;
        }
        if ($threshold == 0) {
          $html .= '<span class="badge rounded-pill badge-light-danger me-1">Set Threshold</span>';
        } else {
          if ($forecastVals > $threshold) {
            if ($dd->y < 1) {
              $y = '';
            } else {
              $y = $dd->y . 'y ';
            }
            if ($dd->m < 1) {
              $m = '';
            } else {
              $m = $dd->m . 'm ';
            }
            $html .= '<center><span class="badge rounded-pill badge-light-success me-1" style="background-color: lightgreen; color: darkgreen">' . $y . $m . $dd->d . 'd' . '</span></center>';
          } else {
            if ($dd->y < 1) {
              $y = '';
            } else {
              $y = $dd->y . 'y ';
            }
            if ($dd->m < 1) {
              $m = '';
            } else {
              $m = $dd->m . 'm ';
            }
            $html .= '<center><span class="badge rounded-pill badge-light-danger me-1" style="background-color: pink; color: red">' . $y . $m . $dd->d . 'd' . '</span></center>';
          }
        }
        if (isset($product)) {
          $labelQty = $cProduct->label_qty;
          if (MergedBrandProduct::where('customer_id', $cProduct->customer_id)->where('merged_brand', $cProduct->brand_id)->where('product_id', $cProduct->product_id)->exists()) {
            $mergedProduct = MergedBrandProduct::where('customer_id', $cProduct->customer_id)->where('merged_brand', $cProduct->brand_id)->where('product_id', $cProduct->product_id)->first();
            if (isset($mergedProduct)) {
              $labelQty = MergedBrandProduct::where('customer_id', $cProduct->customer_id)->where('merged_brand', $cProduct->brand_id)->where('product_id', $cProduct->product_id)->sum('merged_qty');
            }
          } else if (MergedBrandProduct::where('customer_id', $cProduct->customer_id)->where('selected_brand', $cProduct->brand_id)->where('product_id', $cProduct->product_id)->exists()) {
            $mergedProduct = MergedBrandProduct::where('customer_id', $cProduct->customer_id)->where('selected_brand', $cProduct->brand_id)->where('product_id', $cProduct->product_id)->first();
            if (isset($mergedProduct)) {
              $labelQty = MergedBrandProduct::where('customer_id', $cProduct->customer_id)->where('selected_brand', $cProduct->brand_id)->where('product_id', $cProduct->product_id)->sum('merged_qty');
            }
          }
          // if ($cProduct->merged_qty != null) {
          //   $labelQty = $cProduct->merged_qty;
          // }
          array_push($products, array_combine($keys1, [$product->id, $product->name, $labelQty, $cProduct->label_cost, $cProduct->is_active, $html]));
        }
      }
      array_push($brands, array_combine($keys2, [$brand->brand, $brand->id, $products]));
    }
    $getAllProducts = Products::get();
    $products = array();
    $keys = array('prod_id', 'prod_name', 'purchasing_cost', 'weight', 'selling_cost');
    foreach ($getAllProducts as $product) {
      if (CustomerProduct::where('customer_id', $id)->where('product_id', $product->id)->exists()) {
      } else {
        array_push($products, array_combine($keys, [$product->id, $product->name, $product->price, $product->weight, $product->price]));
      }
    }
    $customerProducts = CustomerProduct::where('customer_id', $id)->get();
    $settingValues = Setting::where('id', 1)->first();
    return view('admin.customers.edit_customer', compact('brands', 'skus', 'custServiceCharges', 'id', 'products', 'customerProducts', 'settingValues'))->with($data);
  }
  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $customerUser = CustomerUser::where('customer_id', $id)->first();
    if (Auth::user()->can('customer_view')) {
      return $this->editCustomerDetails($id);
    } else {
      if (Auth::user()->id == $customerUser->user_id) {
        return $this->editCustomerDetails($id);
      } else {
        return back();
      }
    }
  }
  public function saveCustomerProduct(Request $request, $id)
  {
    $customerId = $id;
    $prodIds = $request->prod_ids;
    DB::beginTransaction();
    try {
      $custProd = array();
      for ($i = 0; $i < count($prodIds); $i++) {
        if (CustomerProduct::where('customer_id', $customerId)->where('product_id', $prodIds[$i])->exists()) {
        } else {
          $data = CustomerProduct::create([
            'customer_id' => $id,
            'product_id' => $request->prod_ids[$i],
            'is_active' => $request->labelStatus[$i],

            // 'seller_cost_status' => 1,
            'seller_cost_status' => $request->selling_cost ? 1 : 0,
            'selling_price' => $request->selling_cost[$i]
          ]);
          $product = Products::where('id', $data->product_id)->first();
          $productName = $product->name;
          $purchasingCost = number_format($product->price, 2);
          $weight = number_format($product->weight, 2);
          $custProd['product_name'] = $productName;
          $custProd['purchasing_cost'] = number_format($purchasingCost, 2);
          $custProd['weight'] = number_format($weight, 2);
          $custProd['selling_price'] = number_format($data->selling_price, 2);
          $custProd['seller_cost_status'] = $data->seller_cost_status;
          $custProd['is_active'] = $data->is_active;
        }
      }
      DB::commit();
      return response()->json(['success' => true, 'message' => 'Product Added Successfully', 'data' => $custProd]);
    } catch (\Exception $e) {
      DB::rollback();
      return response()->json(['error' => true, 'message' => 'Something went wrong']);
    }
  }
  public function saveCustomerBrandProduct(Request $request)
  {
    DB::beginTransaction();
    try {
      $customerId = $request->customer_id;
      $brandId = $request->brand_id;
      $prodIds = $request->prod_ids;
      $custProd = array();
      for ($i = 0; $i < count($prodIds); $i++) {
        if ($prodIds[$i] != null || $prodIds[$i] != '') {
          if (CustomerProduct::where('customer_id', $customerId)->where('product_id', $prodIds[$i])->exists()) {
          } else {
            CustomerProduct::create([
              'customer_id' => $customerId,
              'product_id' => $request->prod_ids[$i],
              'is_active' => '1',
              'selling_price' => '0.00'
            ]);
          }
          if (CustomerHasProduct::where('customer_id', $customerId)->where('brand_id', $brandId)->where('product_id', $prodIds[$i])->exists()) {
          } else {
            $customerProduct = CustomerProduct::where('customer_id', $customerId)->where('product_id', $prodIds[$i])->first();
            $sellingPriceOfProduct = 0;
            $status = 1;
            if (isset($customerProduct)) {
              $sellingPriceOfProduct = $customerProduct->selling_price;
              $status = $customerProduct->is_active;
            }
            $data = CustomerHasProduct::create([
              'customer_id' => $customerId,
              'brand_id' => $brandId,
              'product_id' => $request->prod_ids[$i],
              'is_active' => $status,
              'selling_price' => $sellingPriceOfProduct
            ]);
            $product = Products::where('id', $data->product_id)->first();
            $productName = $product->name;
            $purchasingCost = $product->price;
            $weight = $product->weight;
            $custProd['product_id'] = $product->id;
            $custProd['product_name'] = $productName;
            $custProd['purchasing_cost'] = $purchasingCost;
            $custProd['weight'] = $weight;
            // return response()->json(['data' => $custProd]);
          }
        }
      }
      DB::commit();
      return response()->json(['success' => true, 'message' => 'Product Added Successfully', 'data' => $custProd]);
    }
    catch (\Exception $e) {
      DB::rollback();
      return response()->json(['error' => true, 'message' => 'Something went wrong']);
    }
  }
  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    // dd($request->all());
    $validatedData = $request->validate([
      'customer_name' => 'required',
      'phone' => 'required',
      'email' => ['required', Rule::unique('customers')->ignore($id)]
    ]);
    DB::beginTransaction();
    try {
      if (Customers::withTrashed()->where('email', $request->input('email'))->where('id', '!=', $id)->exists()) {
        return redirect()->back()->withInput()->withError('Email Already Exists');
      }
      $custUser = CustomerUser::where('customer_id', $id)->first();
      if (User::withTrashed()->where('email', $request->input('email'))->where('id', '!=', $custUser->user_id)->exists()) {
        return redirect()->back()->withInput()->withError('Email Already Exists');
      }
      if ($request->input('pick_cost') <= 0.009) {
        return redirect()->back()->withError('Cant Add Zero Pick Cost');
      }
      $pickPackFlatStatus = 0;
      // $pickPackFlatRate = 0.00;
      if ($request->pick_pack_flat_status == 1) {
        $pickPackFlatStatus = 1;
      }
      $defaultPickPackStatus = 0;
      if ($request->default_pick_pack_status == 1) {
        $defaultPickPackStatus = 1;
      }
      $pickPackFlatRate = $request->pick_pack_flat;
      // if ($request->input('pack_cost') <= 0.009) {
      //   return redirect()->back()->withError('Cant Add Zero Pack Cost');
      // }
      $isActive = $request->input('is_active');
      if ($isActive == 1) {
        $isActive = 1;
      } else {
        $isActive = 0;
      }
      $password = $request->password;
      if ($password == '') {
        $customer = Customers::with('service_charges')->find($id);
        // On left field name in DB and on right field name in Form/view
        $customer->customer_name = $request->input('customer_name');
        $customer->is_active = $isActive;
        $customer->phone = $request->input('phone');
        $customer->email = $request->input('email');
        $customer->address = $request->input('address');
        $customer->po_box_number = $request->input('po_box_number');
        $customer->save();
        if (isset($custUser)) {
          if (User::withTrashed()->where('id', $custUser->user_id)->exists()) {
            $user = User::where('id', $custUser->user_id)->first();
            if (isset($user)) {
              $user->name = $request->customer_name;
              $user->email = $request->email;
              $user->save();
            }
          } else {
            $user = User::create([
              'name' => $request->customer_name,
              'email' => $request->email,
              'password' => Hash::make($request->password),
              'customer_status' => 1,
            ]);
            if (!CustomerUser::where('customer_id', $custUser->customer_id)->where('user_id', $user->id)->exists()) {
              CustomerUser::create([
                'customer_id' => $custUser->customer_id,
                'user_id' => $user->id
              ]);
            }
          }
        }
        if ($customer->service_charges) {
          $customer->service_charges->labels = $request->input('labels_cost');
          $customer->service_charges->pick = $request->input('pick_cost');
          $customer->service_charges->pack = $request->input('pack_cost');
          $customer->service_charges->mailer = $request->input('mailer_cost');
          $customer->service_charges->postage_cost = $request->input('pc_custom');
          $customer->service_charges->postage_cost_lt5 = $request->input('pc_1_4oz');
          $customer->service_charges->postage_cost_lt9 = $request->input('pc_5_8oz');
          $customer->service_charges->postage_cost_lt13 = $request->input('pc_9_12oz');
          $customer->service_charges->postage_cost_gte13 = $request->input('pc_13_15oz');
          $customer->service_charges->default_service_charges = $request->input('default_service_charges');
          $customer->service_charges->default_postage_charges = $request->input('default_postage_charges');
          $customer->service_charges->pick_pack_flat = $pickPackFlatRate;
          $customer->service_charges->pick_pack_flat_status = $pickPackFlatStatus;
          $customer->service_charges->default_pick_pack_flat_status = $defaultPickPackStatus;
          $customer->service_charges->return_service_charges = $request->return_service_charges;
          $customer->service_charges->save();
        } else {
          ServiceCharges::create([
            'customer_id' => $id,
            'labels' => $request->input('labels_cost'),
            'pick' => $request->input('pick_cost'),
            'pack' => $request->input('pack_cost'),
            'mailer' => $request->input('mailer_cost'),
            'postage_cost' => $request->input('pc_custom'),
            'postage_cost_lt5' => $request->input('pc_1_4oz'),
            'postage_cost_lt9' => $request->input('pc_5_8oz'),
            'postage_cost_lt13' => $request->input('pc_9_12oz'),
            'postage_cost_gte13' => $request->input('pc_13_15oz'),
            'default_service_charges' => $request->input('default_service_charges'),
            'default_postage_charges' => $request->input('default_postage_charges'),
            'return_service_charges' => $request->input('return_service_charges'),
          ]);
        }
        $customer = Customers::with('brands')->where('id', $id)->first();
        $custBrands = $customer->brands;
        foreach ($custBrands as $custBrand) {
          if ($custBrand->sku) {
            foreach ($custBrand->sku as $sku) {
              foreach ($sku->sku_product as $skuProduct) {
                $pickCost = $skuProduct->pick;
                $packCost = $skuProduct->pack;
                $pickPackFlatStatus = $skuProduct->pick_pack_flat_status;
                if ($pickCost == 0 || $pickCost == NULL) {
                  $pickCost = NULL;
                } else {
                  if ($pickPackFlatStatus == 1) {
                    $pickCost = $pickPackFlatRate;
                  } else {
                    $pickCost = $request->input('pick_cost');
                  }
                }
                if ($packCost == 0 || $packCost == NULL) {
                  $packCost = NULL;
                } else {
                  if ($pickPackFlatStatus == 1) {
                    $packCost = $pickPackFlatRate;
                  } else {
                    $packCost = $request->input('pack_cost');
                  }
                }
                $skuProduct->pick = $pickCost;
                $skuProduct->pack = $packCost;
                $skuProduct->save();
              }
            }
          }
        }
      } else {
        $customer = Customers::with('service_charges')->find($id);
        // On left field name in DB and on right field name in Form/view
        $customer->customer_name = $request->input('customer_name');
        $customer->is_active = $isActive;
        $customer->phone = $request->input('phone');
        $customer->email = $request->input('email');
        $customer->address = $request->input('address');
        $customer->password = Hash::make($password);
        $customer->save();
        $custUser = CustomerUser::where('customer_id', $id)->first();
        if (isset($custUser)) {
          if (User::withTrashed()->where('id', $custUser->user_id)->exists()) {
            $user = User::where('id', $custUser->user_id)->first();
            if (isset($user)) {
              $user->name = $request->customer_name;
              $user->email = $request->email;
              $user->save();
            }
          } else {
            $user = User::create([
              'name' => $request->customer_name,
              'email' => $request->email,
              'password' => Hash::make($request->password),
              'customer_status' => 1,
            ]);
            if (!CustomerUser::where('customer_id', $custUser->customer_id)->where('user_id', $user->id)->exists()) {
              CustomerUser::create([
                'customer_id' => $custUser->customer_id,
                'user_id' => $user->id
              ]);
            }
          }
        }
        if ($customer->service_charges) {
          $customer->service_charges->labels = $request->input('labels_cost');
          $customer->service_charges->pick = $request->input('pick_cost');
          $customer->service_charges->pack = $request->input('pack_cost');
          $customer->service_charges->mailer = $request->input('mailer_cost');
          $customer->service_charges->postage_cost = $request->input('pc_custom');
          $customer->service_charges->postage_cost_lt5 = $request->input('pc_1_4oz');
          $customer->service_charges->postage_cost_lt9 = $request->input('pc_5_8oz');
          $customer->service_charges->postage_cost_lt13 = $request->input('pc_9_12oz');
          $customer->service_charges->postage_cost_gte13 = $request->input('pc_13_15oz');
          $customer->service_charges->default_service_charges = $request->input('default_service_charges');
          $customer->service_charges->default_postage_charges = $request->input('default_postage_charges');
          $customer->service_charges->save();
        } else {
          ServiceCharges::create([
            'customer_id' => $id,
            'labels' => $request->input('labels_cost'),
            'pick' => $request->input('pick_cost'),
            'pack' => $request->input('pack_cost'),
            'mailer' => $request->input('mailer_cost'),
            'postage_cost' => $request->input('pc_custom'),
            'postage_cost_lt5' => $request->input('pc_1_4oz'),
            'postage_cost_lt9' => $request->input('pc_5_8oz'),
            'postage_cost_lt13' => $request->input('pc_9_12oz'),
            'postage_cost_gte13' => $request->input('pc_13_15oz'),
            'default_service_charges' => $request->input('default_service_charges'),
            'default_postage_charges' => $request->input('default_postage_charges')
          ]);
        }
        $customer = Customers::with('brands')->where('id', $id)->first();
        $custBrands = $customer->brands;
        foreach ($custBrands as $custBrand) {
          if ($custBrand->sku) {
            foreach ($custBrand->sku as $sku) {
              foreach ($sku->sku_product as $skuProduct) {
                $pickCost = $skuProduct->pick;
                $packCost = $skuProduct->pack;
                $pickPackFlatStatus = $skuProduct->pick_pack_flat_status;
                if ($pickCost == 0 || $pickCost == NULL) {
                  $pickCost = NULL;
                } else {
                  if ($pickPackFlatStatus == 1) {
                    $pickCost = $pickPackFlatRate;
                  } else {
                    $pickCost = $request->input('pick_cost');
                  }
                }
                if ($packCost == 0 || $packCost == NULL) {
                  $packCost = NULL;
                } else {
                  if ($pickPackFlatStatus == 1) {
                    $packCost = $pickPackFlatRate;
                  } else {
                    $packCost = $request->input('pack_cost');
                  }
                }
                $skuProduct->pick = $pickCost;
                $skuProduct->pack = $packCost;
                $skuProduct->save();
              }
            }
          }
        }
      }
      DB::commit();
      return redirect()->back()->withSuccess('Customer Updated Successfully');
    }
    catch (\Exception $e) {
      DB::rollback();
      return redirect()->back()->withError('Something went wrong');
    }
  }
  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $data = Customers::with('brands')->find($id);
    $data->brands->load('sku');
    foreach ($data->brands as $key => $brand) {
      if ($brand->sku->count() > 0) {
        $brand->sku->load('sku_product');
        foreach ($brand->sku as $k => $sku) {
          $sku->load('sku_product');
          if ($sku->sku_product->count() > 0) {
            foreach ($sku->sku_product as $index => $sku_product) {
              // $sku_product->delete();
            }
          }
          // $sku->delete();
        }
        // $brand->delete();
      }
    }
    $data->delete();
    $custUser = CustomerUser::where('customer_id', $id)->first();
    if (isset($custUser)) {
      User::find($custUser->user_id)->delete();
    }
    return redirect('/customers')->withSuccess('Customer Deleted Successfully');
  }
  /**
   * Show brands of customer.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function customerBrands(Request $request, $id)
  {
    if ($request->ajax()) {
      $columns = $request->get('columns');
      $orders = $request->get('order');
      $orderbyColAndDirection = [];
      foreach ($orders as $key => $value) {
        array_push($orderbyColAndDirection, $columns[$value['column']]['data'] . ' ' . $value['dir']);
      }
      $orderbyColAndDirection = implode(", ", $orderbyColAndDirection);
      $data  = Labels::with('customer')->where('customer_id', $id)->get();
      return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('customer_name', function ($row) {
          return ucwords($row->customer->customer_name);
        })
        ->addColumn('brand', function ($row) {
          return ucwords($row->brand);
        })
        ->addColumn('mailer_cost', function ($row) {
          if ($row->mailer_cost != NULL || $row->mailer_cost != 0) {
            return '$' . number_format($row->mailer_cost, 2);
          } else {
            return '$0.00';
          }
        })
        ->addColumn('qty', function ($row) {
          return ucwords($row->qty);
        })
        ->addColumn('date', function ($row) {
          return ucwords(date("M d, Y", strtotime($row->date)));
        })
        ->addColumn('action', function ($row) {
          $btn = '<div class="dropdown">
                        <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i data-feather="more-vertical"></i>
                        </button>
                        <div class="dropdown-menu">';
          if (Auth::user()->can('labels_view')) {
            $btn .= '<a class="dropdown-item" href="/brand/' . $row->id . '/sku">
                            <i data-feather="eye"></i>
                            <span>View SKU</span>
                        </a>';
          }
          if (Auth::user()->can('labels_update')) {
            $btn .= '<a class="dropdown-item" href="/customer/' . $row->customer_id . '/brand/' . $row->id . '/edit">
                            <i data-feather="edit-2"></i>
                            <span>Edit Brand</span>
                        </a>';
          }
          if (Auth::user()->can('labels_delete')) {
            $btn .= '<a class="dropdown-item" href="/delete/brand/' . $row->id . '" onclick="confirmDelete(event)">
                            <i data-feather="trash"></i>
                            <span>Delete Brand</span>
                        </a>';
          }
          $btn .= '</div></div>';
          return $btn;
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    return view('admin.customers.customer_labels', compact('id'));
  }
  public function getCustomerBrand($id)
  {
    $data  = Labels::where('customer_id', $id)->get();
    if (sizeof($data) > 0) {
      $charges = ServiceCharges::where('customer_id', $id)->latest('updated_at')->first();
      if ($charges) {
        $charges = $charges;
      } else {
        $newCharges = Setting::where('id', '1')->first();
        $charges = $newCharges;
      }
      return response()->json([
        'status' => 'success',
        'data' => $data,
        'charges' => $charges
      ], 200);
    } else {
      return response()->json([
        'status' => 'failed',
        'message' => "No Brands Found. Please first add brand."
      ], 200);
    }
  }
  public function getAllBrands()
  {
    $data = DB::table('labels')
      ->join('customers', 'labels.customer_id', '=', 'customers.id')
      ->where('customers.deleted_at', NULL)->get();
    if (sizeof($data) > 0) {
      return response()->json([
        'status' => 'success',
        'data' => $data
      ], 200);
    } else {
      return response()->json([
        'status' => 'failed',
        'message' => "No Brands Found. Please first add brand."
      ], 200);
    }
  }
  public function getServiceCharges($id)
  {
    $charges = ServiceCharges::where('customer_id', $id)->latest('updated_at')->first();
    if ($charges) {
      return response()->json([
        'status' => 'success',
        'data' => $charges
      ], 200);
    } else {
      $setting = Setting::where('id', '1')->first();
      if (isset($setting)) {
        return response()->json([
          'status' => 'success',
          'data' => $setting
        ], 200);
      } else {
        return response()->json([
          'status' => 'failed',
          'message' => 'No Service Charges found.'
        ], 200);
      }
    }
  }
  public function trash(Request $request)
  {
    if ($request->ajax()) {
      $customers = Customers::with('service_charges')->onlyTrashed()->get();
      return Datatables::of($customers)
        ->addIndexColumn()
        ->addColumn('customer_name', function ($row) {
          return ucwords($row->customer_name);
        })
        ->addColumn('phone', function ($row) {
          return ucwords($row->phone);
        })
        ->addColumn('email', function ($row) {
          return ucwords($row->email);
        })
        ->addColumn('address', function ($row) {
          return ucwords($row->address);
        })
        ->addColumn('is_active', function ($row) {
          if ($row->is_active == 0) return '<span class="badge rounded-pill badge-light-danger me-1">Not Active</span>';
          if ($row->is_active == 1) return '<span class="badge rounded-pill badge-light-success me-1">Active</span>';
        })
        ->addColumn('action', function ($row) {
          $btn = '<a href="/customers/' . $row->id . '/restore" class="btn btn-primary btn-sm">Restore</a>&nbsp;';
          return $btn;
        })
        ->rawColumns(['action', 'is_active', 'charges'])
        ->make(true);
    }
    return view('admin.customers.trash');
  }
  public function restore($id)
  {
    $data = Customers::with('brands')->withTrashed()->find($id);
    $data->brands->load('sku');
    foreach ($data->brands as $key => $brand) {
      if ($brand->sku->count() > 0) {
        $brand->sku->load('sku_product');
        foreach ($brand->sku as $k => $sku) {
          $sku->load('sku_product');
          if ($sku->sku_product->count() > 0) {
            foreach ($sku->sku_product as $index => $sku_product) {
              // $sku_product->restore();
            }
          }
          // $sku->restore();
        }
        // $brand->restore();
      }
    }
    $custUser = CustomerUser::where('customer_id', $id)->first();
    if (isset($custUser)) {
      $user = User::withTrashed()->where('id', $custUser->user_id)->first();
      if (isset($user)) {
        $user->restore();
      }
    }
    $data->restore();
    return redirect('/customers')->withSuccess('Customer Restored Successfully');
  }
  public function permanentlyDeleteCustomer($id)
  {
    $data = Customers::with('brands')->withTrashed()->find($id);
    $data->brands->load('sku');
    foreach ($data->brands as $key => $brand) {
      if ($brand->sku != null) {
        $brand->sku->load('sku_product');
        foreach ($brand->sku as $k => $sku) {
          $sku->load('sku_product');
          if ($sku->sku_product != null) {
            foreach ($sku->sku_product as $index => $sku_product) {
              $sku_product->forceDelete();
            }
          }
          $sku->forceDelete();
        }
        $brand->forceDelete();
      }
    }
    $data->forceDelete();
    return redirect()->back()->withSuccess('Customer Permanently Deleted');
  }
  public function createCustomerBrand($id)
  {
    $customer = Customers::find($id);
    if ($customer) {
      return view('admin.customers.create_brand', compact('customer'));
    } else {
      return redirect()->back()->with(['error' => 'Error while finding Customer']);
    }
  }
  public function storeCustomerBrand($id, Request $request)
  {
    $validatedData = $request->validate([
      'brand' => 'required',
    ]);
    DB::beginTransaction();
    try {
      if (Labels::where('brand', $request->brand)->where('customer_id', $id)->exists()) {
        return redirect()->back()->withError('Label Name Already Exists');
      }
      // create brand
      $brand = Labels::create([
        'customer_id' => $id,
        'brand' => $request->input('brand'),
        'mailer_cost' => $request->input('mailer_cost'),
        'qty' => 0,
        'date' => Carbon::now(),
        'deleted_at' => null,
      ]);
      // create label history
      $brandHistory = LabelsHistory::create([
        'customer_id' => $id,
        'brand_id' => $brand->id,
        'user_id' => Auth::user()->id,
        'qty' => 0,
        'date' => Carbon::now(),
        'deleted_at' => null,
        'status' => 1
      ]);
      DB::commit();
      return redirect()->back()->withSuccess('Brand Created');
      // return redirect('/customer/'.$id.'/brands')->withSuccess('Brand has been added');
    } catch (\Exception $e) {
      DB::rollback();
      return redirect()->back()->withError('Something went wrong');
    }
  }
  public function editCustomerSingleBrand(Request $request, $customerId, $brandId)
  {
    $brand = Labels::with('customer')->where('id', $brandId)->first();
    $customerBrandsProducts = CustomerHasProduct::where('customer_id', $customerId)->where('brand_id', $brandId)->get();
    $customer = Customers::with('brands')->where('id', $customerId)->first();
    $id = $customer->id;
    $brandId = $brandId;
    $brand = Labels::where('id', $brandId)->first();
    $brands = array();
    $keys1 = array('prod_id', 'prod_name', 'labels_qty', 'label_cost', 'status', 'forecast_labels');
    $keys2 = array('brand_name', 'brand_id', 'products');
    $products = array();
    $customerProducts = CustomerHasProduct::where('customer_id', $id)->where('brand_id', $brandId)->get();
    foreach ($customerProducts as $cProduct) {
      $product = Products::where('id', $cProduct->product_id)->first();
      $threshold = Setting::where('id', 1)->first();
      $available = CustomerHasProduct::where('customer_id', $customerId)
        ->where('brand_id', $brandId)
        ->where('product_id', $cProduct->product_id)
        ->sum('label_qty');
      $lastTenDaysLabelDeduction = ProductLabelOrder::where('customer_id', $id)
        ->where('brand_id', $brandId)
        ->where('product_id', $cProduct->product_id)
        ->where('created_at', '>', Carbon::now()->subDays(10))
        ->sum('label_deduction');
      $perDayDeduction = $lastTenDaysLabelDeduction / 10;
      if ($perDayDeduction == 0) {
        $perDayDeduction = 1;
      }
      $forecastVals = $available / $perDayDeduction;
      $forecastVals = round($forecastVals);
      $startDate = new DateTime(date("Y/m/d"));
      $endDate = new DateTime(date("Y/m/d", strtotime("+$forecastVals days")));
      $dd = date_diff($startDate, $endDate);
      $html = '';
      if (isset($threshold)) {
        $threshold = $threshold->threshold_val;
      } else {
        $threshold = 0;
      }
      if ($threshold == 0) {
        $html .= '<span class="badge rounded-pill badge-light-danger me-1">Set Threshold</span>';
      } else {
        if ($forecastVals > $threshold) {
          if ($dd->y < 1) {
            $y = '';
          } else {
            $y = $dd->y . 'y ';
          }
          if ($dd->m < 1) {
            $m = '';
          } else {
            $m = $dd->m . 'm ';
          }
          $html .= '<center><span class="badge rounded-pill badge-light-success me-1" style="background-color: lightgreen; color: darkgreen">' . $y . $m . $dd->d . 'd' . '</span></center>';
        } else {
          if ($dd->y < 1) {
            $y = '';
          } else {
            $y = $dd->y . 'y ';
          }
          if ($dd->m < 1) {
            $m = '';
          } else {
            $m = $dd->m . 'm ';
          }
          $html .= '<center><span class="badge rounded-pill badge-light-danger me-1" style="background-color: pink; color: red">' . $y . $m . $dd->d . 'd' . '</span></center>';
        }
      }
      if (isset($product)) {
        $customerProduct = CustomerHasProduct::where('customer_id', $customer->id)
          ->where('brand_id', $brandId)
          ->where('product_id', $product->id)
          ->first();
        $labelQty = $cProduct->label_qty;
        if (isset($product)) {
          $labelQty = $cProduct->label_qty;
          if (MergedBrandProduct::where('customer_id', $cProduct->customer_id)->where('merged_brand', $cProduct->brand_id)->where('product_id', $cProduct->product_id)->exists()) {
            $mergedProduct = MergedBrandProduct::where('customer_id', $cProduct->customer_id)->where('merged_brand', $cProduct->brand_id)->where('product_id', $cProduct->product_id)->first();
            if (isset($mergedProduct)) {
              $labelQty = MergedBrandProduct::where('customer_id', $cProduct->customer_id)->where('merged_brand', $cProduct->brand_id)->where('product_id', $cProduct->product_id)->sum('merged_qty');
            }
          } else if (MergedBrandProduct::where('customer_id', $cProduct->customer_id)->where('selected_brand', $cProduct->brand_id)->where('product_id', $cProduct->product_id)->exists()) {
            $mergedProduct = MergedBrandProduct::where('customer_id', $cProduct->customer_id)->where('selected_brand', $cProduct->brand_id)->where('product_id', $cProduct->product_id)->first();
            if (isset($mergedProduct)) {
              $labelQty = MergedBrandProduct::where('customer_id', $cProduct->customer_id)->where('selected_brand', $cProduct->brand_id)->where('product_id', $cProduct->product_id)->sum('merged_qty');
            }
          }
        }
        // if ($cProduct->merged_qty != null) {
        //   $labelQty = $cProduct->merged_qty;
        // }
        array_push($products, array_combine($keys1, [$product->id, $product->name, $labelQty, $cProduct->label_cost, $cProduct->is_active, $html]));
      }
    }
    array_push($brands, array_combine($keys2, [$brand->brand, $brand->id, $products]));
    $getAllProducts = CustomerProduct::where('customer_id', $customerId)->get();
    $products = array();
    $keys = array('prod_id', 'prod_name', 'purchasing_cost', 'weight', 'selling_cost');
    foreach ($getAllProducts as $getproduct) {
      $product = Products::where('id', $getproduct->product_id)->first();
      if (isset($product)) {
        if (!CustomerHasProduct::where('customer_id', $customerId)->where('brand_id', $brandId)->where('product_id', $getproduct->product_id)->exists()) {
          array_push($products, array_combine($keys, [$product->id, $product->name, number_format($product->price, 2), $product->weight, number_format($product->price, 2)]));
        }
      }
    }
    if ($request->ajax()) {
      return response()->json($products);
    }
    return view('admin.customers.edit_brand', compact('brand', 'products', 'customerBrandsProducts', 'brands'));
  }
  public function updateCustomerBrand($customerId, $brandId, Request $request)
  {
    DB::beginTransaction();
    try {
      $brand = Labels::with('customer')->where('id', $brandId)->first();
      $brand->brand = $request->brand;
      $brand->mailer_cost = $request->mailer_cost;
      $brand->save();
      DB::commit();
      return redirect()->back()->withSuccess('Brand Has been Updated');
    } catch (\Exception $e) {
      DB::rollback();
      return redirect()->back()->withError('Something went wrong');
    }
  }
  public function customerBrandLabelsHistory($customerId, $brandId, Request $request)
  {
    if ($request->ajax()) {
      $data = LabelsHistory::with('customer')->where('brand_id', $brandId)->orderBy('created_at', 'ASC')->get();
      return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('customer_name', function ($row) {
          return ucwords($row->customer->customer_name);
        })
        ->addColumn('brand', function ($row) {
          $brand = Labels::where('id', $row->brand_id)->withTrashed()->first();
          return ucwords($brand->brand);
        })
        ->addColumn('qty', function ($row) {
          return number_format($row->qty);
        })
        ->addColumn('date', function ($row) {
          return ucwords(date("M d, Y", strtotime($row->date)));
        })
        ->addColumn('status', function ($row) {
          return $btn = '<a href="javascript:void(0)" type="button" class="btn btn-sm btn-primary revert" data-history-id="' . $row->id . '">
                        Revert
                    </a>';
        })
        ->rawColumns(['status'])
        ->filter(function ($instance) use ($request) {
        })
        ->make(true);
    }
    return view('admin.customers.brand_labels_history');
  }
  public function CustomerBrandTrash($customerId, Request $request)
  {
    if ($request->ajax()) {
      $data = Labels::with('customer')->where('customer_id', $customerId)->onlyTrashed()->get();
      return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('customer_name', function ($row) {
          return ucwords($row->customer->customer_name);
        })
        ->addColumn('brand', function ($row) {
          return ucwords($row->brand);
        })
        ->addColumn('qty', function ($row) {
          return number_format($row->qty);
        })
        ->addColumn('date', function ($row) {
          return ucwords(date("Y-m-d", strtotime($row->date)));
        })
        ->addColumn('action', function ($row) {
          $btn = '';
          if (Auth::user()->can('customer_delete')) {
            $btn .= '<p><a class="btn btn-primary waves-effect waves-float waves-light" href="/brands/restoreTrash/' . $row->id . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Restore">
                            Restore
                          </a>';
            $btn .= '</p>';
          }
          return $btn;
        })
        ->rawColumns(['action'])
        // ->filter(function ($instance) use ($request) {
        //   if (!empty($request->get('search')['value'])) {
        //     $keyword = $request->get('search')['value'];
        //     $instance->whereRaw("labels.qty like '%$keyword%'");
        //   }
        // })
        ->make(true);
    }
    return view('admin.customers.brands_trash');
  }
  public function getCustomerCharges($id)
  {
    $data = ServiceCharges::where('customer_id', $id)->latest('updated_at')->first();
    if ($data) {
      return response()->json(['status' => 'success', 'data' => $data]);
    } else {
      $data = Setting::where('id', '1')->first();
      return response()->json(['status' => 'success', 'data' => $data]);
    }
  }
  public function setLabelStatus(Request $request)
  {
    DB::beginTransaction();
    try {
      $custBrands = Labels::where('customer_id', $request->customer_id)->get();
      foreach ($custBrands as $brand) {
        $custHasProd = CustomerHasProduct::where('product_id', $request->product_id)->where('brand_id', $brand->id)->where('customer_id', $request->customer_id)->first();
        if (isset($custHasProd)) {
          $status = $request->status;
          $custHasProd->is_active = $status;
          $custHasProd->save();
        }
      }
      $custProd = CustomerProduct::where('product_id', $request->product_id)->where('customer_id', $request->customer_id)->first();
      if (isset($custProd)) {
        $custProd->is_active = $request->status;
        $custProd->save();
      }
      DB::commit();
      return response()->json(['success' => true, 'message' => 'Updated Successfully']);
    } catch (\Exception $e) {
      DB::rollback();
      return response()->json(['error' => true, 'message' => 'Something went wrong']);
    }
  }
  public function setSellerCostStatus(Request $request)
  {
    DB::beginTransaction();
    try {
      $custBrands = Labels::where('customer_id', $request->customer_id)->get();
      foreach ($custBrands as $brand) {
        $custHasProd = CustomerHasProduct::where('product_id', $request->product_id)->where('brand_id', $brand->id)->where('customer_id', $request->customer_id)->first();
        if (isset($custHasProd)) {
          $custHasProd->seller_cost_status = $request->status;
          $custHasProd->save();
        }
      }
      $custProd = CustomerProduct::where('product_id', $request->product_id)->where('customer_id', $request->customer_id)->first();
      if (isset($custProd)) {
        $custProd->seller_cost_status = $request->status;
        $custProd->save();
      }
      DB::commit();
      return response()->json(['success' => true, 'message' => 'Seller Cost Status Updated']);
    } catch (\Exception $e) {
      DB::rollback();
      return response()->json(['error' => true, 'message' => 'Something went wrong']);
    }
  }
  public function updateCustomerProdSellingPrice(Request $request)
  {
    DB::beginTransaction();
    try {
      $update = CustomerProduct::where('customer_id', $request->customer_id)->where('product_id', $request->product_id)->first();
      $updateCproduct = CustomerHasProduct::where('customer_id', $request->customer_id)->where('product_id', $request->product_id)->get();
      if (isset($update)) {
        $update->selling_price = $request->selling_price;
        $update->save();
      }
      foreach ($updateCproduct as $cprod) {
        $cprod->selling_price = $request->selling_price;
        $cprod->save();
      }
      DB::commit();
      return response()->json(['success' => true, 'message' => 'Updated Successfully']);
    } catch (\Exception $e) {
      DB::rollback();
      return response()->json(['error' => true, 'message' => 'Something went wrong']);
    }
  }
  public function deleteCustomerProd($id, $prod_id)
  {
    $custProd = CustomerProduct::where('customer_id', $id)->where('product_id', $prod_id)->first();
    if (isset($custProd)) {
      $customerBrands = Labels::where('customer_id', $id)->get();
      foreach ($customerBrands as $customerBrandskey => $brand) {
        if (isset($brand)) {
          $skus = Sku::with('sku_product')->where('brand_id', $brand->id);
          if ($skus->exists()) {
            $getskus = $skus->get();
            foreach ($getskus as $skuskey => $sku) {
              if (isset($sku)) {
                $skuProducts = SkuProducts::where('sku_id', $sku->id)->where('product_id', $prod_id);
                if ($skuProducts->exists()) {
                  $orderDetails = OrderDetails::where('sku_id', $sku->id);
                  if ($orderDetails->exists()) {
                    return redirect()->back()->withError('Ordered Product. Cant Delete');
                  } else {
                    $skuProducts->delete();
                    CustomerHasProduct::where('customer_id', $id)->where('product_id', $prod_id)->delete();
                    CustomerProduct::where('customer_id', $id)->where('product_id', $prod_id)->delete();
                  }
                }
              }
            }
          }
        }
      }
      $skuData = Sku::with('sku_product')->get();
      foreach ($skuData as $skuDatakey => $dataSku) {
        if ($dataSku->sku_product->count() > 0) {
        } else {
          $dataSku->delete();
        }
      }
      CustomerHasProduct::where('customer_id', $id)->where('product_id', $prod_id)->delete();
      CustomerProduct::where('customer_id', $id)->where('product_id', $prod_id)->delete();
    }
    return redirect()->back()->withSuccess('Product Deleted Successfully');
  }
  public function deleteCustomerBrandProduct($c_id, $b_id, $p_id)
  {
    $skus = Sku::with('sku_product')->where('brand_id', $b_id);
    if ($skus->exists()) {
      $getskus = $skus->get();
      foreach ($getskus as $skuskey => $sku) {
        if (isset($sku)) {
          $skuProducts = SkuProducts::where('sku_id', $sku->id)->where('product_id', $p_id);
          if ($skuProducts->exists()) {
            $orderDetails = OrderDetails::where('sku_id', $sku->id);
            if ($orderDetails->exists()) {
              return redirect()->back()->withError('Ordered Product. Cant Delete');
            } else {
              $skuProducts->delete();
              CustomerHasProduct::where('customer_id', $c_id)->where('brand_id', $b_id)->where('product_id', $p_id)->delete();
            }
          }
        }
      }
    }
    CustomerHasProduct::where('customer_id', $c_id)->where('brand_id', $b_id)->where('product_id', $p_id)->delete();
    return redirect()->back()->withSuccess('Product Deleted Successfully');
  }
  public function customerTrashedProducts(Request $request, $id)
  {
    if ($request->ajax()) {
      $products = CustomerProduct::with('trashedproduct', 'customer')
        ->onlyTrashed()
        ->where('customer_id', $id)
        ->get();
      // $products->whereHas('product', function($q){
      //   return $q->withTrashed();
      // });
      return Datatables::of($products)
        ->addIndexColumn()
        ->addColumn('customer_name', function ($row) {
          return ucwords($row->customer->customer_name);
        })
        ->addColumn('product_name', function ($row) {
          $name = '';
          if ($row->trashedproduct) {
            $name = $row->trashedproduct->name;
          }
          return ucwords($name);
        })
        ->addColumn('purchasing_cost', function ($row) {
          $price = '';
          if ($row->trashedproduct) {
            $price = $row->trashedproduct->price;
          }
          return ucwords($price);
        })
        ->addColumn('weight', function ($row) {
          $weight = '';
          if ($row->trashedproduct) {
            $weight = $row->trashedproduct->weight;
          }
          return ucwords($weight);
        })
        ->addColumn('selling_cost', function ($row) {
          return ucwords($row->selling_price);
        })
        ->addColumn('action', function ($row) use ($id) {
          $btn = '';
          // if (Auth::user()->can('restore', Labels::class)) {
          $btn .= '<p><a class="btn btn-primary waves-effect waves-float waves-light" href="/restore_customer_trashed_product/' . $id . '/' . $row->product_id . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Restore">
                            Restore
                          </a>';
          $btn .= '</p>';
          // }
          return $btn;
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    return view('admin.customers.trashed_products', compact('id'));
  }
  public function restoreCustomerTrashedProduct(Request $request, $c_id, $b_id, $p_id)
  {
    $custProd = CustomerProduct::where('customer_id', $c_id)->where('product_id', $p_id)->onlyTrashed()->first();
    if (isset($custProd)) {
      $customerBrands = Labels::where('customer_id', $c_id)->get();
      foreach ($customerBrands as $customerBrandskey => $brand) {
        if (isset($brand)) {
          $skus = Sku::with('sku_product')->where('brand_id', $brand->id)->withTrashed();
          if ($skus->exists()) {
            $getskus = $skus->get();
            foreach ($getskus as $skuskey => $sku) {
              if (isset($sku)) {
                $skuProducts = SkuProducts::where('sku_id', $sku->id)->where('product_id', $p_id)->onlyTrashed()->restore();
              }
            }
          }
          $skuData = Sku::with('sku_product')->where('brand_id', $brand->id)->onlyTrashed()->restore();
        }
      }
      CustomerHasProduct::where('customer_id', $c_id)->where('product_id', $p_id)->restore();
      CustomerProduct::where('customer_id', $c_id)->where('product_id', $p_id)->restore();
      return redirect()->back()->withSuccess('Product Restored Successfully');
    }
  }
  public function add_labels(Request $request)
  {
    if (Auth::user()->can('customer_view')) {
      $customers = Customers::get();
      $labels = Labels::get();
      return view('admin.customers.add_labels', compact('labels', 'customers'));
    } else {
      $customerUser = CustomerUser::where('user_id', Auth::user()->id)->first();
      $customerId = Auth::user()->id;
      if (isset($customerUser)) {
        $customerId = $customerUser->customer_id;
      }
      $customers = Customers::where('id', $customerId)->get();
      $labels = Labels::where('customer_id', $customerId)->get();
      return view('admin.customers.add_labels', compact('labels', 'customers'));
    }
  }
  public function addCustomerLabel(Request $request, $id)
  {
    if ($request->ajax()) {
      $columns = $request->get('columns');
      $orders = $request->get('order');
      $orderbyColAndDirection = [];
      foreach ($orders as $key => $value) {
        array_push($orderbyColAndDirection, $columns[$value['column']]['data'] . ' ' . $value['dir']);
      }
      $orderbyColAndDirection = implode(", ", $orderbyColAndDirection);
      $query = Labels::where('customer_id', $id)
        ->with('customer', 'customer.brands', 'cust_has_prod', 'cust_has_prod.products', 'customer.product', 'sku.sku_product.product', 'sku.sku_product.product.customerHasProduct')
        ->where('deleted_at', NULL);
      $data = $query->get();
      return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('customer_name', function ($row) {
          return ucwords($row->customer->customer_name);
        })
        ->addColumn('brand', function ($row) {
          return ucwords($row->brand);
        })
        ->addColumn('qty', function ($row) {
          return number_format($row->qty);
        })
        ->addColumn('date', function ($row) {
          return ucwords(date("M d, Y", strtotime($row->date)));
        })
        ->addColumn('action', function ($row) {
          $btn = '<div class="dropdown">
                      <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                          <i data-feather="more-vertical"></i>
                      </button>
                    <div class="dropdown-menu">';
          if (Auth::user()->can('sku_view')) {
            $btn .= '<a class="dropdown-item" href="/brand/' . $row->id . '/sku">
                          <i data-feather="eye"></i>
                          <span>View SKU</span>
                      </a>';
          }
          if (Auth::user()->can('labels_update')) {
            $btn .= '<a class="dropdown-item" href="/brands/' . $row->id . '/edit">
                          <i data-feather="edit-2"></i>
                          <span>Edit Brand</span>
                      </a>';
          }
          if (Auth::user()->can('labels_delete')) {
            $btn .= '<a class="dropdown-item" href="/delete/brand/' . $row->id . '" onclick="confirmDelete(event)">
                          <i data-feather="trash"></i>
                          <span>Delete Brand</span>
                      </a>';
          }
          $btn .= '</div></div>';
          return $btn;
        })
        ->rawColumns(['action'])
        // ->filter(function ($instance) use ($request) {
        //   if (!empty($request->get('search')['value'])) {
        //     $keyword = $request->get('search')['value'];
        //     $instance->whereRaw("labels.brand like '%$keyword%' OR customers.customer_name like '%$keyword%'");
        //   }
        // })
        ->make(true);
    }
    $customers = Customers::get();
    $brands = Labels::where('customer_id', $id)->get();
    return view('admin.customers.add_customer_label', compact('customers', 'brands'));
  }
  public function addBrandProducts(Request $request, $id)
  {
    if ($request->ajax()) {
      $query = Labels::where('customer_id', $id)
        ->with('customer', 'customer.brands', 'cust_has_prod', 'cust_has_prod.products', 'customer.product', 'sku.sku_product.product', 'sku.sku_product.product.customerHasProduct')
        ->where('deleted_at', NULL);
      $data = $query->get();
      return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('customer_name', function ($row) {
          return ucwords($row->customer->customer_name);
        })
        ->addColumn('brand', function ($row) {
          return ucwords($row->brand);
        })
        ->addColumn('qty', function ($row) {
          return number_format($row->qty);
        })
        ->addColumn('date', function ($row) {
          return ucwords(date("M d, Y", strtotime($row->date)));
        })
        // ->filter(function ($instance) use ($request) {
        //   if (!empty($request->get('search')['value'])) {
        //     $keyword = $request->get('search')['value'];
        //     $instance->whereRaw("labels.brand like '%$keyword%' OR customers.customer_name like '%$keyword%'");
        //   }
        // })
        ->make(true);
    }
    $customers = Customers::get();
    $brands = Labels::where('customer_id', $id)->get();
    return view('admin.customers.add_brand_products', compact('customers', 'brands'));
  }
  public function getProductsCustomers(Request $request, $id)
  {
    $showSameBrandProducts = CustomerHasProduct::with('products', 'getcustomers', 'brands')->where('customer_id', $request->customer_id)->where('brand_id', '!=', $request->brand_id)->where('product_id', $id)->get();
    $selectedProduct = CustomerHasProduct::with('products', 'getcustomers', 'brands')->where('customer_id', $request->customer_id)->where('brand_id', $request->brand_id)->where('product_id', $id)->first();
    $labelQtty = 0;
    if (isset($selectedProduct)) {
      $labelQtty = $selectedProduct->label_qty;
      if (MergedBrandProduct::where('customer_id', $request->customer_id)->where('merged_brand', $request->brand_id)->where('product_id', $id)->exists()) {
        $mergedBrandProduct = MergedBrandProduct::where('customer_id', $request->customer_id)->where('merged_brand', $request->brand_id)->where('product_id', $id)->first();
        if (isset($mergedBrandProduct)) {
          $labelQtty = $mergedBrandProduct->merged_qty;
        }
      } else if (MergedBrandProduct::where('customer_id', $request->customer_id)->where('selected_brand', $request->brand_id)->where('product_id', $id)->exists()) {
        $mergedBrandProduct2 = MergedBrandProduct::where('customer_id', $request->customer_id)->where('selected_brand', $request->brand_id)->where('product_id', $id)->first();
        if (isset($mergedBrandProduct2)) {
          $labelQtty = $mergedBrandProduct2->merged_qty;
        }
      } else {
        $labelQtty = $selectedProduct->label_qty;
      }
    }
    $arr = array();
    $keys = array('customer_id', 'brand_id', 'product_id', 'customer_name', 'brand_name', 'product_name', 'label_qty', 'status');
    foreach ($showSameBrandProducts as $cProduct) {
      $mergedProduct = MergedBrandProduct::where('customer_id', $cProduct->customer_id)->where('merged_brand', $cProduct->brand_id)->where('product_id', $cProduct->product_id);
      $mergedProduct2 = MergedBrandProduct::where('customer_id', $cProduct->customer_id)->where('selected_brand', $cProduct->brand_id)->where('product_id', $cProduct->product_id);
      $status = 'false';
      $labelQty = $cProduct->label_qty;
      if ($mergedProduct->exists()) {
        $mergedProduct = $mergedProduct->first();
        if (isset($mergedProduct)) {
          $labelQty = $mergedProduct->merged_qty;
          $status = 'true';
        }
      } else if ($mergedProduct2->exists()) {
        $mergedProduct2 = $mergedProduct2->first();
        if (isset($mergedProduct2)) {
          $labelQty = $mergedProduct2->merged_qty;
          $status = 'true';
        }
      } else {
        $status = 'false';
        $labelQty = $cProduct->label_qty;
      }
      array_push($arr, array_combine($keys, [$cProduct->customer_id, $cProduct->brand_id, $cProduct->product_id, $cProduct->getcustomers->customer_name, $cProduct->brands->brand, $cProduct->products->name, $labelQty, $status]));
    }
    return response()->json(['products' => $arr, 'label_qty' => $labelQtty]);
  }
}
