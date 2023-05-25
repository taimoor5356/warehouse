@extends('admin.layout.app')
@section('title', 'Near to Empty')
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
                                    <h2 class="content-header-title float-start mb-0">Near to Empty</h2>
                                    <div class="breadcrumb-wrapper">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="/">Home</a>
                                            </li>
                                            <li class="breadcrumb-item">Inventory
                                            </li>
                                            <li class="breadcrumb-item active">Near to Empty
                                            </li>
                                        </ol>
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
                            <div class="card-header" style="display:block !important;">
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-2">
                                        <label for="category">Categories</label>
                                        <select name="category" id="category" class="select2 form-select arrow">
                                            <option value="">All</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ ucwords($category->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-2">
                                        <label for="product">Product</label>
                                        <select name="product" id="product" class="select2 form-select arrow">
                                            <option value="">Select Product</option>
                                            {{-- @foreach ($categories as $product)
                                                <option value="{{ $product->id }}">{{ ucwords($product->name) }}</option>
                                            @endforeach --}}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table data-table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Category </th>
                                            <th>Product</th>
                                            <th>Inventory Available </th>
                                            <th>Ordered </th>
                                            <th>OTW</th>
                                            <th class="text-center">Forecast Statuses</th>
                                            <th class="text-center">Empty in</th>
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
                // ordering: true,
                stateSave: true,
                ajax: {
                    url: "{{ route('near_to_empty') }}",
                    data: function (d) {
                        d.category = $('#category').val();
                        d.product = $('#product').val();
                    }
                },
                columns: [
                  {data: 'category', name: 'category', orderable: false},
                  {data: 'name', name: 'name', orderable: false},
                  {data: 'inventory_available', name: 'inventory_available', orderable: true},
                  {data: 'upcoming', name: 'upcoming', orderable: true},
                  {data: 'otw', name: 'otw', orderable: true},
                  {data: 'forecast_statuses', name: 'forecast_statuses', orderable: false},
                  {data: 'forecast_val', name: 'forecast_val', orderable: true},
                //   {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                'order': [
                    [6, 'asc']
                ],
                "drawCallback": function( settings ) {
                    feather.replace();
                },
            });
            window.table = table;
          });
          $(document).ready(function() {
            $(document).on('change', '#category', function() {
                var id = $(this).val();
                if(id=="") {
                    $('#product').html('<option value="">Select Product</option>');
                } else {
                    $.get('/category/'+id+'/products', function(result) {
                        if(result.status == "success") {
                            var options = "<option value=''>Select Product</option>";
                            result.data.forEach(element => {
                                options += "<option value='"+element.id+"'>"+element.name+"</option>"
                            });
                            $('#product').html(options);
                        } else {
                            $('#product').html("<option value=''>Select Product</option>");
                        }
                    });
                }
            });
            $(document).on('change', '#category, #product', function() {
                window.table.draw(false);
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
