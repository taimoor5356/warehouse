@extends('admin.layout.app')
@section('title', 'OTW Inventory')
@section('datatablecss')

    <link rel="stylesheet" type="text/css"
        href="{{ asset('admin/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">

@stop

@section('datepickercss')
    <link rel="stylesheet" type="text/css"
        href="{{ URL::asset('admin/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
@stop
@section('content')

    <style type="text/css">
        .dataTables_length {
            float: left;
            padding-left: 20px;
        }

        .dataTables_filter {
            padding-right: 20px;
        }

        .dataTables_info {
            padding-left: 20px !important;
            padding-bottom: 30px !important;
        }

        .dataTables_paginate {
            padding-right: 20px !important;
        }

    </style>
    <style type="text/css">
        .bootstrap-touchspin.input-group-lg {
            width: 13.375rem !important;
        }

    </style>
    <!-- BEGIN: Content-->
    <div class="content-header row">
        <div class="content-header-left col-md-12 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <div class="row">
                        <div class="col-9">
                            <h2 class="content-header-title float-start mb-0">OTW Inventory</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item">Inventory
                                    </li>
                                    <li class="breadcrumb-item">OTW Inventory
                                    </li>
                                </ol>
                            </div>
                        </div>
                        <div class="col-3 addBtnClass">
                            @can('otw_inventory_create')
                                <a href="/otw_inventory/create" style="float:right;margin-right:15px;"
                                    class="btn btn-primary waves-effect waves-float waves-light">Add OTW Inventory</a>
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
                <div class="card-header" style="display:block !important;">
                    <div class="form-group col-sm-12 col-md-3 mt-1">
                        <label for="category">Select Category</label>
                        <select name="category" id="category_id" class="form-control select2 toggle-vis">
                            <option value="" selected>- Select Category -</option>
                            @foreach($categories as $category)
                                @isset($category)
                                    <option value="{{ $category->id }}" data-column="">{{ $category->name }}</option>
                                @endisset
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table data-table table-bordered">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Product</th>
                                <th style="width: 15%;">Description</th>
                                {{-- <th>Orders </th> --}}
                                <th>OTW</th>
                                <th>Purchase Date </th>
                                <th>Shipped Date </th>
                                <th>Expected Delivery Date </th>
                                <th>Days Left</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th><span class="otw-total"></span></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Basic Tables end -->
    {{-- move to stock modal --}}
    <div class="modal-size-default d-inline-block">
        <!-- Modal -->
        <div class="modal fade text-start" id="move_to_stock_modal" tabindex="-1" aria-labelledby="myModalLabel18"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel18">Move This to Stock</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form class="form form-horizontal" enctype='multipart/form-data'
                            action="{{ url('otw_inventory/storeStock') }}" method="post">
                            {{ @csrf_field() }}
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-1 row">
                                        <div class="col-sm-3">
                                            <input type="hidden" name="product_id" id="productId" />
                                            <input type="hidden" name="row_id" id="row_id" />
                                            <input type="hidden" name="otw_quantity" id="otw_quantity" />
                                            <label class="col-form-label" for="first-name">Quantity</label>
                                        </div>
                                        <div class="input-group input-group-lg">
                                            <input type="number" id="qty_stock" value="1" class="touchspin2"
                                                name="qty" />
                                            {{-- error message if qty_stock greater then otw --}}
                                            <p class="text-danger text-bold d-none" id="error_message_stock_qty">Quantity No
                                                be greater than Otw</p>
                                            @error('qty')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-1 row">
                                        <div class="col-sm-3">
                                            <label class="col-form-label" for="first-name">Description</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <textarea id="description" class="form-control description-input-field" name="description" value="{{ old('description') }}" placeholder="Enter description"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-1 row">
                                        <div class="col-sm-3">
                                            <label class="col-form-label" for="first-name">Delivered Date</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" id="fp-default" class="form-control flatpickr-basic flatpickr-input"
                                                name="date" value="{{ old('date') }}" placeholder="DD/MM/YYYY" />
                                            @error('date')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                            </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary me-1" id="move_to_stock">Submit</button>
                        <button type="reset" class="btn btn-outline-secondary">Reset</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- END: Content-->

    <script type="text/javascript">
        $(function() {

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                // ordering: true,
                stateSave: true,
                ajax: {
                    url: "{{ route('otw/inventory/admin') }}",
                    data: function(d) {
                        d.category_id = $('#category_id').val();
                    }
                },
                columns: [{
                        data: 'category_name',
                        name: 'category_name',
                        orderable: false
                    },
                    {
                        data: 'name',
                        name: 'name',
                        orderable: false
                    },
                    {
                        data: 'description',
                        name: 'description',
                        orderable: false
                    },
                    // {
                    //     data: 'upcoming',
                    //     name: 'upcoming'
                    // },
                    {
                        data: 'qty',
                        name: 'qty',
                        orderable: true
                    },
                    {
                        data: 'purchase_date',
                        name: 'purchase_date',
                        orderable: true
                    },
                    {
                        data: 'shipping_date',
                        name: 'shipping_date',
                        orderable: true
                    },
                    {
                        data: 'expected_delivery_date',
                        name: 'expected_delivery_date',
                        orderable: true
                    },
                    {
                        data: 'days_left',
                        name: 'days_left',
                        orderable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                'order': [
                    [3, 'desc']
                ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    // Remove the formatting to get integer data for summation
                    var intVal = function(i) {
                        return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i ===
                            'number' ? i : 0;
                    };
                    pageTotal1 = api
                        .column(3, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Update footer
                    $(api.column(3).footer()).find('.otw-total').html(pageTotal1);
                },
                "drawCallback": function(settings) {
                    feather.replace();
                },
            });
            $(document).on('change', '#category_id', function() {
                table.draw(false);
            });
        });

        function myFunction() {

            var r = confirm("Do you really want to delete this inventory?");
            if (r == true) {
                return true;
            } else {
                return false;
            }
            document.getElementById("demo").innerHTML = txt;
        }

        $(document).ready(function() {
            $('.touchspin2').TouchSpin({
                min: 1,
                max: 100000000,
                step: 1,
            });
            $(document).on('click', '.dropdown-item', function () {
                $('.description-input-field').val('');
            });
            $(".flatpickr-input").flatpickr({
                dateFormat: 'm/d/Y'
            });
            // on change of quantity in move to stock 
            $(document).on("keyup , change", "#qty_stock", function() {
                let qty = $(this).val();
                let otw_quantity = $('#otw_quantity').val();
                if (Number(qty) > Number(otw_quantity)) {
                    $("#error_message_stock_qty").removeClass('d-none');
                    $("#move_to_stock").attr("disabled", "disabled");
                } else {
                    $("#error_message_stock_qty").addClass('d-none');
                    $("#move_to_stock").removeAttr("disabled");

                }
            });
            //     
        });
        $('body').on("click", ".movestock", function() {
            var productId = $(this).data('product-id');
            var rowId = $(this).data('row-id');
            var otwQuantity = $(this).data('otw-quantity');
            $("#productId").val(productId);
            $("#row_id").val(rowId);
            $("#otw_quantity").val(otwQuantity);
        });
    </script>
@endsection
@section('datatablejs')
    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>

    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>


@stop
@section('datepickerjs')
    <script src="{{ URL::asset('admin/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/js/scripts/forms/pickers/form-pickers.js') }}"></script>


@stop
