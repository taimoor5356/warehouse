@extends('admin.layout.app')
@section('title', 'Stock Forecast')
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
                                    <h2 class="content-header-title float-start mb-0">Stock Forecast</h2>
                                    <div class="breadcrumb-wrapper">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="/">Home</a>
                                            </li>
                                            <li class="breadcrumb-item"><a href="/stock_forecast">Stock Forecast</a>
                                            </li>
                                        </ol>
                                    </div>
                                </div>

                            {{-- <div class="col-6 addBtnClass">
                                @can('trash_view',\App\AdminModels\Inventory::class)
                                    <a href="/inventory/trash" style="float:right;"  class="btn btn-danger waves-effect waves-float waves-light"><i data-feather="trash-2"></i> View Trash</a>
                                @endcan
                                @can('create',\App\AdminModels\Inventory::class)
                                <a href="/inventory/create" style="float:right;margin-right:15px;"  class="btn btn-primary waves-effect waves-float waves-light">Add New Inventory</a>
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
                            <div class="card-header" style="display:block !important;">
                                
                               
                            </div>
                            <div class="table-responsive">
                                <table class="table data-table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Category </th>
                                            <th>Inventory Available </th>
                                            <th>Unavailable in</th>
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
                ajax: "{{ route('inventory/stock_forecast') }}",
                columns: [
                  {data: 'name', name: 'name'},
                  {data: 'category', name: 'category'},
                  {data: 'inventory_available', name: 'inventory_available'},
                  {data: 'forecast_val', name: 'forecast_val'},
                //   {data: 'action', name: 'action', orderable: false, searchable: false},
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
