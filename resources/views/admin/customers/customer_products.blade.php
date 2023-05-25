@extends('admin.layout.app')
@section('title', 'Customer Products')
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
                        <h2 class="content-header-title float-start mb-0">Customer Products</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="/customers">View Customers</a>
                                    </li>
                                    <li class="breadcrumb-item">Customer Products</li>
                                </ol>
                            </div>
                        </div>
                        {{-- <div class="col-6 addBtnClass">
                            @can('trash_view',\App\AdminModels\Labels::class)
                                <a href="/customer/{{request()->route()->id}}/brands/trash" style="float:right;"  class="btn btn-danger waves-effect waves-float waves-light"><i data-feather="trash-2"></i> View Trash</a>
                            @endcan
                            @can('create',\App\AdminModels\Labels::class)
                                <a href="/customer/{{request()->route()->id}}/brand/create" style="float:right;margin-right:15px;"  class="btn btn-primary waves-effect waves-float waves-light">Add New Brand</a>
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
                <div class="card-header"  style="display:block !important;">
                </div>
                <div class="table-responsive">
                    <table class="table data-table table-bordered">
                        <thead>
                            <tr>
                                <th>Customer Name</th>
                                <th>Product Name</th>
                                <th>Purchasing</th>
                                <th>Weight</th>
                                <th>Selling Cost</th>
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
@endsection
@section('modal')
    {{-- Add Inventory Modal --}}
    <div class="modal fade text-start show" id="labelsModal" tabindex="-1" aria-labelledby="myModalLabel33" style="" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Add Labels</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body">
                        
                        <label>Labels: </label>
                        <div class="mb-1">
                            <input type="number" min="1" placeholder="" name="labels" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary waves-effect waves-float waves-light" id="add-labels" data-bs-dismiss="modal">Add</button>
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
            $('.toast .me-auto').html('Success');
            $('.toast .toast-header').addClass('bg-danger');
            $('.toast .text-muted').html('Now');
            $('.toast .toast-body').html("{{session('error')}}");
            $('#toast-btn').click();
        @endif
        $(document).on('click', '.add-labels', function(e){
            $("#add-labels").data('brand-id', $(this).data('brand-id'));
            $('#labelsModal form input[type="number"]').focus();
        });
        $(document).on('click', '#add-labels', function(e){
            e.preventDefault();
            var brand_id = $(this).data('brand-id');
            var url = '/brand/' + brand_id + '/labels';
            var labels = $('#labelsModal form input[type="number"]').val();
            $.post(url, {labels: labels, _token: '{{csrf_token()}}'}, function(result){
                if(result.status == "success") {
                    $('.toast .me-auto').html('Success');
                    $('.toast .toast-header').addClass('bg-success');
                } else {
                    $('.toast .me-auto').html('Error');
                    $('.toast .toast-header').addClass('bg-danger');
                }
                $('.data-table').DataTable().draw(false);
                $('.toast .text-muted').html('Now');
                $('.toast .toast-body').html(result.message);
                $('#toast-btn').click();
                $('#labelsModal form input[type="number"]').val('');
            });
        });
        var url = `{!! route('show_all', $id) !!}`;
        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ordering: true,
            stateSave: true,
                // iDisplayLength: 100,
            ajax: url,
            columns: [
                {data: 'c_name', name: 'c_name'},
                {data: 'product_name', name: 'product_name'},
                {data: 'purchasing_cost', name: 'purchasing_cost'},
                {data: 'weight', name: 'weight'},
                {data: 'selling_cost', name: 'selling_cost'},
                // {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            "drawCallback": function( settings ) {
                feather.replace();
            },
            'order': [
                [1, 'asc']
            ],
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
        }).then(function (result) {
            if (result.value) {
                window.location.replace(url);
            }
        });
    }
  </script>
@endsection

