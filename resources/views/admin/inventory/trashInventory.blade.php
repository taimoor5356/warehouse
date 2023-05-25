@extends('admin.layout.app')
@section('title', 'Trashed Inventory')
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
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-start mb-0">Inventory</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="/inventory">Inventory</a>
                                    </li>
                                    <li class="breadcrumb-item active">Trash Inventory
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
                                            <th>Product</th>
                                            <th>Quantity </th>
                                            <th>Date</th>
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

    <script type="text/javascript">
          $(function () {

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                stateSave: true,
                ajax: "{{ route('inventory/trash') }}",
                columns: [
                  {data: 'name', name: 'name'},
                  {data: 'qty', name: 'qty'},
                  {data: 'date', name: 'date'},
                  {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                "drawCallback": function( settings ) {
                    feather.replace();
                },
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
        </script>
@endsection
@section('datatablejs')
    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>

    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
@stop
