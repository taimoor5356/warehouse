@extends('admin.layout.app')
@section('title', 'Partial Payments')
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

    </style>
    <!-- BEGIN: Content-->
    <div class="content-header row">
        <div class="content-header-left col-md-12 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <div class="row">
                        <div class="col-6">
                            <h2 class="content-header-title float-start mb-0">Partial Payments</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item">Invoices
                                    </li>
                                    <li class="breadcrumb-item">Partial Payments
                                    </li>
                                </ol>
                            </div>
                        </div>
                        <div class="col-6 addBtnClass">
                            
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
                    <input type="hidden" value="{{$id}}" id="id">
                </div>
                <div class="table-responsive">
                    <table class="table data-table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Paid</th>
                                <th>Remaining</th>
                                <th>Action</th>
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
        
        <div class="modal fade text-start show" id="updateIsPaidStatusModal" tabindex="-1" aria-labelledby="myModalLabel37" style=""
            aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel37">Update Partial Payments</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form class="update-partial-payment-form">
                        @csrf
                        <div class="modal-body">
                            <label>Enter Password: </label>
                            <div class="mb-1">
                                <input type="password" name="password" min="1" class="password form-control" placeholder="Enter Password" required>
                                <small class="error-message text-danger"></small>
                            </div>
                            <label>Select Status: </label>
                            <div class="mb-1">
                                <select name="is_paid" class="is_paid_status form-control" data-total-amount="" required>
                                    <option value="" selected disabled>Select</option>
                                    <option value="1">Paid</option>
                                    <option value="2">Partially Paid</option>
                                </select>
                            </div>
                            <label>Add Amount: </label>
                            <div class="mb-1">
                                <input type="text" name="amount" min="1" class="partial-amount form-control" placeholder="Add Amount" required>
                                <input type="hidden" name="invoice_id" class="invoice-id" value="{{ $id }}">
                                <input type="hidden" name="row_id" class="row-id" value="{{ $id }}">
                            </div>
                        </div>
                        <div class="modal-footer d-inline">
                            <div class="row">
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <div class="">
                                        <small class="fields-error text-danger">
                                        </small>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 ms-auto text-end">
                                    <button type="button" class="btn btn-primary waves-effect waves-float waves-light update_partial_payments">Submit</button>
                                </div>
                            </div>
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
            var url = '{{ route("partial_payments", ":id") }}',
            url = url.replace(':id', id);
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                stateSave: true,
                bDestroy: true,
                paging: false,
                ajax: {
                    url: url
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'total',
                        name: 'total'
                    },
                    {
                        data: 'paid',
                        name: 'paid'
                    },
                    {
                        data: 'remaining',
                        name: 'remaining'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    }
                ],
                "drawCallback": function(settings) {
                    feather.replace();
                }
            });
            $(document).on('click', '.edit-partial-payment', function() {
                $('.row-id').val($(this).attr('data-id'));
                $('.is_paid_status').attr('data-total-amount', $(this).attr('data-total-amount'));
            });
            $(document).on('change', '.is_paid_status', function() {
                if ($(this).val() == 1) {
                    $('.partial-amount').attr('readonly', true);
                    $('.partial-amount').val($(this).attr('data-total-amount'));
                } else if ($(this).val() == 2) {
                    $('.partial-amount').attr('readonly', false);
                    $('.partial-amount').val('');
                }
            });
            $(document).on('click', '.update_partial_payments', function () {
                var data = $('.update-partial-payment-form').serialize();
                $.ajax({
                    url: '{{ route("update_partial_payments") }}',
                    type: 'POST',
                    dataType: 'JSON',
                    data: data,
                    success:function(response) {
                        if (response.status == 'password_error') {
                            $('.error-message').html(response.message);
                        } else if (response.status == 'prev_check_error') {
                            $('.fields-error').html(response.message);
                        } else if (response.status == true){
                            $('.error-message').html('');
                            $('.fields-error').html('');
                            location.reload();
                        } else if (response.status == false) {
                            $('.error-message').html('');
                            $('.fields-error').html(response.message);
                        } else {
                            location.reload();
                        }
                    }
                });
            });
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
