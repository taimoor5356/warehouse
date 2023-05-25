@extends('admin.layout.app')
@section('title', 'Manage SKUs')
@section('datatablecss')

    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">

@stop

@section('content')

<style type="text/css">
    /* .dataTables_length{float: left;padding-left: 20px;}
    .dataTables_filter{padding-right:20px;}
    .dataTables_info{padding-left: 20px !important; padding-bottom:30px !important;}
    .dataTables_paginate{padding-right: 20px !important;} */
    td.details-control {
        background: url('https://datatables.net/examples/resources/details_open.png') no-repeat center center;
        cursor: pointer;
    }
    tr.shown td.details-control {
        background: url('https://datatables.net/examples/resources/details_close.png') no-repeat center center;
    }
    input[type="checkbox"]{
       background-color: #fff;
    }
</style>
<!-- BEGIN: Content-->
            <div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-8">
                                    <h2 class="content-header-title float-start mb-0">Manage SKUs</h2>
                                    <div class="breadcrumb-wrapper">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="/">Home</a>
                                            </li>
                                            <li class="breadcrumb-item">Customers
                                            </li>
                                            <li class="breadcrumb-item">Manage SKUs
                                            </li>
                                        </ol>
                                    </div>
                                </div>
                                <div class="col-4 addBtnClass">
                                    @can('sku_create')
                                    <a href="/sku/create" style="margin-left:auto;"  class="btn btn-primary waves-effect waves-float waves-light">Add New SKU</a>
                                    @endcan
                                    @can('sku_delete')
                                    <a href="/sku/trash" style="margin-left:auto;"  class="btn btn-danger waves-effect waves-float waves-light">View Trash</a>
                                    @endcan
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                
            </div>
            <div class="row" id="basic-table">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header text-right" style="display:block !important;">
                            <div class="row d-flex flex-row-reverse">
                                <div class="form-group col-sm-12 col-md-2">
                                    <label for="brand">Select Brand</label>
                                    <div class="invoice-customer">
                                        <select name="brand" id="brand" class="form-select select2" required>
                                            <option value="">All</option>
                                        </select>
                                        @error('brand')
                                        <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                        <div id="brand-error" class="text-danger font-weight-bold"></div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-12 col-md-2">
                                    <label for="customer">Select Customer</label>
                                    <select name="customer" id="customer" class="form-control select2">
                                        <option value="all">All</option>
                                        @foreach($customers as $customer)
                                            <option value="{{$customer->id}}">{{$customer->customer_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                        </div>
                        <div class="table-responsive p-1">
                            <table class="table data-table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 12%"><center><img class="show_all_btn" src="https://datatables.net/examples/resources/details_open.png" alt=""></center></th>
                                        <th style="width: 10%">SKU ID </th>
                                        <th style="width: 25%">SKU</th>
                                        <th style="width: 15%">Customer</th>
                                        <th style="width: 15%">Brand</th>
                                        <th style="width: 15%">Sell Price</th>
                                        <th style="width: 5%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
                <!-- Basic Tables end -->
       
    <!-- END: Content-->
    @section('modal')
        {{-- Add Inventory Modal --}}
        <div class="modal fade text-start show" id="inventoryModal" tabindex="-1" aria-labelledby="myModalLabel33" style="" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel33">Add Inventory</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="" method="post">
                        <div class="modal-body">
                            
                            <label>Inventory: </label>
                            <div class="mb-1">
                                <input type="number" min="1" placeholder="Inventory" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary waves-effect waves-float waves-light" id="add-inventory" data-bs-dismiss="modal">Add</button>
                            <button type="button" class="btn btn-dark waves-effect waves-float waves-light" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Basic toast -->
        <button class="btn btn-outline-primary toast-basic-toggler mt-2" id="toast-btn">ab</button>
        <div class="toast-container">
            <div class="toast basic-toast position-fixed top-0 end-0 m-2" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <i class="icon" data-feather="check"></i> &nbsp;&nbsp;&nbsp;
                    <strong class="me-auto">Vue Admin</strong>
                    <small class="text-muted">11 mins ago</small>
                    <button type="button" class="ms-1 btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">Hello, world! This is a toast message. Hope you're doing well.. :)</div>
            </div>
        </div>
    @endsection
    <script type="text/javascript">
          $(document).on('ready', function(){
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                stateSave: true,
                bDestroy: true,
                ajax: {
                url: "{{ route('sku.index') }}",
                    data: function (d) {
                        if ($('#customer :selected').text() == 'All') {
                            customername = ''
                        } else {
                            customername = $('#customer :selected').text();
                        }
                        if ($('#brand :selected').text() == 'All') {
                            brandname = ''
                        } else {
                            brandname = $('#brand :selected').text();
                        }
                        d.customer = customername;
                        d.brand = brandname;
                        // d.customer = $('#customer :selected').text();
                        // d.brand = $('#brand :selected').text();
                    }
                },
                columns: [
                    {'className': 'details-control', 'orderable': false, 'data': null, 'defaultContent': ''},
                    {data: 'sku_id', name: 'sku_id'},
                    {data: 'name', name: 'name'},
                    {data: 'customer', name: 'customer'},
                    {data: 'brand', name: 'brand'},
                    {data: 'cost', name: 'cost'},
                    // {data: 'product', name: 'product',orderable: false, searchable: false},
                    // {data: 'quantity', name: 'quantity', orderable: false, searchable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                "drawCallback": function( settings ) {
                    feather.replace();
                },
                'order': [[1, 'asc']],
            });
            $(document).on('click', '.show_all_btn', function() {
                var _this = $(this);
                $('.data-table tbody td.details-control').each(function() {
                    var tr = $(this).closest('tr');
                    var row = table.row( tr );
            
                    if ( row.child.isShown() ) {
                        // This row is already open - close it
                        row.child.hide();
                        tr.removeClass('shown');
                        _this.attr('src', 'https://datatables.net/examples/resources/details_open.png');
                        // _this.addClass('btn-success');
                        // _this.removeClass('btn-danger');
                        // _this.html('+');
                    }
                    else {
                        // Open this row
                        row.child( format(row.data()) ).show();
                        tr.addClass('shown');
                        _this.attr('src', 'https://datatables.net/examples/resources/details_close.png');
                        // _this.removeClass('btn-success');
                        // _this.addClass('btn-danger');
                        // _this.html('-');
                    }
                });
            });
            // Add event listener for opening and closing details
            $('.data-table tbody').on('click', 'td.details-control', function () {
                var tr = $(this).closest('tr');
                var row = table.row( tr );
        
                if ( row.child.isShown() ) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                }
                else {
                    // Open this row
                    row.child( format(row.data()) ).show();
                    tr.addClass('shown');
                }
            } );
            // Refilter the table
            $('#customer, #brand').on('change', function () {
                table.draw(false);
            });
            $(document).on('click', '#delete_sku', function() {
                var id = $(this).data('id');
                $.post('sku/'+id, {_method: "DELETE", _token:"{{csrf_token()}}"}, function(result) {
                    if(result.status == "success") {
                        table.draw(false);
                    } else {

                    }
                });
            });
            // get customer brand
            $(document).on('change', '#customer', function() {
                $('.customer-error').html('');
                var id = $(this).val();
                if(id=="all") {
                    $.get('/getAllBrands', function(result) {
                        if(result.status == "success") {
                            var options = "<option value=''>All</option>";
                            result.data.forEach(element => {
                                options += "<option value='"+element.id+"'>"+element.brand+"</option>"
                            });
                            
                            $('#brand-error').html('');
                            $('#brand').html(options);
                        } else {
                            $('#brand').html("<option value=''>All</option>");
                            $('#brand-error').html(result.message);
                        }
                    });
                } else {
                    $.get('/customer/'+id+'/brand', function(result) {
                        if(result.status == "success") {
                            var options = "<option value=''>All</option>";
                            result.data.forEach(element => {
                                options += "<option value='"+element.id+"'>"+element.brand+"</option>"
                            });
                            
                            $('#brand-error').html('');
                            $('#brand').html(options);
                        } else {
                            $('#brand').html("<option value=''>All</option>");
                            $('#brand-error').html(result.message);
                        }
                    });
                }
            });
          });
            /* Formatting function for row details - modify as you need */
            function format ( d ) {
                // `d` is the original data object for the row
                var html = '';
                html += `
                    <span style="font-weight: bold;">SKU Products</span>
                    <table class="table table-bordered w-100 mt-1">
                        <thead>`;
                html += `
                            <th class="p-1 text-center" style="padding:5px">Product</th>
                            <th class="p-1 text-center" style="padding:5px">Qty</th>
                            <th class="p-1 text-center" style="padding:5px">Labels</th>
                            <th class="p-1 text-center" style="padding:5px">Purchasing Cost</th>
                            <th class="p-1 text-center" style="padding:5px">Weight</th>
                            <th class="p-1 text-center" style="padding:5px">Service Charges</th>
                            <th class="p-1 text-center" style="padding:5px">Selling Cost</th>
                        </thead>
                        <tbody>`;
                for(var i = 0; i < d.sku_product.length; i++) {
                    var customer_id = d.brand_detail.customer.id;
                    var brand_id = d.brand_detail.id;
                    index = d.sku_product[i].product.customer_has_product.findIndex(x => x.brand_id == brand_id);
                    customer_has_product = d.sku_product[i].product.customer_has_product[index];

                    // index2 = d.sku_product[i].product.customer_product.findIndex(x => x.customer_id == customer_id);
                    // customerProduct = d.sku_product[i].product.customer_product[index2];

                    html += `
                        <tr>
                            <td class="p-1 text-center" style="padding:5px">`+d.sku_product[i].product.name+`</td>
                            <td class="p-1 text-center" style="padding:5px">1</td>
                            <td class="p-1 text-center" style="padding:5px">`+((customer_has_product.label_qty).toLocaleString())+`</td>
                            <td class="p-1 text-center" style="padding:5px">$ `+parseFloat(d.sku_product[i].purchasing_cost).toFixed(2)+`</td>
                            <td class="p-1 text-center" style="padding:5px">`+parseFloat(d.sku_product[i].product.weight).toFixed(2)+`</td>
                            <td class="p-1 text-center" style="padding:5px">`;

                    // for(var j = 0; j < 3; j++) {
                        if (customer_has_product.is_active == 0) {
                            // if (Number(d.sku_product[i].label) > 0) {
                                // alert();
                                html += `
                                <img src="{{ asset('images/checked.png') }}" width="10px" height="10px"> Label<br>
                                `;
                            // }
                        }
                        if (d.sku_product[i].pick > 0) {
                            html += `
                            <img src="{{ asset('images/checked.png') }}" width="10px" height="10px"> Pick<br>
                            `;
                        }
                        if (d.sku_product[i].pack > 0) {
                            html += `
                            <img src="{{ asset('images/checked.png') }}" width="10px" height="10px"> Pack<br>
                            `;
                        }
                    // }
                    
                    html += `</td>
                            <td class="p-1 text-center" style="padding:5px">$ `+parseFloat(d.sku_product[i].selling_cost).toFixed(2)+`</td>
                        </tr>
                    `;
                }
                       html += `</tbody>
                    </table>
                `;
                return html;
            }


        </script>
@endsection

@section('datatablejs')
    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/responsive.bootstrap4.min.js') }}"></script>

    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>

    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
@stop


