@extends('admin.layout.app')
@section('title', 'Purchased Inventory')
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
                            <h2 class="content-header-title float-start mb-0">Purchased Inventory</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item">Inventory
                                    </li>
                                    <li class="breadcrumb-item"><a href="/upcoming_inventory">Purchased Inventory</a>
                                    </li>
                                </ol>
                            </div>
                        </div>
                        <div class="col-3 addBtnClass">
                            @can('upcoming_inventory_create')
                                <a href="/upcoming_inventory/create" style="float:right;margin-right:15px;"
                                    class="btn btn-primary waves-effect waves-float waves-light">Add Purchased Inventory</a>
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
                                <th>Purchase Quantity </th>
                                <th>OTW Quantity </th>
                                <th>Description</th>
                                <th>Purchase Date</th>
                                <th>Days Ordered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th><span class="upcoming-total"></span></th>
                                @php 
                                    $sumOfOtw = \App\AdminModels\OtwInventory::sum('qty');
                                @endphp
                                <th>Total <span class="otw-total">({{ $sumOfOtw }})</span></th>
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
    {{-- otw incentory modal --}}
    <div class="modal-size-default d-inline-block">
        <!-- Modal -->
        <div class="modal fade text-start" id="move_to_otw_modal" tabindex="-1" aria-labelledby="myModalLabel18"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel18">Move This to OTW</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form class="form form-horizontal" enctype='multipart/form-data'
                            action="{{ route('otw_inventory.store') }}" method="post">
                            {{ @csrf_field() }}
                            <div class="row">

                                <div class="col-12">
                                    <div class="mb-1 row">
                                        <div class="col-sm-3">
                                            <input type="hidden" name="product_id" id="productId" />
                                            <input type="hidden" name="row_id" id="row_id" />
                                            <input type="hidden" name="upcoming_quantity" id="upcoming_quantity" />
                                            <label class="col-form-label" for="first-name">Quantity</label>
                                        </div>
                                        <div class="input-group input-group-lg">
                                            <input type="number" id="qty" value="1" min="1" class="touchspin2"
                                                name="qty" />
                                            {{-- error message if qty greater then upcoming --}}
                                            <p class="text-danger text-bold d-none" id="error_message_otw_qty">Quantity No be
                                                greater than Purchased</p>
                                            @error('qty')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-1 row">
                                        <div class="col-sm-3">
                                            <label class="col-form-label" for="description">Supplier/Notes</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <textarea name="description" id="description" cols="10" rows="2" class="form-control description-input-field"
                                                value="{{ old('description') }}"></textarea>
                                            @error('description')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-1 row">
                                        <div class="col-sm-3">
                                            <label class="col-form-label" for="first-name">Shipping Date</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" id="fp-default" class="form-control flatpickr-basic flatpickr-input"
                                                name="shipping_date" value="{{ old('date') }}" placeholder="MM/DD/YYYY" />
                                            @error('date')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-1 row">
                                        <div class="col-sm-3">
                                            <label class="col-form-label" for="first-name">Expected Delivery Date</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" id="fp-default" class="form-control flatpickr-basic flatpickr-input"
                                                name="expected_delivery_date" value="{{ old('expected_delivery_date') }}"
                                                placeholder="MM/DD/YYYY" />
                                            @error('expected_delivery_date')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary me-1" id="move_to_otw">Submit</button>
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
                ordering: true,
                stateSave: true,
                ajax: {
                    url: "{{ route('upcoming/inventory/admin') }}",
                    data: function (d) {
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
                        data: 'qty',
                        name: 'qty',
                        orderable: true
                    },
                    {
                        data: 'otw',
                        name: 'otw',
                        orderable: true
                    },
                    {
                        data: 'description',
                        name: 'description',
                        orderable: false
                    },
                    {
                        data: 'purchase_date',
                        name: 'purchase_date',
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
                    [2, 'desc']
                ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    // Remove the formatting to get integer data for summation
                    var intVal = function(i) {
                        return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i ===
                            'number' ? i : 0;
                    };
                    pageTotal1 = api
                        .column(2, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Update footer
                    $(api.column(2).footer()).find('.upcoming-total').html(pageTotal1);
                    
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
            // on change of quantity in move to otw 
            $(document).on("keyup , change", "#qty", function() {
                let qty = $(this).val();
                let upcoming_quantity = $('#upcoming_quantity').val();
                if (Number(qty) > Number(upcoming_quantity)) {
                    $("#error_message_otw_qty").removeClass('d-none');
                    $("#move_to_otw").attr("disabled", "disabled");
                } else {
                    $("#error_message_otw_qty").addClass('d-none');
                    $("#move_to_otw").removeAttr("disabled");
                }
            });
            $(document).on('click', '.dropdown-item', function () {
                $('.description-input-field').val('');
            });
            $(".flatpickr-input").flatpickr({
                dateFormat: 'm/d/Y'
            });
            //                                          
        });
        $('body').on("click", ".moveotw", function() {
            var productId = $(this).data('product-id');
            var rowId = $(this).data('row-id');
            var upcomingQuantity = $(this).data('upcoming-quantity');
            $("#productId").val(productId);
            $("#row_id").val(rowId);
            $("#upcoming_quantity").val(upcomingQuantity);
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
