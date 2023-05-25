@extends('admin.layout.app')
@section('title', 'Users')
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
                                <div class="col-9">
                                    <h2 class="content-header-title float-start mb-0">Users</h2>
                                    <div class="breadcrumb-wrapper">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="#">Home</a>
                                            </li>
                                            <li class="breadcrumb-item">Users
                                            </li>
                                            <li class="breadcrumb-item active">View Users
                                            </li>
                                        </ol>
                                    </div>
                                </div>
                                <div class="col-3 addBtnClass">
                                    @can('user_create')
                                        <a href="/user/create" style="margin-left:auto;"  class="btn btn-primary waves-effect waves-float waves-light">Add New User</a>
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
                        <div class="card-header">
                        </div>
                        <div class="table-responsive">
                            <table class="table data-table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
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
                searching: true,
                info: true,
                lengthChange: true,
                processing: true,
                paging: false,
                stateSave: true,
                ajax: "{{ route('user/admin') }}",
                columns: [
                  {data: 'name', name: 'name'},
                  {data: 'email', name: 'email', orderable: false,},
                  {data: 'role_id', name: 'role_id'},
                  {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                "drawCallback": function( settings ) {
                    feather.replace();
                },                
            });

          });

          function myFunction() {

            var r = confirm("Do you really want to delete this user?");
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
