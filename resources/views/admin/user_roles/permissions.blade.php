@extends('admin.layout.app')
@section('title', 'Role Permissions')
@section('content')

    <style>
        ul,
        #myUL {
            list-style-type: none;
        }

        #myUL {
            margin: 0;
            padding: 0;
        }

        .caret {
            cursor: pointer;
            -webkit-user-select: none;
            /* Safari 3.1+ */
            -moz-user-select: none;
            /* Firefox 2+ */
            -ms-user-select: none;
            /* IE 10+ */
            user-select: none;
        }

        .caret::before {
            content: "\25B6";
            color: black;
            display: inline-block;
            margin-right: 6px;
        }

        .caret-down::before {
            -ms-transform: rotate(90deg);
            /* IE 9 */
            -webkit-transform: rotate(90deg);
            /* Safari */
            '
    transform: rotate(90deg);
        }

        .nested {
            display: none;
        }

        .active {
            display: block;
        }

    </style>
    <section id="basic-horizontal-layouts">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Role Permissions</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/">Home</a>
                                </li>
                                <li class="breadcrumb-item">Users
                                </li>
                                <li class="breadcrumb-item"><a href="/userroles">Role Permissions</a>
                                </li>
                                <li class="breadcrumb-item active">Assign Permissions
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
                        <h5 class="card-title">Assign permissions to
                            {{ ucwords(Spatie\Permission\Models\Role::find($role_id)->name) }}</h5>
                    </div>
                    <div class="card-body">
                        <form class="form form-horizontal" action="{{ url('/assign_permissions') }}" method="post">
                            {{ @csrf_field() }}
                            <div class="row">
                                <input type="hidden" name="role_id" value="{{ $role_id }}" />
                                <div class="table-responsive">
                                    <ul id="myUL">
                                        @foreach ($headings as $key => $heading)
                                            <hr>
                                            <li><span class="caret"
                                                    style="font-size: 1.5rem">{{ $key }}</span>
                                                <ul class="nested">
                                                    @foreach ($heading as $key => $head)
                                                        <hr>
                                                        <li><span class="caret"
                                                                style="font-size: 1.5rem">{{ $head }}</span>
                                                            <ul class="nested">
                                                                @foreach ($permissions as $permission)
                                                                    @php
                                                                        $slugName = explode('_', $permission->name);
                                                                    @endphp
                                                                    @if ($slugName[0] == 'customer' && $key == 'customers')
                                                                        <hr>
                                                                        <li style="font-size: 1.2rem"><input
                                                                                @if (in_array($permission->id, $assignedPermisions)) checked @endif
                                                                                type="checkbox" name="permissions[]"
                                                                                value="{{ $permission->id }}" />
                                                                            {{ $permission->slug }}</li>
                                                                    @elseif ($slugName[0] == 'labels' && $key == 'labels')
                                                                    @if($permission->slug == 'Labels Restore' || $permission->slug == 'Labels Trash View' || $permission->slug == 'Labels History')
                                                                    @else
                                                                        <hr>
                                                                        <li style="font-size: 1.2rem"><input
                                                                                @if (in_array($permission->id, $assignedPermisions)) checked @endif
                                                                                type="checkbox" name="permissions[]"
                                                                                value="{{ $permission->id }}" />
                                                                            {{ $permission->slug }}</li>
                                                                    @endif
                                                                    @elseif ($slugName[0] == 'sku' && $key == 'skus')
                                                                        <hr>
                                                                        <li style="font-size: 1.2rem"><input
                                                                                @if (in_array($permission->id, $assignedPermisions)) checked @endif
                                                                                type="checkbox" name="permissions[]"
                                                                                value="{{ $permission->id }}" />
                                                                            {{ $permission->slug }}</li>
                                                                    @elseif ($slugName[0] == 'file' && $key == 'files')
                                                                        <hr>
                                                                        <li style="font-size: 1.2rem"><input
                                                                                @if (in_array($permission->id, $assignedPermisions)) checked @endif
                                                                                type="checkbox" name="permissions[]"
                                                                                value="{{ $permission->id }}" />
                                                                            {{ $permission->slug }}</li>
                                                                    @elseif ($slugName[0] == 'inventory' && $key == 'inventory')
                                                                    @if ($permission->slug == 'Inventory Trash View' || $permission->slug == 'Inventory Restore' || $permission->slug == 'Inventory History' || $permission->slug == 'Inventory Upcoming' || $permission->slug == 'Inventory Otw')
                                                                    @else
                                                                        <hr>
                                                                        <li style="font-size: 1.2rem"><input
                                                                                @if (in_array($permission->id, $assignedPermisions)) checked @endif
                                                                                type="checkbox" name="permissions[]"
                                                                                value="{{ $permission->id }}" />
                                                                            {{ $permission->slug }}</li>
                                                                    @endif
                                                                    @elseif ($slugName[0] == 'product' && $key == 'products')
                                                                        <hr>
                                                                        <li style="font-size: 1.2rem"><input
                                                                                @if (in_array($permission->id, $assignedPermisions)) checked @endif
                                                                                type="checkbox" name="permissions[]"
                                                                                value="{{ $permission->id }}" />
                                                                            {{ $permission->slug }}</li>
                                                                    @elseif ($slugName[0] == 'category' && $key == 'categories')
                                                                        <hr>
                                                                        <li style="font-size: 1.2rem"><input
                                                                                @if (in_array($permission->id, $assignedPermisions)) checked @endif
                                                                                type="checkbox" name="permissions[]"
                                                                                value="{{ $permission->id }}" />
                                                                            {{ $permission->slug }}</li>
                                                                    @elseif ($slugName[0] == 'unit' && $key == 'units')
                                                                        <hr>
                                                                        <li style="font-size: 1.2rem"><input
                                                                                @if (in_array($permission->id, $assignedPermisions)) checked @endif
                                                                                type="checkbox" name="permissions[]"
                                                                                value="{{ $permission->id }}" />
                                                                            {{ $permission->slug }}</li>
                                                                    @elseif ($slugName[0] == 'upcoming' && $key == 'upcoming')
                                                                    @if($permission->slug == 'Upcoming Inventory Move Otw')
                                                                    @else
                                                                        <hr>
                                                                        <li style="font-size: 1.2rem"><input
                                                                                @if (in_array($permission->id, $assignedPermisions)) checked @endif
                                                                                type="checkbox" name="permissions[]"
                                                                                value="{{ $permission->id }}" />
                                                                            {{ $permission->slug }}</li>
                                                                    @endif
                                                                    @elseif ($slugName[0] == 'otw' && $key == 'otw')
                                                                    @if($permission->slug == 'Otw Inventory Move Stock')
                                                                    @else
                                                                        <hr>
                                                                        <li style="font-size: 1.2rem"><input
                                                                                @if (in_array($permission->id, $assignedPermisions)) checked @endif
                                                                                type="checkbox" name="permissions[]"
                                                                                value="{{ $permission->id }}" />
                                                                            {{ $permission->slug }}</li>
                                                                    @endif
                                                                    @elseif ($slugName[0] == 'order' && $key == 'counts')
                                                                        <hr>
                                                                        <li style="font-size: 1.2rem"><input
                                                                                @if (in_array($permission->id, $assignedPermisions)) checked @endif
                                                                                type="checkbox" name="permissions[]"
                                                                                value="{{ $permission->id }}" />
                                                                            {{ $permission->slug }}</li>
                                                                    @elseif ($slugName[0] == 'return' && $key == 'returned_order')
                                                                        <hr>
                                                                        <li style="font-size: 1.2rem"><input
                                                                                @if (in_array($permission->id, $assignedPermisions)) checked @endif
                                                                                type="checkbox" name="permissions[]"
                                                                                value="{{ $permission->id }}" />
                                                                            {{ $permission->slug }}</li>
                                                                    @elseif ($slugName[0] == 'invoices' && $key == 'invoices')
                                                                        <hr>
                                                                        <li style="font-size: 1.2rem"><input
                                                                                @if (in_array($permission->id, $assignedPermisions)) checked @endif
                                                                                type="checkbox" name="permissions[]"
                                                                                value="{{ $permission->id }}" />
                                                                            {{ $permission->slug }}</li>
                                                                    @elseif ($slugName[0] == 'report' && $key == 'reports')
                                                                        <hr>
                                                                        <li style="font-size: 1.2rem"><input
                                                                                @if (in_array($permission->id, $assignedPermisions)) checked @endif
                                                                                type="checkbox" name="permissions[]"
                                                                                value="{{ $permission->id }}" />
                                                                            {{ $permission->slug }}</li>
                                                                    @elseif ($slugName[0] == 'userrole' && $key == 'roles')
                                                                        <hr>
                                                                        <li style="font-size: 1.2rem"><input
                                                                                @if (in_array($permission->id, $assignedPermisions)) checked @endif
                                                                                type="checkbox" name="permissions[]"
                                                                                value="{{ $permission->id }}" />
                                                                            {{ $permission->slug }}</li>
                                                                    @elseif ($slugName[0] == 'user' && $key == 'users')
                                                                        <hr>
                                                                        <li style="font-size: 1.2rem"><input
                                                                                @if (in_array($permission->id, $assignedPermisions)) checked @endif
                                                                                type="checkbox" name="permissions[]"
                                                                                value="{{ $permission->id }}" />
                                                                            {{ $permission->slug }}</li>
                                                                    @elseif ($slugName[0] == 'setting' && $key == 'settings')
                                                                        <hr>
                                                                        <li style="font-size: 1.2rem"><input
                                                                                @if (in_array($permission->id, $assignedPermisions)) checked @endif
                                                                                type="checkbox" name="permissions[]"
                                                                                value="{{ $permission->id }}" />
                                                                            {{ $permission->slug }}</li>
                                                                    @endif
                                                                @endforeach
                                                            </ul>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="col-sm-12 offset-sm-9">
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
    <script>
        var toggler = document.getElementsByClassName("caret");
        var i;

        for (i = 0; i < toggler.length; i++) {
            toggler[i].addEventListener("click", function() {
                this.parentElement.querySelector(".nested").classList.toggle("active");
                this.classList.toggle("caret-down");
            });
        }
    </script>
@endsection
