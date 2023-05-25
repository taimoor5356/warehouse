@extends('admin.layout.app')
@section('title', 'Labels Report')
@section('datatablecss')

    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
@stop

@section('content')

<style type="text/css">
    .dataTables_length{float: left;padding-left: 20px;}
    .dataTables_filter{padding-right:20px;}
    .dataTables_info{padding-left: 20px !important; padding-bottom:30px !important;}
    .dataTables_paginate{padding-right: 20px !important;}
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
                            <div class="col-6">
                            <h2 class="content-header-title float-start mb-0">Labels Report</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item">Reports
                                    </li>
                                    <li class="breadcrumb-item">Labels Report
                                    </li>
                                </ol>
                            </div>
                             </div>

                             {{-- <div class="col-6 addBtnClass">
                                @can('trash_view',\App\AdminModels\Labels::class)
                                <a href="#" style="float:right;"  class="btn btn-danger waves-effect waves-float waves-light"><i data-feather="trash-2"></i> View Trash</a>
                                @endcan
                                 @can('create',\App\AdminModels\Labels::class)
                                <a href="#" style="float:right;margin-right:15px;"  class="btn btn-primary waves-effect waves-float waves-light">Add New Brand</a>
                                @endcan
                            </div> --}}
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
                                {{-- <div class="form-group col-sm-12 col-md-2" id="search">
                                    <label for="search"></label>
                                    <input type="submit" class="form-control btn btn-primary" value="Filter">
                                </div> --}}
                                <div class="form-group col-sm-12 col-md-2">
                                    <label for="days-filter">Select Days</label>
                                    <div class="dayfilter">
                                        <select name="days_filter" id="days-filter" class="form-select select2" required>
                                            <option value="">All</option>
                                            <option value="under">Under 365 days</option>
                                            <option value="over">Over 365 days</option>
                                            <option value="no_sale">No Sale for 12 days</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-sm-12 col-md-2">
                                    <label for="brand">Select Brand</label>
                                    <div class="invoice-customer">
                                        <select name="brand" id="brand" class="form-select select2" required>
                                            <option value="all">All</option>
                                            {{-- @foreach($brands as $brand)
                                                <option value="{{$brand->id}}">{{$brand->brand}}</option>
                                            @endforeach --}}
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
                        <div class="table-responsive">
                            <table class="table data-table table-bordered">
                                <thead>
                                    <tr>
                                        {{-- <th style="width: 12%"><center><img class="show_all_btn" src="https://datatables.net/examples/resources/details_open.png" alt=""></center></th> --}}
                                        <th>#</th>
                                        <th>Customer</th>
                                        <th>Brand</th>
                                        <th>Product</th>
                                        <th>Labels </th>
                                        <th>Forecast</th>
                                        <th>Actions</th>
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
    <div class="modal fade text-start show" id="labelsModal" tabindex="-1" aria-labelledby="myModalLabel33" style="" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Add Labels</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form>
                    <div class="modal-body">
                        
                        <label>Labels: </label>
                        <div class="mb-1">
                            <input type="number" min="1" placeholder="" name="label_qty" class="form-control" id="qtyPrice">
                        </div>
                        <small class="d-none negative_err text-danger">Can't Add Negative Value</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary waves-effect waves-float waves-light" id="add-labels" data-brand-id="" data-customer_id="" data-bs-dismiss="modal" data-product-id="">Add</button>
                        <button type="button" class="btn btn-dark waves-effect waves-float waves-light" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade text-start show" id="reducelabelsModal" tabindex="-1" aria-labelledby="myModalLabel35" style="" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel35">Reduce Labels</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form>
                    <div class="modal-body">
                        
                        <label>Labels: </label>
                        <div class="mb-1">
                            <input type="number" min="1" placeholder="" name="label_qty" class="form-control" id="reduceqtyPrice">
                        </div>
                        <small class="d-none negative_err text-danger">Can't Add Negative Value</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary waves-effect waves-float waves-light" id="reduce-labels" data-brand-id="" data-customer_id="" data-bs-dismiss="modal" data-product-id="">Submit</button>
                        <button type="button" class="btn btn-dark waves-effect waves-float waves-light" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade text-start show" id="labelCostModal" tabindex="-1" aria-labelledby="myModalLabel34" style="" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel34">Update Label Charges</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form>
                    <div class="modal-body">
                         {{--  --}}
                        <div class="form-check form-switch form-check-primary mb-1">
                            <input type="checkbox" name="label_cost_type"  class="form-check-input " id="label_cost_type" value="1" style="font-size: 50px; width:60px;">
                            <label class="form-check-label">
                                <span class="switch-icon-left" data-on="default" style="font-size: 9px; margin-top: 6px; ">Default</span>
                                <span class="switch-icon-right" data-off="custom" style="font-size: 9px; margin-top: 6px; margin-left: -6px; color: rgb(88, 4, 4); ">Custom</span>
                            </label>
                        </div>
                        {{--  --}}
                        
                        <label>Labels Cost: </label>
                        <div class="mb-1">
                            <input type="number" min="1" placeholder="" name="label_cost" class="form-control" id="labelCost">
                        </div>
                        <small class="d-none negative_err text-danger">Can't Add Negative Value</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary waves-effect waves-float waves-light" id="add-label-cost" data-brand-id="" data-customer_id="" data-bs-dismiss="modal" data-product-id="">Update</button>
                        <button type="button" class="btn btn-dark waves-effect waves-float waves-light" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

        <!-- Basic toast -->
        <button class="btn btn-outline-primary toast-basic-toggler mt-2" id="toast-btn"></button>
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

        $(document).ready(function(){
            // get customer brand
            $(document).on('change', '#customer', function() {
                $('.customer-error').html('');
                var id = $(this).val();
                if(id=="all") {
                    $('#brand').html('<option value="all" selected>All</option>');
                    // $.get('/getAllBrands', function(result) {
                    //     if(result.status == "success") {
                    //         var options = "<option value='all' selected>All</option>";
                    //         result.data.forEach(element => {
                    //             options += "<option value='"+element.id+"'>"+element.brand+"</option>"
                    //         });
                            
                    //         $('#brand-error').html('');
                    //         $('#brand').html(options);
                    //     } else {
                    //         $('#brand').html("<option value='all' selected>All</option>");
                    //         $('#brand-error').html(result.message);
                    //     }
                    // });
                } else {
                    $.get('/customer/'+id+'/brand', function(result) {
                        if(result.status == "success") {
                            var options = "<option value='all' selected>All</option>";
                            $('#brand').val('all');
                            result.data.forEach(element => {
                                options += "<option value='"+element.id+"'>"+element.brand+"</option>"
                            });
                            
                            $('#brand-error').html('');
                            $('#brand').html(options);
                        } else {
                            $('#brand').html("<option value='all' selected>All</option>");
                            $('#brand-error').html(result.message);
                        }
                    });
                }
            });
        });
        $(function () {
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                // ordering: true,
                searchDelay: 2000,
                stateSave: true,
                buttons: [
                    'pdf'
                ],
                // paging: false,
                // bFilter: false,
                // ajax: "{{ route('label/admin') }}",
                ajax: {
                    url: "{{ route('labels_report') }}",
                    data: function (d) {
                        // if ($('#customer :selected').text() == 'All') {
                        //     customername = ''
                        // } else {
                        //     customername = $('#customer :selected').text();
                        // }
                        // if ($('#brand :selected').text() == 'All') {
                        //     brandname = ''
                        // } else {
                        //     brandname = $('#brand :selected').text();
                        // }
                        d.customer_id = $('#customer').val();
                        d.brand_id = $('#brand').val();
                        d.days_filter = $('#days-filter').val();
                    }
                },
                columns: [
                    // {'className': 'details-control', 'orderable': false, 'data': null, 'defaultContent': ''},
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'customer_name', name: 'customer_name', orderable: false},
                    {data: 'brand_name', name: 'brand_name', orderable: false},
                    {data: 'prod_name', name: 'prod_name', orderable: false},
                    {data: 'cust_has_label_qty', name: 'cust_has_label_qty', orderable: true},
                    {data: 'forecast_days', name: 'forecast_days', orderable: true},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                'order': [[5, 'asc']],
                "drawCallback": function( settings ) {
                    feather.replace();
                },
            });
            $('#customer, #brand').on('change', function () {
                setTimeout(() => {
                    table.draw();
                }, 300);
            });
            $('#days-filter').on('change', function() {
                table.draw(false);
            });
            // Refilter the table
            $('#search').on('click', function () {
                table.draw();
            });
            $("#label_cost_type").on('change', function(){  
              var labelCostType=$("#label_cost_type").val();
                // custom 0
                // default 1
                if (labelCostType==1) {
                    var url = '{{ route("toggleLabelCost") }}';
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                        },
                        success:function(response) {
                            $("#labelCost").val(response.lable_cost);
                            $("#labelCost").attr("readonly",true);                
                        }
                    });
                    $("#label_cost_type").val(0);
                } else {
                    $("#labelCost").attr("readonly",false);
                    $("#labelCost").val('');
                    $("#label_cost_type").val(1);
                }
            });
        $(document).on('click', '.more_btn', function() {
            var customer_id = $(this).attr('data-customer_id');
            var brand_id = $(this).closest('tr').find('.brandId').val();
            var product_id = $(this).closest('tr').find('.productId').val();
            $(this).data('product-id', product_id);
            $('.add-labels').data('product-id', product_id);
            $('.add-labels').data('customer_id', customer_id);
            $('.reduce-labels').data('product-id', product_id);
            $('.reduce-labels').data('customer_id', customer_id);
            $('.add-label-cost').data('product-id', product_id);
            $('.add-label-cost').data('customer_id', customer_id);
            $('.history_more_btn').data('product-id', product_id);
            $('.history_more_btn').attr('href', '/customer/'+customer_id+'/brand/'+brand_id+'/product/'+product_id+'/labels');
            $('.forecast_more_btn').data('product-id', product_id);
            $('.forecast_more_btn').attr('href', '/customer/'+customer_id+'/brand/'+brand_id+'/product/'+product_id+'/labelforecast');
            $('.negative_err').addClass('d-none');
        });
        $(document).on('click', '.add-labels', function() {
            var product_id = $(this).data('product-id');
            var brand_id = $(this).data('brand-id');
            var customer_id = $(this).data('customer_id');
            $('#add-labels').data('product-id', product_id);
            $('#add-labels').data('brand-id', brand_id);
            $('#add-labels').data('customer_id', customer_id);
        });
        $(document).on('click', '.reduce-labels', function() {
            var product_id = $(this).data('product-id');
            var brand_id = $(this).data('brand-id');
            var customer_id = $(this).data('customer_id');
            $('#reduce-labels').data('product-id', product_id);
            $('#reduce-labels').data('brand-id', brand_id);
            $('#reduce-labels').data('customer_id', customer_id);
        });
        $(document).on('click', '#reduce-labels', function() {
            var customer_id = $(this).data('customer_id');
            var brand_id = $(this).data('brand-id');
            var product_id = $(this).data('product-id');
            var qty = $('#reduceqtyPrice').val();
            if (qty < 0) {
                $('.negative_err').removeClass('d-none');
                return false;
            } else {
                $('.negative_err').addClass('d-none');
            }
            var url = '{{ route("reduce_label_to_product") }}';
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    'customer_id': customer_id,
                    'brand_id': brand_id,
                    'product_id': product_id,
                    'qty': qty,
                },
                success:function(response) {
                    $('#reducelabelsModal').hide();
                    // window.location.reload();
                    table.draw();
                }
            });
        });
        $(document).on('click', '#add-labels', function() {
            table.clear();
            var customer_id = $(this).data('customer_id');
            var brand_id = $(this).data('brand-id');
            var product_id = $(this).data('product-id');
            var qty = $('#qtyPrice').val();
            if (qty < 0) {
                $('.negative_err').removeClass('d-none');
                return false;
            } else {
                $('.negative_err').addClass('d-none');
            }
            var url = '{{ route("add_label_to_product") }}';
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    'customer_id': customer_id,
                    'brand_id': brand_id,
                    'product_id': product_id,
                    'qty': qty,
                },
                success:function(response) {
                    $('#labelsModal').hide();
                    // window.location.reload();
                    table.draw();
                }
            });
        });
        $(document).on('click', '.add-label-cost', function() {
            var product_id = $(this).data('product-id');
            var brand_id = $(this).data('brand-id');
            var customer_id = $(this).data('customer_id');
            $('#add-label-cost').data('product-id', product_id);
            $('#add-label-cost').data('brand-id', brand_id);
            $('#add-label-cost').data('customer_id', customer_id);
            
        });
        $(document).on('click', '#add-label-cost', function() {
            var customer_id = $(this).data('customer_id');
            var brand_id = $(this).data('brand-id');
            var product_id = $(this).data('product-id');
            var label_cost = $('#labelCost').val();
            if (label_cost < 0) {
                $('.negative_err').removeClass('d-none');
                return false;
            } else {
                $('.negative_err').addClass('d-none');
            }
            var url = '{{ route("add_label_cost_to_product") }}';
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    'customer_id': customer_id,
                    'brand_id': brand_id,
                    'product_id': product_id,
                    'label_cost': label_cost,
                },
                success:function(response) {
                    $('#labelCostModal').hide();
                    // window.location.reload();
                    table.draw();
                }
            });
        });
        });
        </script>
@endsection
@section('datatablejs')
    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
@stop

