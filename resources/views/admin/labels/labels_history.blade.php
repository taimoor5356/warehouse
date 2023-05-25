@extends('admin.layout.app')
@section('title', 'Brand History')
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
<input type="hidden" id="p_id" value="{{$product_id}}" />
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-start mb-0">Brands</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="/brands">Brands</a>
                                    </li>
                                    <li class="breadcrumb-item active">Labels History
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
                            <div class="table-responsive">
                                <table class="table data-table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Customer</th>
                                            <th>Brand</th>
                                            <th>labels </th>
                                            <th>Date</th>
                                            <th>Status</th>
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
    
@endsection
@section('datatablejs')
    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>

    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
@stop

@section('page_js')
<script type="text/javascript">
    $(document).ready(function(){
        $(document).on('click', '.revert', function(){
            var history_id = $(this).data('history-id');
            $.get('/brand/history/'+history_id+'/revert', function(result){
                if(result.status == "success") {
                    $('.toast .me-auto').html('Success');
                    $('.toast .toast-header').addClass('bg-success');
                } else {
                    $('.toast .me-auto').html('Error');
                    $('.toast .toast-header').addClass('bg-danger');
                }
                $('.data-table').DataTable().ajax.reload();
                $('.toast .text-muted').html('Now');
                $('.toast .toast-body').html(result.message);
                $('#toast-btn').click();
            });
        });

        @if (session('success'))
            $('.toast .me-auto').html('Success');
            $('.toast .toast-header').addClass('bg-success');
            $('.toast .text-muted').html('Now');
            $('.toast .toast-body').html("{{session('success')}}");
            $('#toast-btn').click();
        
        @endif
    });
      $(function () {

        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ordering: true,
            stateSave: true,
            "ajax": {
            "url": "{{ route('brands/history/table') }}",
            "data": {
                "pid": $("#p_id").val(),
            }
            },
            columns: [
              {data: 'username', name: 'username'},
              {data: 'customer_name', name: 'customer_name'},
              {data: 'brand', name: 'brand'},
              {data: 'qty', name: 'qty'},
              {data: 'date', name: 'date'},
              {data: 'status', name: 'status'},
            ],
            "drawCallback": function( settings ) {
                feather.replace();
            },
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
