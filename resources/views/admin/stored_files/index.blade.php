@extends('admin.layout.app')
@section('title', 'Files')
@section('datatablecss')

    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">

@stop
@section('content')
<style type="text/css">
    .dataTables_length{float: left;padding-left: 20px;}
    .dataTables_filter{padding-right:20px;}
    .dataTables_info{padding-left: 20px !important; padding-bottom:30px !important;}
    .dataTables_paginate{padding-right: 20px !important;}
</style>

<!-- BEGIN: Content-->
            <div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <div class="row">
                            <div class="col-6">
                            <h2 class="content-header-title float-start mb-0">Files</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    {{-- <li class="breadcrumb-item">Inventory
                                    </li> --}}
                                    <li class="breadcrumb-item">Files
                                    </li>
                                </ol>
                            </div>
                            </div>
                            <div class="col-6 addBtnClass">
                                @can('file_storage_create')
                                    <a href="#" style="margin-left:auto;" class="btn btn-primary waves-effect waves-float waves-light" data-bs-toggle="modal" data-bs-target="#uploadFile">Upload File</a>
                                    {{-- <a href="/truncate_files" style="margin-left:auto;" class="btn btn-danger waves-effect waves-float waves-light">Truncate</a> --}}
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
                            <div class="card-header">
                                <div class="row w-100">
                                    <div class="col-md-3">
                                        <label>Customers</label>
                                        <select name="customer_id" id="customers" class="form-control select2">
                                            <option value="">Select Customer</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>File Name</label>
                                        <select name="file_name" id="file_name" class="form-control select2">
                                            <option value="">Select File Name</option>
                                            {{-- @foreach($fileNames as $file)
                                                <option value="{{ $file->file_name }}">{{ $file->file_name }}</option>
                                            @endforeach --}}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table data-table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Sr No</th>
                                            <th>Customer</th>
                                            <th>File Name</th>
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
                <!-- Basic Tables end -->

    <!-- END: Content-->

@endsection

@section('modal')
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
    <div class="modal fade text-start show" id="uploadFile" tabindex="-1" aria-labelledby="myModalLabel34" style=""
        aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel34">Upload File</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('stored_files.store') }}" enctype="multipart/form-data" method="post">
                    @csrf
                    <div class="modal-body">
                        <label>Customer: </label>
                        <div class="mb-1">
                            <select name="customer_id" class="form-control select2" required>
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <label>Upload File: </label>
                        <div class="mb-1">
                            <input type="file" min="1" placeholder="Upload File" class="form-control" name="file" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary waves-effect waves-float waves-light">Submit</button>
                        <button type="button" class="btn btn-dark waves-effect waves-float waves-light" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('datatablejs')
    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>

    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
@stop

@section('page_js')
<script type="text/javascript">
    $(document).ready(function(){
        @if (session('success'))
            $('.toast .me-auto').html('Success');
            $('.toast .toast-header').addClass('bg-success');
            $('.toast .text-muted').html('Now');
            $('.toast .toast-body').html("{{session('success')}}");
            $('#toast-btn').click();
        @elseif (session('error'))
            $('.toast .me-auto').html('Error');
            $('.toast .toast-header').addClass('bg-danger');
            $('.toast .text-muted').html('Now');
            $('.toast .toast-body').html("{{session('error')}}");
            $('#toast-btn').click();
        @endif
        var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                stateSave: true,
            // "ajax": {
            //     "url": "{{ route('stored_files.index') }}",
            // },
            ajax: {
                "url": "{{ route('stored_files.index') }}",
                data: function(d) {
                    d.customer_id = $('#customers').val();
                    d.file_id = $('#file_name').val();
                }
            },
            'columns':
            [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'customer_name',
                    name: 'customer_name'
                },
                {
                    data: 'file_name',
                    name: 'file_name'
                },
                {
                    data: 'action',
                    name: 'action'
                },
            ],
                'order': [
                    [1, 'asc']
                ],
            "drawCallback": function( settings ) {
                feather.replace();
            },
        });
        // Refilter the table
        $('#customers').on('change', function() {
            table.draw(false);
            var _this = $(this);
            if (_this.val() == '') {
                $('#file_name').html("<option value=''>Select File Name</option>");
            } else {
                $.ajax({
                    url: "{{ route('get_customer_files') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        customer: _this.val()
                    },
                    success:function (result) {
                        if (result.status == true) {
                            var options = "<option value=''>Select File Name</option>";
                            result.files.forEach(element => {
                                options += "<option value='" + element.id + "'>" + element.name +
                                    "</option>"
                            });
                            $('#file_name').html(options);
                        } else {
                            $('#file_name').html("<option value=''>Select File Name</option>");
                        }
                    }
                });
            }
        });
        // Refilter the table
        $('#file_name').on('change', function() {
            table.draw(false);
        });
    });
</script>
@endsection
