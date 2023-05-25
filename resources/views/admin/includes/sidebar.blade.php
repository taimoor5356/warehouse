<!-- BEGIN: Main Menu-->
<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item me-auto"><a class="navbar-brand" href="{{route('dashboard')}}">
                    <span class="brand-logo">
                    </span>
                    <div class="">
                        <span class="brand-text" style="margin-top: 0px; color: #4B4B4B; font-size: 22px; font-weight: bold">
                            Warehouse
                        </span>
                    </div>
                </a>
            </li>
        </ul>
    </div>
    <hr>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <!-- Dashboard -->
            <li
                class="nav-item @if (\Request::route()->getName() == 'dashboard') active @elseif(\Request::route()->getName() == 'home.dashboard') active @endif">
                <a class="d-flex align-items-center" href="/"><i data-feather="grid"></i><span
                        class="menu-title text-truncate" data-i18n="Invoice">Dashboard</span></a>
            </li>
            <!-- Batches -->
            <!-- Change permission of Customer View to Batches -->
            @can('order_view')
                <li class=" nav-item"><a class="d-flex align-items-center" href="{{ url('orders') }}"><i
                            data-feather='book-open'></i><span class="menu-title text-truncate"
                            data-i18n="Invoice">Batches</span></a>
                    <ul class="menu-content">
                        @can('order_create')
                            <li class=" nav-item {{ request()->is('orders/create') ? 'active' : '' }}"><a
                                    class="d-flex align-items-center" href="{{ url('orders/create') }}"><i
                                        data-feather="briefcase"></i><span class="menu-title text-truncate"
                                        data-i18n="Invoice">Add Batches</span></a>
                            </li>
                        @endcan
                        @can('order_view')
                            <li class=" nav-item {{ request()->is('orders') ? 'active' : '' }}"><a
                                    class="d-flex align-items-center" href="{{ url('orders') }}"><i
                                        data-feather="briefcase"></i><span class="menu-title text-truncate"
                                        data-i18n="Invoice">Batches History</span></a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan
            <!-- Customers -->
            @can('customer_view')
                <li class=" nav-item"><a class="d-flex align-items-center" href="#"><i data-feather='users'></i><span
                            class="menu-title archive" data-i18n="Invoice">Customers</span></a>
                    <ul class="menu-content">
                        @can('customer_create')
                            <li class=" nav-item {{ request()->is('customers/create') ? 'active' : '' }}"><a
                                    class="d-flex align-items-center" href="{{ url('customers/create') }}"><i
                                        data-feather="user-plus"></i><span class="menu-title text-truncate"
                                        data-i18n="Invoice">Add Customer</span></a>
                            </li>
                        @endcan
                        @can('customer_view')
                            <li class=" nav-item  {{ request()->is('customers') ? 'active' : '' }}"><a
                                    class="d-flex align-items-center" href="{{ url('customers') }}"><i
                                        data-feather="user-check"></i><span class="menu-title text-truncate"
                                        data-i18n="Invoice">Manage Customers</span></a>
                            </li>
                        @endcan
                        @can('labels_view')
                            <li class=" nav-item  {{ request()->is('brands') ? 'active' : '' }}"><a
                                    class="d-flex align-items-center" href="{{ url('brands') }}"><i
                                        data-feather="shopping-bag"></i><span class="menu-title text-truncate"
                                        data-i18n="Invoice">Manage Brands</span></a>
                            </li>
                        @endcan
                        @can('labels_view')
                            <li class=" nav-item  {{ request()->is('all/brand_products') ? 'active' : '' }}"><a
                                    class="d-flex align-items-center" href="{{ url('all/brand_products') }}"><i
                                        data-feather="plus"></i><span class="menu-title text-truncate" data-i18n="Invoice">Brand
                                        Products</span></a>
                            </li>
                        @endcan
                        @can('labels_view')
                            <li class=" nav-item  {{ request()->is('add_labels') ? 'active' : '' }}"><a
                                    class="d-flex align-items-center" href="{{ url('add_labels') }}"><i
                                        data-feather="shopping-bag"></i><span class="menu-title text-truncate"
                                        data-i18n="Invoice">Manage Labels</span></a>
                            </li>
                        @endcan
                        @can('sku_view')
                            <li class=" nav-item  {{ request()->is('sku') ? 'active' : '' }}"><a
                                    class="d-flex align-items-center" href="{{ url('sku') }}"><i
                                        data-feather="inbox"></i><span class="menu-title text-truncate"
                                        data-i18n="Invoice">Manage SKUs</span></a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan
            <!-- Inventory -->
            @can('inventory_view')
                <li class=" nav-item"><a class="d-flex align-items-center" href="#"><i
                            data-feather='database'></i><span class="menu-title text-truncate"
                            data-i18n="Invoice">Inventory</span></a>
                    <ul class="menu-content">
                        @can('product_view')
                            <li class=" nav-item {{ request()->is('products') ? 'active' : '' }}"><a
                                    class="d-flex align-items-center" href="{{ url('products') }}"><i
                                        data-feather="codesandbox"></i><span class="menu-title text-truncate"
                                        data-i18n="Invoice">Products</span></a>
                            </li>
                        @endcan
                        @can('category_view')
                            <li class=" nav-item {{ request()->is('category') ? 'active' : '' }}">
                                <a class="d-flex align-items-center" href="{{ url('category') }}">
                                    <i data-feather="columns"></i>
                                    <span class="menu-title text-truncate" data-i18n="Invoice">Categories</span>
                                </a>
                            </li>
                        @endcan
                        @can('inventory_view')
                            <li class=" nav-item {{ request()->is('inventory') ? 'active' : '' }}"><a
                                    class="d-flex align-items-center" href="{{ url('inventory') }}"><i
                                        data-feather="codesandbox"></i><span class="menu-title text-truncate"
                                        data-i18n="Invoice">Inventory Details</span></a>
                            </li>
                        @endcan
                        @can('unit_view')
                            <li class=" nav-item {{ request()->is('units') ? 'active' : '' }}">
                                <a class="d-flex align-items-center" href="{{ url('units') }}">
                                    <i data-feather='inbox'></i>
                                    <span class="menu-title text-truncate" data-i18n="Invoice">Units</span>
                                </a>
                            </li>
                        @endcan
                        @can('upcoming_inventory_view')
                            <li class=" nav-item {{ request()->is('upcoming_inventory') ? 'active' : '' }}"><a
                                    class="d-flex align-items-center" href="{{ url('upcoming_inventory') }}"><i
                                        data-feather='log-in'></i><span class="menu-title text-truncate"
                                        data-i18n="Invoice">Purchase Order</span></a>
                            </li>
                        @endcan
                        @can('otw_inventory_view')
                            <li class=" nav-item {{ request()->is('otw_inventory') ? 'active' : '' }}"><a
                                    class="d-flex align-items-center" href="{{ url('otw_inventory') }}"><i
                                        data-feather="truck"></i><span class="menu-title text-truncate"
                                        data-i18n="Invoice">OTW</span></a>
                            </li>
                        @endcan
                        @can('product_view')
                            <li class=" nav-item {{ request()->is('near_to_empty') ? 'active' : '' }}"><a
                                    class="d-flex align-items-center" href="{{ url('near_to_empty') }}"><i
                                        data-feather="briefcase"></i><span class="menu-title text-truncate"
                                        data-i18n="Invoice">Near to Empty</span></a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan
            <!-- Returns -->
            @can('return_order_view')
                <li class=" nav-item"><a class="d-flex align-items-center" href="{{ url('orders') }}"><i
                            data-feather="corner-down-left"></i><span class="menu-title text-truncate"
                            data-i18n="Invoice">Returns</span></a>
                    <ul class="menu-content">
                        <li class=" nav-item  {{ request()->is('create_return_order') ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ url('create_return_order') }}"><i
                                    data-feather="corner-down-left"></i><span class="menu-title text-truncate"
                                    data-i18n="Invoice">Customer Returns</span></a>
                        </li>
                        @can('file_storage_view')
                            <li class=" nav-item  {{ request()->is('stored_files') ? 'active' : '' }}">
                                <a class="d-flex align-items-center" href="{{ url('stored_files') }}"><i
                                        data-feather="file-text"></i><span class="menu-title text-truncate"
                                        data-i18n="Invoice">Files</span></a>
                            </li>
                        @endcan
                        @hasanyrole(['customer|Returns'])
                            <li class=" nav-item  {{ request()->is('order_return') ? 'active' : '' }}">
                                <a class="d-flex align-items-center" href="{{ url('order_return') }}"><i
                                        data-feather="file"></i><span class="menu-title text-truncate"
                                        data-i18n="Invoice">Returned History</span></a>
                            </li>
                        @endhasanyrole
                    </ul>
                </li>
            @endcan
            <!-- Invoices -->
            @can('invoices_view')
                <li class=" nav-item"><a class="d-flex align-items-center" href="#"><i
                            data-feather='file-text'></i><span class="menu-title archive"
                            data-i18n="Invoice">Invoices</span></a>
                    <ul class="menu-content">
                        <li class=" nav-item {{ request()->is('invoices') ? 'active' : '' }}"><a
                                class="d-flex align-items-center" href="{{ url('invoices') }}"><i
                                    data-feather="briefcase"></i><span class="menu-title text-truncate"
                                    data-i18n="Invoice">Invoices</span></a>
                        </li>
                        <li class=" nav-item {{ request()->is('view-merged-invoices') ? 'active' : '' }}"><a
                                class="d-flex align-items-center" href="{{ url('view-merged-invoices') }}"><i
                                    data-feather="briefcase"></i><span class="menu-title text-truncate"
                                    data-i18n="Invoice">Merged Invoices</span></a>
                        </li>
                    </ul>
                </li>
            @endcan
            <!-- Reports -->
            @can('report_view')
                <li class=" nav-item"><a class="d-flex align-items-center" href="#"><i
                            data-feather='file'></i><span class="menu-title text-truncate"
                            data-i18n="Reports">Reports</span></a>
                    <ul class="menu-content">
                        @can('report_sales_view')
                            <li class=" nav-item {{ request()->is('reports/sales') ? 'active' : '' }}">
                                <a class="d-flex align-items-center" href="{{ url('reports/sales') }}">
                                    <i data-feather='trending-up'></i>
                                    <span class="menu-title text-truncate" data-i18n="Invoice">Sales</span>
                                </a>
                            </li>
                        @endcan
                        @can('report_profit_view')
                            <li class=" nav-item {{ request()->is('reports/profit') ? 'active' : '' }}">
                                <a class="d-flex align-items-center" href="{{ url('reports/profit') }}">
                                    <i data-feather="dollar-sign"></i>
                                    <span class="menu-title text-truncate" data-i18n="Invoice">Profit</span>
                                </a>
                            </li>
                        @endcan
                        @can('report_inventory_forecast_view')
                            <li class=" nav-item {{ request()->is('inventory-forecast-report') ? 'active' : '' }}">
                                <a class="d-flex align-items-center" href="{{ url('inventory-forecast-report') }}">
                                    <i data-feather="file"></i>
                                    <span class="menu-title text-truncate" data-i18n="Invoice">Inventory Forecast
                                        Report</span>
                                </a>
                            </li>
                        @endcan
                        @can('report_inventory_history_view')
                            <li class=" nav-item {{ request()->is('inventory-history-report') ? 'active' : '' }}">
                                <a class="d-flex align-items-center" href="{{ url('inventory-history-report') }}">
                                    <i data-feather="file"></i>
                                    <span class="menu-title text-truncate" data-i18n="Invoice">Inventory History Report</span>
                                </a>
                            </li>
                        @endcan
                        @can('report_brands_view')
                            <li class=" nav-item {{ request()->is('brands-report') ? 'active' : '' }}">
                                <a class="d-flex align-items-center" href="{{ url('brands-report') }}">
                                    <i data-feather='trending-up'></i>
                                    <span class="menu-title text-truncate" data-i18n="Invoice">Brands Report</span>
                                </a>
                            </li>
                        @endcan
                        @can('report_labels_forecast_view')
                            <li class=" nav-item {{ request()->is('labelsforecast') ? 'active' : '' }}">
                                <a class="d-flex align-items-center" href="{{ url('labelsforecast') }}">
                                    <i data-feather='trending-up'></i>
                                    <span class="menu-title text-truncate" data-i18n="Invoice">Labels Forecast</span>
                                </a>
                            </li>
                        @endcan
                        @can('report_all_products_view')
                            <li
                                class=" nav-item  {{ request()->is('all-products-report') ? 'active' : (request()->is('filter-product-report') ? 'active' : '') }}">
                                <a class="d-flex align-items-center" href="{{ url('all-products-report') }}"><i
                                        data-feather="file"></i><span class="menu-title text-truncate"
                                        data-i18n="Invoice">All
                                        Products Report</span></a>
                            </li>
                        @endcan
                        @can('report_products_in_out_view')
                            <li
                                class=" nav-item  {{ request()->is('product/report') ? 'active' : (request()->is('product/submit/report') ? 'active' : '') }}">
                                <a class="d-flex align-items-center" href="{{ url('product/report') }}">
                                    <i data-feather="file"></i>
                                    <span class="menu-title text-truncate" data-i18n="Invoice">Product In/Out Report</span>
                                </a>
                            </li>
                        @endcan
                        @can('report_products_brand_view')
                            <li class=" nav-item {{ request()->is('product_brands_report') ? 'active' : '' }}">
                                <a class="d-flex align-items-center" href="{{ url('product_brands_report') }}">
                                    <i data-feather="file"></i>
                                    <span class="menu-title text-truncate" data-i18n="Invoice">Products Brand Report</span>
                                </a>
                            </li>
                        @endcan
                        @can('report_labels_view')
                            <li class=" nav-item {{ request()->is('reports/labels_report') ? 'active' : '' }}">
                                <a class="d-flex align-items-center" href="{{ route('labels_report') }}">
                                    <i data-feather="file"></i>
                                    <span class="menu-title text-truncate" data-i18n="Invoice">Labels Report</span>
                                </a>
                            </li>
                        @endcan
                        @can('report_postage_view')
                            <li class=" nav-item {{ request()->is('postage-report') ? 'active' : '' }}">
                                <a class="d-flex align-items-center" href="{{ url('postage-report') }}">
                                    <i data-feather="file"></i>
                                    <span class="menu-title text-truncate" data-i18n="Invoice">Postage Report</span>
                                </a>
                            </li>
                        @endcan
                        @can('report_returned_view')
                            <li class=" nav-item  {{ request()->is('order_return') ? 'active' : '' }}">
                                <a class="d-flex align-items-center" href="{{ url('order_return') }}"><i
                                        data-feather="file"></i><span class="menu-title text-truncate"
                                        data-i18n="Invoice">Returned Reports</span></a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan
            <!-- Users -->
            @can('userrole_view')
                <li class=" nav-item"><a class="d-flex align-items-center" href="{{ url('orders') }}"><i
                            data-feather='user-plus'></i><span class="menu-title text-truncate"
                            data-i18n="Invoice">Users</span></a>
                    <ul class="menu-content">
                        @can('userrole_view')
                            <li class=" nav-item @if (strpos(\Request::route()->getName(), 'roles') !== false) active @endif">
                                <a class="d-flex align-items-center" href="{{ url('roles') }}">
                                    <i data-feather="user"></i>
                                    <span class="menu-title text-truncate" data-i18n="Invoice">Roles</span>
                                </a>
                            </li>
                        @endcan
                        @can('userrole_view')
                            <li class=" nav-item @if (strpos(\Request::route()->getName(), 'user.index') !== false) active @endif">
                                <a class="d-flex align-items-center" href="{{ url('user') }}">
                                    <i data-feather="users"></i>
                                    <span class="menu-title text-truncate" data-i18n="Invoice">View Users</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan
            <!-- Settings -->
            @can('setting_update')
                <li class=" nav-item @if (strpos(\Request::route()->getName(), 'settings') !== false) active @endif""><a
                        class="
                    d-flex align-items-center" href="{{ url('settings') }}"><i
                            data-feather="settings"></i><span class="menu-title text-truncate"
                            data-i18n="Invoice">Settings</span></a>
                </li>
            @endcan
        </ul>
    </div>
</div>
<!-- END: Main Menu-->
