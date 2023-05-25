@extends('admin.layout.app')
@section('title', 'Inventory')
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
                    <div class="col-4">
                        <h2 class="content-header-title float-start mb-0">Inventory</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/">Home</a>
                                </li>
                                <li class="breadcrumb-item"><a href="/inventory">Inventory</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                    <div class="col-8 addBtnClass">
                        @can('trash_view',\App\AdminModels\Inventory::class)
                            <a href="/inventory/trash" style="float:right;"  class="btn btn-danger waves-effect waves-float waves-light"><i data-feather="trash-2"></i> View Trash</a>
                        @endcan
                            @can('create',\App\AdminModels\Inventory::class)
                        <a href="/inventory/create" style="float:right;margin-right:15px;"  class="btn btn-primary waves-effect waves-float waves-light">Add New Inventory</a>
                        @endcan
                        @can('create',\App\AdminModels\Products::class)
                        <a href="/products/create" style="float:right;margin-right:15px;"  class="btn btn-primary waves-effect waves-float waves-light">Add New Product</a>
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
            <div class="card-header" style="display:block !important;">
                <div class="form-group col-sm-12 col-md-3 mt-1">
                    <label for="category">Select Category</label>
                    <select name="category" id="category_id" class="form-control select2 toggle-vis">
                        <option value="" selected>- Select Category -</option>
                        @foreach($categories as $category)
                            @isset($category)
                                <option value="{{ $category->id }}" data-column="">{{ $category->name }}</option>
                            @endisset
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table data-table table-bordered">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Product</th>
                            <th>Unit Cost</th>
                            <th>Quantity </th>
                            <th>Total Cost </th>
                            {{-- <th>Forecast </th> --}}
                            {{-- <th>Date</th> --}}
                            {{-- <th>Actions</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th><span class="current-qty"></span><span class="inventory-qty"></span></th>
                        <th><span class="current-total"></span><span class="inventory-total"></span></th>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Basic Tables end -->
@section('modal')
    @include('modals.modal')
    <!-- Basic toast -->
    <button class="btn btn-outline-primary toast-basic-toggler mt-2" id="toast-btn">ab</button>
    <div class="toast-container">
        <div class="toast basic-toast position-fixed top-0 end-0 m-2" role="alert" aria-live="" aria-atomic="true">
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
    <!-- END: Content-->
<script type="text/javascript"> 
    $(document).ready(function() {
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
        $(document).on('click', '.enter-pincode', function(){
            $('#enter_pin_Modal form input[type="password"]').focus();
            var href = $(this).attr('href');
            var type = $(this).data('type');
            $('#enter-pin-code').attr('href', href);
            $('#enter-pin-code').data('type', type);
        });
        $(document).on('click', '#enter-pin-code', function(e){
            e.preventDefault();
            var pin_code = $('#inputPinCode').val();
            var type = $(this).data('type');
            if (pin_code != '') {
                $.ajax({
                    url: '{{route("pin_code.check_pin")}}',
                    type: 'POST',
                    data: {
                        _token: '{{csrf_token()}}',
                        'pin_code': pin_code
                    },
                    success:function(reponse) {
                        if (type == 'add') {
                            if(reponse.status == 'success') {
                                // confirmAdd(e);
                                $('#enter_pin_Modal').modal('toggle');
                                $('#inventoryModal').modal('toggle');
                            } else {
                                $('#pin_error').html(reponse.msg);
                            }
                        } else if (type == 'edit') {
                            if(reponse.status == 'success') {
                                confirmEdit(e);
                            } else {
                                $('#pin_error').html(reponse.msg);
                            }
                        }
                        else if(type == 'delete') {
                            if(reponse.status == 'success') {
                                confirmDelete(e);
                            } else {
                                $('#pin_error').html(reponse.msg);
                            }
                        }
                    }
                });
            } else {
                $('#pin_error').html('Pin Required');
            }
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
    function confirmEdit(e) {
        var url = e.currentTarget.getAttribute('href');
        window.location.replace(url);
    }
    $(function () {
        calculateInventoryTotal(category_id = null);
        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            // ordering: true,
            paging: true,
            stateSave: true,
            ajax: {
                url: "{{ route('inventory/admin') }}",
                data: function(d) {
                    d.category_id = $('#category_id').val();
                }
            },
            columns: [
                {data: 'category_name', name: 'category_name', orderable: false},
                {data: 'name', name: 'name', orderable: false},
                {data: 'unit_cost', render: $.fn.dataTable.render.number( ',', '.', 2, '$' ), name: 'unit_cost', render: $.fn.dataTable.render.number( ',', '.', 2, '$' ), orderable: true},
                {data: 'pqty', render: $.fn.dataTable.render.number( ',', 2), name: 'pqty', render: $.fn.dataTable.render.number( ',', 2), orderable: true},
                {data: 'total_cost', render: $.fn.dataTable.render.number( ',', '.', 2, '$' ), name: 'total_cost', render: $.fn.dataTable.render.number( ',', '.', 2, '$' ), orderable: true},
                // {data: 'forecast_val', name: 'forecast_val'},
                // {data: 'date', name: 'date'},
                // {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            footerCallback: function(row, data, start, end, display) {
                var api = this.api();
                // Remove the formatting to get integer data for summation
                var intVal = function(i) {
                    return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i ===
                        'number' ? i : 0;
                };
                // Total over all pages
                // total = api
                //     .column(2)
                //     .data()
                //     .reduce(function(a, b) {
                //         return intVal(a) + intVal(b);
                //     }, 0);
                // // Total over this page
                // pageTotal = api
                //     .column(2, {
                //         page: 'current'
                //     })
                //     .data()
                //     .reduce(function(a, b) {
                //         return intVal(a) + intVal(b);
                //     }, 0);
                // // Update footer
                // $(api.column(2).footer()).html('$' + pageTotal.toFixed(2));
                // Total over all pages
                total = api
                    .column(3)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                // Total over this page
                pageTotal = api
                    .column(3, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                // Update footer
                // $(api.column(3).footer()).html(pageTotal);
                $('.current-qty').html(pageTotal);
                // Total over all pages
                total = api
                    .column(4)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                // Total over this page
                pageTotal = api
                    .column(4, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                // Update footer
                // $(api.column(4).footer()).html('$' + pageTotal.toFixed(2));
                $('.current-total').html('$'+pageTotal.toFixed(2));
            },
            "drawCallback": function( settings ) {
                feather.replace();
            },
            'order': [
                [4, 'desc']
            ],
        });
        $(document).on('change', '#category_id', function() {
            calculateInventoryTotal($(this).val());
            table.draw(false);
        });
    });
    function calculateInventoryTotal (category = null) {
        $.ajax({
            url: "{{ route('inventory_cost_total') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                category_id: category
            },
            success:function (response) {
                if (response.status == true) {
                    $('.inventory-qty').html('(Total: '+response.total_qty+')');
                    $('.inventory-total').html('(Total: $'+response.total_cost.toFixed(2)+')');
                }
            }
        });
    }
    function myFunction(e) {
        e.preventDefault();
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
