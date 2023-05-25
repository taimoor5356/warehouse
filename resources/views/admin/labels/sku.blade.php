@extends('admin.layout.app')
@section('title', 'Brand SKUs')
@section('datatablecss')

    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">

@stop

@section('content')

<!-- BEGIN: Content-->
            <div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-8">
                                    <h2 class="content-header-title float-start mb-0">Brand SKUs</h2>
                                    <div class="breadcrumb-wrapper">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="/">Home</a>
                                            </li>
                                            <li class="breadcrumb-item">Customers
                                            </li>
                                            <li class="breadcrumb-item"><a href="/brands">Manage Brands</a>
                                            </li>
                                            <li class="breadcrumb-item">Brand SKUs
                                            </li>
                                        </ol>
                                    </div>
                                </div>
                                <div class="col-4 addBtnClass">
                                    @can('create',\App\Models\Sku::class)
                                    <a href="/brand/{{$brand->id}}/sku/create" style="margin-left:auto;"  class="btn btn-primary waves-effect waves-float waves-light">Add New SKU</a>
                                    @endcan
                                    <a href="/brand/{{$brand->id}}/sku/trash" style="margin-left:auto;"  class="btn btn-danger waves-effect waves-float waves-light">View Trash</a>
                            
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
                            <div class="table-responsive p-1">
                                <table class="table data-table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>SKU ID </th>
                                            <th>Customer</th>
                                            <th>Brand</th>
                                            <th>SKU</th>
                                            {{--  <th>Product</th>  --}}
                                            {{--  <th>Quantity</th>  --}}
                                            <th>Cost</th>
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
    {{-- Add Inventory Modal --}}
    <div class="modal fade text-start show" id="inventoryModal" tabindex="-1" aria-labelledby="myModalLabel33" style="" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Add Inventory</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body">
                        
                        <label>Inventory: </label>
                        <div class="mb-1">
                            <input type="number" min="1" placeholder="Inventory" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary waves-effect waves-float waves-light" id="add-inventory" data-bs-dismiss="modal">Add</button>
                        <button type="button" class="btn btn-dark waves-effect waves-float waves-light" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/responsive.bootstrap4.min.js') }}"></script>

    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>

    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
@stop

@section('page_js')
<script type="text/javascript">
    $(document).ready(function(){
      var table = $('.data-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    stateSave: true,
                    bDestroy: true,
          "ajax": {
          "url": "/brand/{{$brand->id}}/sku",
          },
          columns: [
              {data: 'sku_id', name: 'sku_id'},
              {data: 'customer', name: 'customer'},
              {data: 'brand', name: 'brand'},
              {data: 'name', name: 'name'},
              // {data: 'product', name: 'product',orderable: false, searchable: false},
              // {data: 'quantity', name: 'quantity', orderable: false, searchable: false},
              {data: 'cost', name: 'cost'},
              {data: 'action', name: 'action', orderable: false, searchable: false},
          ],
          "drawCallback": function( settings ) {
              feather.replace();
          },
      });
      $(document).on('click', '#delete_sku', function() {
          var id = $(this).data('id');
          $.post('/sku/'+id, {_method: "DELETE", _token:"{{csrf_token()}}"}, function(result) {
              if(result.status == "success") {
                  table.draw(false);
              } else {

              }
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


