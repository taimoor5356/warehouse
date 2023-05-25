@extends('admin.layout.app')
@section('title', 'Add Customer Brand')
@section('datepickercss')
<link rel="stylesheet" type="text/css" href="{{ URL::asset('admin/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
@stop

@section('content')
<style type="text/css">
    .bootstrap-touchspin.input-group-lg {
    width: 13.375rem !important;
}
</style>
<section id="basic-horizontal-layouts">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-start mb-0">Add Customer Brand</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Home</a>
                            </li>
                            <li class="breadcrumb-item active">Customers
                            </li>
                            <li class="breadcrumb-item"><a href="/customers">Manage Customers</a>
                            </li>
                            <li class="breadcrumb-item active">Add Customer Brand
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Add Brand</h4>
                </div>
                <div class="card-body">
                    <form class="form form-horizontal" enctype='multipart/form-data' action="/customer/{{$customer->id}}/brand/store" method="post">
                        {{@csrf_field()}}
                        <div class="row">
                            <input type="hidden" id="customer_id" value="{{ $customer->id }}">
                            <div class="col-12">
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="catselect">Select Customer</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" value="{{$customer->customer_name}}" class="form-control" readonly>
                                        @error('customer_id')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="catselect">Enter Brand Name</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="brand" value=""  name="brand" maxlength="50" required />
                                        @error('brand')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="catselect">Enter Mailer Charges</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <div class="input-icon">
                                            <input type="number" min="0.0" step="0.01" id="mailer_cost" class="form-control" value="0.00" name="mailer_cost"/>
                                            <i>$</i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            {{-- <div class="mb-1 row"> --}}
                                
                                <div class="col-md-3">
                                    <label for="col-form-label">Brands</label>
                                </div>
                                <div class="col-md-9">
                                    <table class="table data_table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width: 50%">Brand</th>
                                                <th style="width: 15%">Mailer Charges</th>
                                                <th style="width: 20%">Date</th>
                                                <th style="width: 10%" class="">More</th>
                                            </tr>
                                        </thead>
                                        <tbody id="products_table">
                                            
                                        </tbody>
                                    </table>
                                    <br>
                                </div>
                            {{-- </div> --}}
                            <hr>
                            <div class="col-sm-9 offset-sm-3">
                                <button type="submit" class="btn btn-primary me-1">Submit</button>
                                <button type="reset" class="btn btn-outline-secondary">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

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
    $(document).ready(function(){
        // window.cust_brands_table.destroy();
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
        $('.touchspin2').TouchSpin({
            min: 0,
            max: 100000000,
            step: 1,
        });
        $(function () {
            var custId = $('#customer_id').val();
            var url = "{{ route('customer-labels', ':id') }}";
            url = url.replace(':id', custId);
            var table = $('.data_table').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                bFilter: false,
                paging: false,
                bDestroy: true,
                stateSave: true,
                // ajax: "{{ route('label/admin') }}",
                ajax: {
                    url: url,
                    // data: function (d) {
                    //     if ($('#customer :selected').text() == 'All') {
                    //         customername = ''
                    //     } else {
                    //         customername = $('#customer :selected').text();
                    //     }
                    //     if ($('#brand :selected').text() == 'All') {
                    //         brandname = ''
                    //     } else {
                    //         brandname = $('#brand :selected').text();
                    //     }
                    //     d.customer = customername;
                    //     d.brand = brandname;
                    // }
                },
                columns: [
                    // {'className': 'details-control', 'orderable': false, 'data': null, 'defaultContent': ''},
                    {data: 'brand', name: 'brand'},
                    {data: 'mailer_cost', name: 'mailer_cost'},
                    // {data: 'customer_name', name: 'customer_name'},
                    {data: 'date', name: 'date'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                "drawCallback": function( settings ) {
                    feather.replace();
                },
            });
        });
    });
</script>
@endsection
@section('datepickerjs')
    <script src="{{ URL::asset('admin/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/js/scripts/forms/pickers/form-pickers.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/responsive.bootstrap4.min.js') }}"></script>

    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>

    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
    
@stop

