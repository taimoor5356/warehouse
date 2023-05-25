@extends('admin.layout.app')
@section('title', 'Inventory History Report')
@section('datatablecss')

    <link rel="stylesheet" type="text/css"
        href="{{ asset('admin/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/forms/select/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/css/pages/app-invoice.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@stop

@section('datepickercss')
    <link rel="stylesheet" type="text/css"
        href="{{ URL::asset('admin/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ URL::asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ URL::asset('admin/app-assets/css/plugins/extensions/ext-component-sweet-alerts.css') }}">
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
        
        .popup {
            position: relative;
            display: inline-block;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        /* The actual popup */
        .popup .popuptext {
            visibility: visible;
            min-width: 150px;
            background-color: #555;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 4px 8px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 35%;
            margin-left: -55px;
            font-size: 12px;
            font-weight: bold;
            height: 25px;
        }
        /* Popup arrow */
        .popup .popuptext::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #555 transparent transparent transparent;
        }
        
        
        .popup {
            position: relative;
            display: inline-block;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        /* The actual popup */
        .popup .popuptext2 {
            visibility: visible;
            min-width: 150px;
            background-color: #555;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 4px 8px;
            position: absolute;
            z-index: 1;
            bottom: -70%;
            left: 35%;
            margin-left: -55px;
            font-size: 12px;
            font-weight: bold;
            height: 25px;
        }
        /* Popup arrow */
        .popup .popuptext2::after {
            content: "";
            position: absolute;
            top: -40%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #555 transparent transparent transparent;
            transform: rotate(180deg);
        }
    </style>

    <!-- BEGIN: Content-->
    <input type="hidden" id="p_id" value="0" />
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-start mb-0">Inventory History Report</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Home</a>
                            </li>
                            <li class="breadcrumb-item">Reports
                            </li>
                            <li class="breadcrumb-item active">Inventory History Report
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row" id="basic-table">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row w-100">
                        <div class="form-group col-sm-12 col-md-3">
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
                        <div class="form-group col-sm-12 col-md-3">
                            <label for="product">Select Product</label>
                            <select name="product" id="product" class="form-control select2 toggle-vis">
                                <option value="" selected>- Select Product -</option>
                                
                            </select>
                        </div>
                        <div class="form-group col-sm-12 col-md-3">
                            <label for="supplier_description_filter">Select</label>
                            <select name="supplier_description_filter" id="supplier_description_filter" class="form-control select2 toggle-vis">
                                <option value="" selected>- Select -</option>
                                <option value="manual_addition" >Manual Add</option>
                                <option value="manual_deduction" >Manual Deduct</option>
                                <option value="batch_edited" >Batch Edited</option>
                                <option value="cancelled_orders" >Cancelled Orders</option>
                                <option value="supplier_recvd" >Supplier Received</option>
                                <option value="description" >Description</option>
                                <option value="returned_orders" >Returned</option>
                                <option value="return_edited" >Return Edited</option>
                                <option value="sales" >Sales</option>
                                
                            </select>
                        </div>
                        {{-- <div class="form-group col-sm-12 col-md-3">
                            <label for="description">Description</label>
                            <input type="text" class="form-control" name="description" id="description" placeholder="Description">
                        </div>
                        <div class="form-group col-sm-12 col-md-3">
                            <label for="supplier-received">Supplier Received</label>
                            <input type="text" class="form-control" name="supplier_received" id="supplier-received" placeholder="Supplier Received">
                        </div> --}}
                        <div class="form-group col-sm-12 col-md-3">
                            <label for="product">Select Dates</label>
                            <input id="reportrange" class="form-control mydaterangepicker" style="cursor: pointer; background-color: white" readonly>
                        </div>
                        {{-- <div class="form-group col-sm-12 col-md-2">
                            <label for="min">Start</label>
                            <input type="text"
                                class="form-control flatpickr-human-friendly flatpickr-input flatpickr-mobile"
                                placeholder="MM/DD/YYYY" id="min" name="min">
                        </div>
                        <div class="form-group col-sm-12 col-md-2">
                            <label for="min">End</label>
                            <input type="text"
                                class="form-control flatpickr-human-friendly flatpickr-input flatpickr-mobile"
                                placeholder="MM/DD/YYYY" id="max" name="max">
                        </div> --}}
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table data-table table-bordered">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Product</th>
                                <th>Date</th>
                                <th>Manual Add </th>
                                <th>Batch Edited </th>
                                <th>Cancelled Orders </th>
                                <th>Supplier Received </th>
                                <th>Description </th>
                                <th>Returned </th>
                                <th>Return Edited </th>
                                <th>Manual Deduct </th>
                                <th>Sales </th>
                                <th>Total Inventory </th>
                                {{-- <th>Action</th> --}}
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
    @include('modals.modal')

@section('datatablejs')
    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>

    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/js/scripts/tables/range_dates.js') }}"></script>
@stop
@section('datepickerjs')
    <script src="{{ URL::asset('admin/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/js/scripts/forms/pickers/form-pickers.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/vendors/js/extensions/polyfill.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/js/scripts/extensions/ext-component-sweet-alerts.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
@stop
@endsection
<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('mouseenter', '.popup', function () {
            if ($(this).closest('tr').is(':first-child')) {
                $(this).closest('tr').find('.popuptext').removeClass('d-none');
                $(this).closest('tr').find('.popuptext').addClass('popuptext2');
                $(this).closest('tr').find('.popuptext2').removeClass('popuptext');
            } else {
                $(this).closest('tr').find('.popuptext').removeClass('d-none');
            }
        });
        $(document).on('mouseleave', '.popup', function () {
            $(this).closest('tr').find('.popuptext').addClass('d-none');
            if ($(this).closest('tr').is(':first-child')) {
                $(this).closest('tr').find('.popuptext2').addClass('d-none');
                $(this).closest('tr').find('.popuptext2').addClass('popuptext');
                $(this).closest('tr').find('.popuptext').removeClass('popuptext2');
            }
        });
        var url = "{{ route('inventory_history_report') }}";
        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ordering: false,
            scrollX: true,
            // stateSave: true,
            "ajax": {
                "url": url,
                data: function(d) {
                    let dates = $('#reportrange').val();
                    dates = dates.split(' - ');
                    let from_date = dates[0];
                    let to_date = dates[1];
                    // d.category_id = $('#category_id').val();
                    d.pid = $("#product").val();
                    d.from = from_date;
                    d.to = to_date;
                    d.description = $('#description').val();
                    d.supplier_received = $('#supplier-received').val();
                    d.supplier_description_filter = $('#supplier_description_filter').val();
                }
            },
            columns: [
                //   {data: 'username', name: 'username'},
                {
                    data: 'category_name',
                    name: 'category_name'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'date',
                    name: 'date'
                },
                {
                    data: 'manual_add',
                    name: 'manual_add'
                },
                {
                    data: 'batch_edited',
                    name: 'batch_edited'
                },
                {
                    data: 'cancelled_orders',
                    name: 'cancelled_orders'
                },
                {
                    data: 'supplier_received',
                    name: 'supplier_received'
                },
                {
                    data: 'description',
                    name: 'description'
                },
                {
                    data: 'returned',
                    name: 'returned'
                },
                {
                    data: 'return_edited',
                    name: 'return_edited'
                },
                {
                    data: 'manual_deduct',
                    name: 'manual_deduct'
                },
                {
                    data: 'sales',
                    name: 'sales'
                },
                {
                    data: 'total_inventory',
                    name: 'total_inventory'
                },
                //   {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            "drawCallback": function(settings) {
                feather.replace();
            }
        });
        $(document).on('change', '#category_id', function() {
            var id = $(this).val();
            if(id=="") {
                $('#product').html('<option value=""> - Select Product - </option>');
            } else {
                $.get('/category/'+id+'/products', function(result) {
                    if(result.status == "success") {
                        var options = "<option value=''> - Select Product - </option>";
                        result.data.forEach(element => {
                            options += "<option value='"+element.id+"'>"+element.name+"</option>"
                        });
                        $('#product').html(options);
                    } else {
                        $('#product').html("<option value=''> - Select Product - </option>");
                    }
                });
            }
        });
        $(document).on('change', '#min, #max, #product, #reportrange', function() {
            var date = new Date().getTime();
            localStorage.setItem('data_time', date);
            localStorage.setItem('reportrange_inventory_history_report', '');
            localStorage.setItem('reportrange_inventory_history_report', $('#reportrange').val());
            table.draw(false);
        });
        $(document).on('keyup', '#description, #supplier-received', function() {
            setTimeout(() => {
                table.draw(false);
            }, 3000);
        });
        $(document).on('change', '#supplier_description_filter', function() {
            table.draw(false);
        });
        $(".flatpickr-input").flatpickr({
            dateFormat: 'm/d/Y'
        });
        $(document).on('click', '.enter-pincode', function() {
            $('#enter_pin_Modal form input[type="password"]').focus();
            var href = $(this).attr('href');
            var type = $(this).data('type');
            var id = $(this).data('history-id');
            $('#enter-pin-code').attr('href', href);
            $('#enter-pin-code').data('type', type);
            $('#enter-pin-code').data('id', id);
        });
        $(document).on('click', '#enter-pin-code', function(e) {
            e.preventDefault();
            var pin_code = $('#inputPinCode').val();
            var type = $(this).data('type');
            var id = $(this).data('id');
            if (pin_code != '') {
                $.ajax({
                    url: '{{ route('pin_code.check_pin') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        'pin_code': pin_code
                    },
                    success: function(reponse) {
                        if (type == 'add') {
                            if (reponse.status == 'success') {
                                // confirmAdd(e);
                                $('#enter_pin_Modal').modal('toggle');
                                $('#inventoryModal').modal('toggle');
                            } else {
                                $('#pin_error').html(reponse.msg);
                            }
                        } else if (type == 'edit') {
                            if (reponse.status == 'success') {
                                var history_id = id;
                                $.get('/inventory/history/' + history_id + '/revert',
                                    function(result) {
                                        $('#enter_pin_Modal').modal('toggle');
                                        $('#inventoryModal').modal('toggle');
                                        // if(result.status == "success") {
                                        //     $('.toast .me-auto').html('Success');
                                        //     $('.toast .toast-header').addClass('bg-success');
                                        // } else {
                                        //     $('.toast .me-auto').html('Error');
                                        //     $('.toast .toast-header').addClass('bg-danger');
                                        // }
                                        $('.data-table').DataTable().ajax.reload();
                                        $('.toast .text-muted').html('Now');
                                        $('.toast .toast-body').html(result.message);
                                        $('#toast-btn').click();
                                    });
                                // confirmEdit(e);
                            } else {
                                $('#pin_error').html(reponse.msg);
                            }
                        } else if (type == 'delete') {
                            if (reponse.status == 'success') {
                                confirmDelete(e);
                            } else {
                                $('#pin_error').html(reponse.msg);
                            }
                        }
                    }
                });
            } else {
                $('#pin_error').html('Pin Required');
            }
        });
    });

    function confirmDelete(e) {
        var url = e.currentTarget.getAttribute('href');
        e.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this product!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-outline-secondary ms-1'
            },
            buttonsStyling: false
        }).then(function(result) {
            if (result.value) {
                window.location.replace(url);
            }
        });
    }

    function confirmEdit(e) {
        var url = e.currentTarget.getAttribute('href');
        window.location.replace(url);

    }

    function myFunction() {

        var r = confirm("Do you really want to delete this inventory?");
        if (r == true) {
            return true;
        } else {
            return false;
        }
        document.getElementById("demo").innerHTML = txt;
    }
    $(function() {
        var reportRange = localStorage.getItem('reportrange_inventory_history_report');
            if (reportRange == '01/01/2021 - 01/01/2021') {
                var start = moment().startOf('month');
                var end = moment();
            } else {
                reportRange = reportRange.split(' - ');
                var start = moment(reportRange[0]);
                var end = moment(reportRange[1]);
            }
        function cb(start, end) {
            $('#reportrange span').html(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
        }
        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
            'All Time': ['01/01/2021', moment()],
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'This Year': [moment().startOf('year'), moment().endOf('year')],
            'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
            }
        }, cb);
        cb(start, end);
    });
</script>
@endsection
