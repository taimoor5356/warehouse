@extends('admin.layout.app')

@section('title', 'Categories')
@section('datatablecss')

    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">

@stop

@section('datepickercss')

<link rel="stylesheet" type="text/css" href="{{ URL::asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('admin/app-assets/css/plugins/extensions/ext-component-sweet-alerts.css') }}">
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
                                    <h2 class="content-header-title float-start mb-0">Categories</h2>
                                    <div class="breadcrumb-wrapper">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="/">Home</a>
                                            </li>
                                            <li class="breadcrumb-item">Inventory
                                            </li>
                                            <li class="breadcrumb-item">Categories
                                            </li>
                                        </ol>
                                    </div>
                                </div>
                                <div class="col-6 addBtnClass">
                                    <div class="d-flex ms-auto justify-content-end">
                                        @can('product_view')
                                        <div class="" style="margin-right: 3px">
                                            <form action="{{ route('selected_categories_products') }}" method="POST" class="m">
                                                @csrf
                                                <input type="submit" class="btn btn-success categories-products" value="Categories Products">
                                                <input type="hidden" name="categories_id" class="categories-data">
                                            </form>
                                        </div>
                                        @endcan
                                        @can('category_create')
                                        <a href="/category/create" style="margin-right: 3px"
                                            class="btn btn-primary waves-effect waves-float waves-light">Add New Category</a>
                                        @endcan
                                        @can('category_delete')
                                            <a href="/category/trash"  class="btn btn-danger waves-effect waves-float waves-light">View Trash</a>
                                        @endcan
                                    </div>
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
                               
                            </div>
                            <div class="table-responsive">
                                <table class="table data-table table-bordered">
                                    <thead>
                                        <tr>
                                            <th><div class="text-center"><input type="checkbox" class="all-categories-checkbox"/></div></th>
                                            <th>Category</th>
                                            <th>Total Products</th>
                                            <th>Status</th>
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
@endsection

@section('datatablejs')
    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>

    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
@stop
@section('datepickerjs')
    
    <script src="{{ URL::asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/vendors/js/extensions/polyfill.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/js/scripts/extensions/ext-component-sweet-alerts.js') }}"></script>
    
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
        $('.categories-products').addClass('disabled');
        $(document).on('click', '.all-categories-checkbox', function () {
            if ($(this).is(':checked')) {
                $('.categories-checkbox').each(function() {
                    $(this).prop('checked', true);
                });
                $('.categories-products').removeClass('disabled');
            } else {
                $('.categories-checkbox').each(function() {
                    $(this).prop('checked', false);
                });
                $('.categories-products').addClass('disabled');
            }
            checkedCategories();
        });
        $(document).on('click', '.categories-checkbox', function() {
            if ($('.all-categories-checkbox').is(':checked')) {
                $('.all-categories-checkbox').prop('checked', false);
            }
            checkedCategories();
        });
        $(document).on('click', '.categories-products', function() {
            window.location.href="selected-categories-products";
        });
    });
    function checkedCategories() {
        var arr = [];
        var count = 0;
        $('.categories-checkbox').each(function () {
            let _this = $(this);
            if (_this.is(':checked')) {
                count += 1;
                arr.push(_this.attr('data-category-id'));
            }
        });
        if (count > 0) {
            $('.categories-products').removeClass('disabled');
        } else {
            $('.categories-products').addClass('disabled');
        }
        $('.categories-data').val(arr);
    }
    $(function () {
      var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ordering: true,
            stateSave: true,
            searching: true,
            // iDisplayLength: 100,
          ajax: "{{ route('category/admin') }}",
          columns: [
            {data: 'checkbox', name: 'checkbox', "type": "html", orderable: false},
            {data: 'name', name: 'name'},
            {data: 'total_products', name: 'total_products'},
            {data: 'is_active', name: 'is_active'},
            {data: 'action', name: 'action', searchable: false},
          ],
            'order': [
                [1, 'asc']
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

    function myFunction() {
      var r = confirm("Do you really want to delete this customer?");
      if (r == true) {
        return true;
      } else {
        return false;
      }
      document.getElementById("demo").innerHTML = txt;
    }
  </script>
@endsection