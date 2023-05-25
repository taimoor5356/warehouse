@extends('admin.layout.app')
@section('title', 'Order Details')
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
                            <h2 class="content-header-title float-start mb-0">Product</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="/inventory">Product</a>
                                    </li>
                                    <li class="breadcrumb-item active">Order Details
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
                                            <th>Quantity </th>
                                            <th>Date</th>
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
        $(document).ready(function(){
            
        //   $(function () {
            var id = $('#p_id').val();
            var url = "{{ route('product/view_order_details', ':id') }}";
            url = url.replace(':id', id);
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                stateSave: true,
                "ajax": {
                "url": url,
                "data": {
                    _token: '{{csrf_token()}}',
                    'id': id
                }
                },
                columns: [
                  {data: 'get_qty', name: 'get_qty'},
                  {data: 'date', name: 'date'},
                ],
                "drawCallback": function( settings ) {
                    feather.replace();
                },
            });

        //   });
            $(document).on('click', '.revert', function(){
                var history_id = $(this).data('history-id');
                $.get('/product/product_history/'+history_id+'/revert', function(result){
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
        });
        // //   $(function () {

        //     var table = $('.data-table').DataTable({
        //         processing: true,
        //         serverSide: true,
        //         ordering: true,
        //         "ajax": {
        //         "url": "{{ route('product/view_order_details') }}",
        //         "data": {
        //             _token: '{{csrf_token()}}',
        //             'id': $('#pid').val()
        //         }
        //         },
        //         columns: [
        //           {data: 'get_qty', name: 'get_qty'},
        //           {data: 'date', name: 'date'},
        //         ],
        //         "drawCallback": function( settings ) {
        //             feather.replace();
        //         },
        //     });

        // //   });

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
