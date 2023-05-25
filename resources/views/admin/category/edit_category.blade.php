@extends('admin.layout.app')
@section('title', 'Edit Category')
@section('content')

<section id="basic-horizontal-layouts">
    <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-start mb-0">Edit Category</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="/category">Categories</a>
                                    </li>
                                    <li class="breadcrumb-item active">Edit Category
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
                                    <h4 class="card-title">Edit Category</h4>
                                </div>
                                <div class="card-body">
                                    <form class="form form-horizontal"  action="{{route('category.update',$dataSet['id'])}}" method="post">
                                        {{@csrf_field()}}
                                        @method('PUT')
                                        <input type="hidden" name="id" value="{{$dataSet['id']}}" />
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="mb-1 row">
                                                    <div class="col-sm-3">
                                                        <label class="col-form-label" for="first-name">Name</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input type="text" value="{{$dataSet['name']}}" id="name" class="form-control" name="name" placeholder="Name" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-1 row">
                                                    <div class="col-sm-3">
                                                        <label class="col-form-label" for="first-name">Status</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <div class="form-check form-check-primary form-switch">
                                                            <input type="checkbox" @if($dataSet['is_active'] == 1) checked="checked" @endif name="is_active" value="1" class="form-check-input" id="customSwitch3">
                                                        </div>
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
@endsection
