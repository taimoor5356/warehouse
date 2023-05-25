@extends('admin.layout.app')
@section('title', 'Manage Skus')
@section('datatablecss')

    <link rel="stylesheet" type="text/css"
        href="{{ asset('admin/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">

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

        td.details-control {
            background: url('https://datatables.net/examples/resources/details_open.png') no-repeat center center;
            cursor: pointer;
        }

        tr.shown td.details-control {
            background: url('https://datatables.net/examples/resources/details_close.png') no-repeat center center;
        }

        input[type="checkbox"] {
            background-color: #fff;
        }

    </style>
    <!-- BEGIN: Content-->
    <div class="content-header row">
        <div class="content-header-left col-md-12 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <div class="row">
                        <div class="col-6">
                            <h2 class="content-header-title float-start mb-0">Manage Skus</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item">Customers
                                    </li>
                                    <li class="breadcrumb-item">Manage Skus
                                    </li>
                                </ol>
                            </div>
                        </div>
                        <div class="col-6 addBtnClass">
                            @can('sku_delete')
                                <a href="/sku/trash" style="float:right;"
                                    class="btn btn-danger waves-effect waves-float waves-light"><i data-feather="trash-2"></i>
                                    View Trash</a>
                            @endcan
                            @can('sku_create')
                                <a href="/sku/create" style="float:right;margin-right:15px;"
                                    class="btn btn-primary waves-effect waves-float waves-light">Add New SKU</a>
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
                <div class="card-header text-right" style="display:block !important;">
                    <div class="row d-flex flex-row-reverse">
                        {{-- <div class="form-group col-sm-12 col-md-2" id="search">
                                    <label for="search"></label>
                                    <input type="submit" class="form-control btn btn-primary" value="Filter">
                                </div> --}}
                        <div class="form-group col-sm-12 col-md-2">
                            <label for="brand">Select Brand</label>
                            <div class="invoice-customer">
                                <select name="brand" id="brand" class="form-select select2" required>
                                    <option value="">All</option>
                                    {{-- @foreach ($brands as $brand)
                                                <option value="{{$brand->id}}">{{$brand->brand}}</option>
                                            @endforeach --}}
                                </select>
                                @error('brand')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                                <div id="brand-error" class="text-danger font-weight-bold"></div>
                            </div>
                        </div>
                        <div class="form-group col-sm-12 col-md-2">
                            <label for="customer">Select Customer</label>
                            <select name="customer" id="customer" class="form-control select2">
                                <option value="all">All</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>
                <div class="table-responsive">
                    <table class="table data-table table-bordered">
                        <thead>
                            <tr>
                                <th><img class="show_all_btn" src="https://datatables.net/examples/resources/details_open.png" alt=""></th>
                                <th>SKU ID</th>
                                <th>SKU</th>
                                <th>CUSTOMER</th>
                                <th>BRAND </th>
                                <th>SELL PRICE</th>
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
<script type="text/javascript">
    $(document).ready(function() {
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
            $('.toast .toast-body').html("Something went wrong");
            $('#toast-btn').click();
        @endif
        if (localStorage.getItem('customer_id') != '') {
            var customer = $('#customer');
            customer.find('option[value="'+localStorage.getItem('customer_id')+'"]').attr('selected', 'selected').change();
            if (localStorage.getItem('brand_id') != '') {
                var brand = $('#brand');
                setTimeout(function(){
                    getCustomerBrands(customer.val(), brand);
                }, 500);
            }
        }
        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            // ordering: true,
            stateSave: true,
            bDestroy: true,
            ajax: {
            url: "{{ route('sku.index') }}",
                data: function (d) {
                    if ($('#customer :selected').text() == 'All') {
                        customername = ''
                    } else {
                        customername = $('#customer :selected').text();
                    }
                    if ($('#brand :selected').text() == 'All') {
                        brandname = ''
                    } else {
                        brandname = $('#brand :selected').text();
                    }
                    d.customer = customername;
                    d.brand = brandname;
                }
            },
            columns: [
                {data: 'btn', name: 'btn', orderable: false},
                {data: 'sku_id', name: 'sku_id', orderable: true},
                {data: 'name', name: 'name', orderable: false},
                {data: 'customer', name: 'customer', orderable: false},
                {data: 'brand', name: 'brand', orderable: false},
                {data: 'cost', name: 'cost', orderable: true},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            "drawCallback": function( settings ) {
                feather.replace();
            },
            'order': [[5, 'desc']],
        });
        window.data_table = table;
        // Add event listener for opening and closing details
        $(document).on('click', '.details-control', function () {
            var tr = $(this).closest('tr');
            var row = table.row( tr );
    
            if ( row.child.isShown() ) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
                $(this).attr('src', 'https://datatables.net/examples/resources/details_open.png');
            }
            else {
                // Open this row
                row.child( format(row.data()) ).show();
                tr.addClass('shown');
                $(this).attr('src', 'https://datatables.net/examples/resources/details_close.png');
            }
        } );
        // Refilter the table
        $('#customer, #brand').on('change', function () {
            table.draw(false);
        });
        // get customer brand
        $(document).on('change', '#customer', function() {
            $('.customer-error').html('');
            var id = $(this).val();
            if(id=="all") {
                $.get('/getAllBrands', function(result) {
                    if(result.status == "success") {
                        var options = "<option value=''>All</option>";
                        result.data.forEach(element => {
                            options += "<option value='"+element.id+"'>"+element.brand+"</option>"
                        });
                        
                        $('#brand-error').html('');
                        $('#brand').html(options);
                    } else {
                        $('#brand').html("<option value=''>All</option>");
                        $('#brand-error').html(result.message);
                    }
                });
            } else {
                $.get('/customer/'+id+'/brand', function(result) {
                    if(result.status == "success") {
                        var options = "<option value=''>All</option>";
                        result.data.forEach(element => {
                            options += "<option value='"+element.id+"'>"+element.brand+"</option>"
                        });
                        
                        $('#brand-error').html('');
                        $('#brand').html(options);
                    } else {
                        $('#brand').html("<option value=''>All</option>");
                        $('#brand-error').html(result.message);
                    }
                });
            }
            localStorage.setItem('customer_id', '');
            localStorage.setItem('customer_id', $('#customer').val());
            var date = new Date().getTime();
            localStorage.setItem('data_time', date);
        });
        $(document).on('change', '#brand', function () {
            localStorage.setItem('brand_id', '');
            localStorage.setItem('brand_id', $('#brand').val());
            var date = new Date().getTime();
            localStorage.setItem('data_time', date);
        });
    });
    /* Formatting function for row details - modify as you need */
    function format ( d ) {
        // `d` is the original data object for the row
        let html = '';
        html += `
            <span style="font-weight: bold;">SKU Products</span>
            <table class="table table-bordered w-100 mt-1">
                <thead>`;
        html += `
                    <th class="p-1 text-center" style="padding:5px">Product</th>
                    <th class="p-1 text-center" style="padding:5px">Qty</th>
                    <th class="p-1 text-center" style="padding:5px">Labels</th>
                    <th class="p-1 text-center" style="padding:5px">Purchasing Cost</th>
                    <th class="p-1 text-center" style="padding:5px">Weight</th>
                    <th class="p-1 text-center" style="padding:5px">Service Charges</th>
                    <th class="p-1 text-center" style="padding:5px">Selling Cost</th>
                </thead>
                <tbody>`;
        $.ajax({
            async: false,
            url: "{{ route('get_sku_products_and_labels') }}",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                sku_id: d.id,
                customer_id: d.customer_id,
                brand_id: d.brand_id
            },
            success:function(response) {
                if (response.status == true) {
                    response.data.forEach(element => {
                        html += `
                            <tr>
                                <td class="p-1 text-center" style="padding:5px">`+element.product_name+`</td>
                                <td class="p-1 text-center" style="padding:5px">`+element.quantity+`</td>
                                <td class="p-1 text-center" style="padding:5px">`+element.label_qty+`</td>
                                <td class="p-1 text-center" style="padding:5px">`+element.purchasing_cost+`</td>
                                <td class="p-1 text-center" style="padding:5px">`+element.weight+`</td>
                                <td class="p-1 text-center" style="padding:5px">
                                `;
                                if (element.is_active == 0) {
                                    html += `
                                        <img src="{{ asset('images/checked.png') }}" width="10px" height="10px"> Label<br>
                                    `;
                                }
                                if (element.pick > 0) {
                                    html += `
                                        <img src="{{ asset('images/checked.png') }}" width="10px" height="10px"> Pick<br>
                                    `;
                                }
                                if (element.pack > 0) {
                                    html += `
                                        <img src="{{ asset('images/checked.png') }}" width="10px" height="10px"> Pack<br>
                                    `;
                                }
                        html += `<td class="p-1 text-center" style="padding:5px">`+element.selling_cost+`</td>
                            </tr>
                        `;
                    });
                } else {
                    $('.toast .me-auto').html('Error');
                    $('.toast .toast-header').addClass('bg-danger');
                    $('.toast .text-muted').html('Now');
                    $('.toast .toast-body').html("Something went wrong");
                    $('#toast-btn').click();
                }
                return html;
            }
        });
        html += `
        </tbody>
        </table>
        `;
        return html;
    }
    function confirmDelete(e) {
        var url = e.currentTarget.getAttribute('href');
        var sku_id = e.currentTarget.getAttribute('data-id');
        e.preventDefault();
        Swal.fire({
            title: 'Alert?',
            text: "You want to delete?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-outline-secondary ms-1'
            },
            buttonsStyling: false
        }).then(function(result) {
            if (result.value) {
                $.get('/sku/'+sku_id+'/delete', function(res) {
                    if (res.status == 'success') {
                        $('.toast .me-auto').html('Success');
                        $('.toast .toast-header').addClass('bg-success');
                        $('.toast .text-muted').html('Now');
                        $('.toast .toast-body').html("Deleted Successfully");
                        $('#toast-btn').click();
                        window.data_table.draw(false);
                    }
                });
            }
        });
    }
    function getCustomerBrands(id, brand)
    {
        $.get('/customer/'+id+'/brand', function(result) {
            if(result.status == "success") {
                var options = "<option value=''>All</option>";
                result.data.forEach(element => {
                    options += "<option value='"+element.id+"'>"+element.brand+"</option>"
                });
                
                $('#brand-error').html('');
                $('#brand').html(options);
                brand.find('option[value="'+localStorage.getItem('brand_id')+'"]').attr('selected', 'selected').change();
            } else {
                $('#brand').html("<option value=''>All</option>");
                $('#brand-error').html(result.message);
            }
        });
    }
</script>
@endsection
@section('datatablejs')
<script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
@stop
