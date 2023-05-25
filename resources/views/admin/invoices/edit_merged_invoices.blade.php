@extends('admin.layout.app')
@section('title', 'Edit Merged Invoices')
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

        .rowColor{
            background-color: rgb(245, 245, 255);
        }
        input[type="checkbox"]:checked:after{
            background-color: rgb(149, 46, 46);
        }
        input[type="checkbox"]:disabled:after{
            background-color: rgb(186, 90, 90);
        }
        .loader {
            border: 2px solid #f3f3f3;
            border-radius: 50%;
            border-top: 2px solid transparent;
            width: 15px;
            height: 15px;
            -webkit-animation: spin 1s linear infinite; /* Safari */
            animation: spin 1s linear infinite;
        }

        /* Safari */
        @-webkit-keyframes spin {
        0% { -webkit-transform: rotate(0deg); }
        100% { -webkit-transform: rotate(360deg); }
        }

        @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
        }

    </style>
    <!-- BEGIN: Content-->
    <div class="content-header row">
        <div class="content-header-left col-md-12 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <div class="row">
                        <div class="col-6">
                            <h2 class="content-header-title float-start mb-0">Edit Merged Invoices</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item">Invoices
                                    </li>
                                    <li class="breadcrumb-item">Edit Merged Invoices
                                    </li>
                                    <input type="hidden" id="id" value="{{ $id }}">
                                </ol>
                            </div>
                        </div>
                        <div class="col-6 addBtnClass">
                            <button type="button" style="margin-left:auto;"
                                class="btn btn-success waves-effect waves-float waves-light merge_btn" disabled>Update &nbsp; <div class="loader d-none" style="float: right"></div></button>
                                <a href="{{ route('view_merged_invoices') }}" style="margin-left:auto;"
                                    class="btn btn-primary waves-effect waves-float waves-light">View Merged
                                    Invoices</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" id="basic-table">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        {{-- <div class="col-sm-4">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label class="col-form-label" for="from_date">From:</label>
                                </div>
                                <div class="col-sm-12">
                                    <input type="text" id="from_date" class="form-control flatpickr-basic from_date"
                                        name="from_date" value="{{ old('from_date') }}" placeholder="YYYY-MM-DD" />
                                    @error('from_date')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label class="col-form-label" for="to_date">To:</label>
                                </div>
                                <div class="col-sm-12">
                                    <input type="text" id="to_date" class="form-control flatpickr-basic to_date"
                                        name="to_date" value="{{ old('to_date') }}" placeholder="YYYY-MM-DD" />
                                    @error('to_date')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table data-table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><input type="checkbox" id="checkAllOrders" value="1"/></th>
                                <th>Date</th>
                                <th>Customer Name</th>
                                <th>Total Cost</th>
                                <th>Invoice Number</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @section('modal')
        {{-- Add Inventory Modal --}}
        <div class="modal fade text-start show" id="inventoryModal" tabindex="-1" aria-labelledby="myModalLabel34" style=""
            aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel34">Add Inventory</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="" method="post">
                        <div class="modal-body">
    
                            <label>Add Inventory: </label>
                            <div class="mb-1">
                                <input type="number" min="1" placeholder="Add Inventory" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary waves-effect waves-float waves-light"
                                id="add-inventory" data-bs-dismiss="modal">Submit</button>
                            <button type="button" class="btn btn-dark waves-effect waves-float waves-light"
                                data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade text-start show" id="reduceinventoryModal" tabindex="-1" aria-labelledby="myModalLabel35"
            style="" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel35">Reduce Inventory</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="" method="post">
                        <div class="modal-body">
    
                            <label>Reduce Inventory: </label>
                            <div class="mb-1">
                                <input type="number" min="1" placeholder="Reduce Inventory" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary waves-effect waves-float waves-light"
                                id="reduce-inventory" data-bs-dismiss="modal">Submit</button>
                            <button type="button" class="btn btn-dark waves-effect waves-float waves-light"
                                data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @include('modals.modal')
        {{--  --}}
        {{-- Invoice Detail Modal --}}
        {{-- end modal move to stock --}}
        <!-- Basic toast -->
        <button class="btn btn-outline-primary toast-basic-toggler mt-2" id="toast-btn">ab</button>
        <div class="toast-container">
            <div class="toast basic-toast position-fixed top-0 end-0 m-2" role="alert" aria-live="assertive"
                aria-atomic="true">
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
    <!-- Basic Tables end -->

    <!-- END: Content-->

    <script type="text/javascript">
        $(function() {
            var id = $('#id').val();
            var url = "{{ route('edit_merged_invoices', ':id') }}";
            url = url.replace(':id', id);
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                stateSave: true,
                bDestroy: true,
                paging: false,
                ajax: {
                    url: url,
                },
                columns: [
                    {
                        data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: true, searchable: false,
                    },
                    {
                        data: 'invoice_checkbox',
                        name: 'invoice_checkbox',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'customer_name',
                        name: 'customer_name'
                    },
                    //   {data: 'batch_no', name: 'batch_no'},
                    {
                        data: 'total_cost',
                        name: 'total_cost'
                    },
                    // {
                    //     data: 'order_number',
                    //     name: 'order_number'
                    // },
                    {
                        data: 'invoice_number',
                        name: 'invoice_number'
                    },
                    //{data: 'status', name: 'status'},
                ],
                "drawCallback": function(settings) {
                    feather.replace();
                },
            });
            // Create date inputs
            from_date = new Date($('#from_date'), {
                format: 'MMMM Do YYYY'
            });
            to_date = new Date($('#to_date'), {
                format: 'MMMM Do YYYY'
            });

            // Refilter the table
            $('#from_date, #to_date, #batch_no').on('change', function() {
                table.draw(false);
            });
        });
        function myFunction() {

            var r = confirm("Do you really want to delete this invoice?");
            if (r == true) {
                return true;
            } else {
                return false;
            }
            document.getElementById("demo").innerHTML = txt;
        }
        $(document).on('change', '#from_date', function() {
        });
        $("body").on("change", "#invoice_is_paid", function() {
            var isPaidId = $(this).val();
            var invoiceId = $(this).data('invoice-id');
            $.ajax({
                type: 'POST',
                url: '/updateInvoiceStatus',
                data: {
                    "invoice_id": invoiceId,
                    "is_paid": isPaidId,
                    _token: "{{ csrf_token() }}",
                    dataType: "JSON",
                },
                success: function(data) {
                    console.log('success');
                }
            });
        });
        $(document).on('click', '.singleOrderCheck', function() {
            $('.merge_btn').prop('disabled', false);
            $('#checkAllOrders').prop('checked', false);
            var length = 0;
            var invoiceIdsArr = [];
            var orderIdsArr = [];
            var customerIdsArr = [];
            var invoiceNumberArr = [];
            var invNumberArr = [];
            $('.singleOrderCheck').each(function() {
                var _this = $(this);
                if (_this.is(':checked')) {
                    length += 1;
                    var invoiceId = _this.attr('data-invoice-id');
                    var orderId = _this.attr('data-invoice-order-id');
                    var customerId = _this.attr('data-invoice-customer-id');
                    var invoiceNumber = _this.attr('data-invoice-number');
                    var invNumber = _this.attr('data-inv-no');
                    invoiceIdsArr.push(invoiceId);
                    orderIdsArr.push(orderId);
                    customerIdsArr.push(customerId);
                    invoiceNumberArr.push(invoiceNumber);
                    invNumberArr.push(invNumber);
                    window.invoiceIdsArr = invoiceIdsArr;
                    window.orderIdsArr = orderIdsArr;
                    window.customerIdsArr = customerIdsArr;
                    window.invoiceNumberArr = invoiceNumberArr;
                    window.invNumberArr = invNumberArr;
                }
            });
            if (length >= 2) {
                // $('.merge_btn').prop('disabled', false);
                // $('.merge_btn').removeClass('btn-warning');
                // $('.merge_btn').addClass('btn-success');
            } else {
                // $('.merge_btn').prop('disabled', true);
                // $('.merge_btn').removeClass('btn-success');
                // $('.merge_btn').addClass('btn-warning');
            }

        });
        $(document).on("change", "#checkAllOrders", function() {
            $('.merge_btn').prop('disabled', false);
            if (this.checked) {
                $('.singleOrderCheck').prop("checked", true);
                // $('.merge_btn').prop('disabled', false);
                // $('.merge_btn').removeClass('btn-warning');
                // $('.merge_btn').addClass('btn-success');
            } else {
                $('.singleOrderCheck').prop("checked", false);
                // $('.merge_btn').prop('disabled', true);
                // $('.merge_btn').removeClass('btn-success');
                // $('.merge_btn').addClass('btn-warning');
            }
        });
        $(document).on('click', '.merge_btn', function() {
            $(this).prop('disabled', true);
            $('.loader').removeClass('d-none');
            var id = $('#id').val();
            var url = "{{ route('update_merged_invoices', ':id') }}";
            url = url.replace(':id', id);
            var allChecked = 0;
            var invoicesLength = $('.singleOrderCheck:checked').length;
            if ($('#checkAllOrders').is(':checked')) {
                allChecked = 1;
            }
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    invoiceIds: window.invoiceIdsArr,
                    orderIds: window.orderIdsArr,
                    customerIds: window.customerIdsArr,
                    invoiceNumbers: window.invoiceNumberArr,
                    invNumbers: window.invNumberArr,
                    all_checked: allChecked,
                    invoices_length: invoicesLength,
                },
                success:function(response) {
                    $('.loader').addClass('d-none');
                    if (response.message == true) {
                        alert(response.data);
                        window.location.href = '/view-merged-invoices';
                    } else if(response.message == false) {
                        alert(response.data);
                    }
                }
            });
        });
        $("body").on("change", "#all_order_status_change", function() {

            var statusId = $(this).val();
            if ($('#checkAllOrders').is(':checked')) {
                $('.singleOrderCheck').each(function() {

                    if ($(this).is(':checked')) {
                        var orderId = $(this).data('singleorder-id');

                        $.ajax({
                            type: 'POST',
                            url: '/updateOrderStatus',
                            data: {
                                "order_id": orderId,
                                "status_id": statusId,
                                _token: "{{ csrf_token() }}",
                                dataType: "JSON",
                            },
                            success: function(data) {

                                console.log('success');
                            }
                        });
                    }


                });
            }

        });
        $('.mergedToolTip').tooltip({show: {effect: 'none', delay: 0}});
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