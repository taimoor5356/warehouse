@extends('admin.layout.app')
@section('title', 'Edit Unit')
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
                            <h2 class="content-header-title float-start mb-0">Units</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item">Inventory
                                    </li>
                                    <li class="breadcrumb-item"><a href="/units">Units</a>
                                    </li>
                                    <li class="breadcrumb-item active">Edit Unit
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
                                    <h4 class="card-title">Edit Unit</h4>
                                </div>
                                <div class="card-body">
                                    <form class="form form-horizontal" enctype='multipart/form-data' action="{{route('units.update',$dataSet['id'])}}" method="post">
                                        {{@csrf_field()}}
                                       @method('PUT')
                                        <div class="row">
                                          
                                            <div class="col-12">
                                                <div class="mb-1 row">
                                                    <div class="col-sm-3">
                                                        <label class="col-form-label" for="first-name">Unit Name</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input type="text" id="fp-default" class="form-control" name="name" value="{{$dataSet->name}}" />
                                                        @error('name')
                                                              <p class="text-danger">{{ $message }}</p>
                                                          @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-1 row">
                                                    <div class="col-sm-3">
                                                        <label class="col-form-label" for="first-name">Type Lbs</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <div class="form-check form-check-primary form-switch">
                                                            <input type="checkbox" name="unit_type" @if ($dataSet->unit_type == 1) checked 
                                                            @endif  value="1" class="form-check-input">
                                                        </div>
                                                        @error('unit_type')
                                                              <p class="text-danger">{{ $message }}</p>
                                                          @enderror
                                                    </div>
                                                </div>
                                            </div>
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
                <script type="text/javascript">
                    $(document).ready(function(){
                        $('.touchspin2').TouchSpin({
                        min: 0,
                        max: 100000000,
                        step: 1,
                    });
                    });
                </script>
@endsection
@section('datepickerjs')
    <script src="{{ URL::asset('admin/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/js/scripts/forms/pickers/form-pickers.js') }}"></script>
    
@stop

