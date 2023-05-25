@extends('admin.layout.app')
@section('title', 'Dashboard')
@section('page_cs')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/css/plugins/charts/chart-apex.css') }}">
@endsection

@section('content')
    <section id="dashboard-ecommerce">
        <div class="row">
            @can('product_view')
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h2 class="fw-bolder mb-0">{{ number_format($products) }}</h2>
                            <p class="card-text">Products</p>
                        </div>
                        <div class="avatar bg-light-success p-50 m-0">
                            <div class="avatar-content">
                                <a href="/products"><i data-feather="credit-card" class="font-medium-5"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endcan
            @can('inventory_view')
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h2 class="fw-bolder mb-0">$ {{ number_format($getAllPurchases, 2) }}</h2>
                            <p class="card-text">Inventory Cost</p>
                        </div>
                        <div class="avatar bg-light-primary p-50 m-0">
                            <div class="avatar-content">
                                <a href="/inventory"><i data-feather="database" class="font-medium-5"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endcan
            @can('report_sales_view')
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h2 class="fw-bolder mb-0">$ {{ number_format($getAllSales, 2) }}</h2>
                            <p class="card-text">Sales</p>
                        </div>
                        <div class="avatar bg-light-danger p-50 m-0">
                            <div class="avatar-content">
                                <a href="/reports/sales"><i data-feather="shopping-cart" class="font-medium-5"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endcan
            @can('report_profit_view')
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h2 class="fw-bolder mb-0">$ {{ number_format($getAllProfit, 2) }}</h2>
                            <p class="card-text">Profit</p>
                        </div>
                        <div class="avatar bg-light-success p-50 m-0">
                            <div class="avatar-content">
                                <a href="/reports/profit"><i data-feather="activity" class="font-medium-5"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endcan
            @can ('customer_view')
                <div class="col-lg-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <h2 class="fw-bolder mb-0">{{ number_format($customers) }}</h2>
                                <p class="card-text">Customers</p>
                            </div>
                            <div class="avatar bg-light-secondary p-50 m-0">
                                <div class="avatar-content">
                                    <a href="/customers"><i data-feather="users" class="font-medium-5"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan
            @can('labels_view')
                <div class="col-lg-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <h2 class="fw-bolder mb-0">{{ number_format($brands) }}</h2>
                                <p class="card-text">Brands</p>
                            </div>
                            <div class="avatar bg-light-warning p-50 m-0">
                                <div class="avatar-content">
                                    <a href="/brands"><i data-feather="briefcase" class="font-medium-5"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan
            @can('sku_view')
                <div class="col-lg-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <h2 class="fw-bolder mb-0">{{ number_format($sku) }}</h2>
                                <p class="card-text">SKU</p>
                            </div>
                            <div class="avatar bg-light-light p-50 m-0">
                                <div class="avatar-content">
                                    <a href="/sku"><i data-feather="inbox" class="font-medium-5"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan
            @can('order_view')
                <div class="col-lg-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <h2 class="fw-bolder mb-0">{{ number_format($getAllBatches) }}</h2>
                                <p class="card-text">Total Batches</p>
                            </div>
                            <div class="avatar bg-light-success p-50 m-0">
                                <div class="avatar-content">
                                    <a href="/orders"><i data-feather="activity" class="font-medium-5"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <h2 class="fw-bolder mb-0">{{ number_format($getAllMailers) }}</h2>
                                <p class="card-text">Total Orders</p>
                            </div>
                            <div class="avatar bg-light-success p-50 m-0">
                                <div class="avatar-content">
                                    <a href="/orders"><i data-feather="activity" class="font-medium-5"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan
            @can('file_storage_view')
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h2 class="fw-bolder mb-0">{{ number_format($file_count) }}</h2>
                            <p class="card-text">Files</p>
                        </div>
                        <div class="avatar bg-light-success p-50 m-0">
                            <div class="avatar-content">
                                <a href="/stored_files"><i data-feather="file" class="font-medium-5"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endcan
            @if (Auth::user()->hasRole('customer'))
                <div class="col-lg-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <h2 class="fw-bolder mb-0">{{ number_format($returned_order_count) }}</h2>
                                <p class="card-text">Returned Orders</p>
                            </div>
                            <div class="avatar bg-light-success p-50 m-0">
                                <div class="avatar-content">
                                    <a href="/order_return"><i data-feather="activity" class="font-medium-5"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <h2 class="fw-bolder mb-0">{{ number_format($complete_sales_count) }}</h2>
                                <p class="card-text">Completed Orders</p>
                            </div>
                            <div class="avatar bg-light-success p-50 m-0">
                                <div class="avatar-content">
                                    <a href="/orders"><i data-feather="activity" class="font-medium-5"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-9">
                        <div class="row match-height">
                            <!-- Sales Line Chart Card -->
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header align-items-start">
                                        <div>
                                            <h4 class="card-title mb-25">Sales</h4>
                                            <p class="card-text mb-0">Total Sales: {{ number_format($sales_count) }}</p>
                                        </div>
                                    </div>
                                    <div class="card-body pb-0">
                                        <div id="sales-line-chart"></div>
                                    </div>
                                </div>
                            </div>
                            <!--/ Customers Chart Card -->
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row">
                            <!-- Product Order Card -->
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between">
                                        <h4 class="card-title">Batches</h4>
                                        <div class="dropdown chart-dropdown">
                                            <button class="btn btn-sm border-0 dropdown-toggle px-50" type="button"
                                                id="dropdownItem2" data-bs-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                                Last 7 Days
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownItem2">
                                                <a class="dropdown-item" href="#">Last 28 Days</a>
                                                <a class="dropdown-item" href="#">Last Month</a>
                                                <a class="dropdown-item" href="#">Last Year</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="product-order-chart"></div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <div class="d-flex align-items-center">
                                                <i data-feather="circle" class="font-medium-1 text-primary"></i>
                                                <span class="fw-bold ms-75">New Batches</span>
                                            </div>
                                            <span>{{ number_format($orders_by_status['new']) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <div class="d-flex align-items-center">
                                                <i data-feather="circle" class="font-medium-1 text-warning"></i>
                                                <span class="fw-bold ms-75">In Process</span>
                                            </div>
                                            <span>{{ number_format($orders_by_status['in_process']) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <div class="d-flex align-items-center">
                                                <i data-feather="circle" class="font-medium-1 text-danger"></i>
                                                <span class="fw-bold ms-75">Shipped</span>
                                            </div>
                                            <span>{{ number_format($orders_by_status['shipped']) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <div class="d-flex align-items-center">
                                                <i data-feather="circle" class="font-medium-1 text-success"></i>
                                                <span class="fw-bold ms-75">Delivered</span>
                                            </div>
                                            <span>{{ number_format($orders_by_status['delivered']) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <i data-feather="circle" class="font-medium-1 text-secondary"></i>
                                                <span class="fw-bold ms-75">Cancelled</span>
                                            </div>
                                            <span>{{ number_format($orders_by_status['cancelled']) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="row">
            @can('report_view')
            <div class="col-md-9">
                <div class="row match-height m-0 p-0">
                    <!-- Sales Line Chart Card -->
                    <div class="col-12 m-0 p-0">
                        @can('report_sales_view')
                        <div class="card">
                            <div class="card-header align-items-start">
                                <div>
                                    <h4 class="card-title mb-25">Sales</h4>
                                    <p class="card-text mb-0">Total Sales: {{ $sales_count }}</p>
                                </div>
                            </div>
                            <div class="card-body pb-0">
                                <div id="sales-line-chart"></div>
                            </div>
                        </div>
                        @endcan
                        @can('report_profit_view')
                        <div class="card">
                            <div class="card-header align-items-start">
                                <div>
                                    <h4 class="card-title mb-25">Profit</h4>
                                    <p class="card-text mb-0">Total Profit: $ {{ number_format($profit, 2) }}</p>
                                </div>
                            </div>
                            <div class="card-body pb-0">
                                <div id="profit-lines-chart"></div>
                            </div>
                        </div>
                        @endcan
                    </div>
                    <!--/ Customers Chart Card -->
                </div>
            </div>
            @endcan
            @can('order_view')
            <div class="col-md-3">
                <div class="row">
                    <!-- Product Order Card -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="card-title">Batches</h4>
                                <div class="dropdown chart-dropdown">
                                    <button class="btn btn-sm border-0 dropdown-toggle px-50" type="button"
                                        id="dropdownItem2" data-bs-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        Last 7 Days
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownItem2">
                                        <a class="dropdown-item" href="#">Last 28 Days</a>
                                        <a class="dropdown-item" href="#">Last Month</a>
                                        <a class="dropdown-item" href="#">Last Year</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="product-order-chart"></div>
                                <div class="d-flex justify-content-between mb-1">
                                    <div class="d-flex align-items-center">
                                        <i data-feather="circle" class="font-medium-1 text-primary"></i>
                                        <span class="fw-bold ms-75">New Batches</span>
                                    </div>
                                    <span>{{ number_format($orders_by_status['new']) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <div class="d-flex align-items-center">
                                        <i data-feather="circle" class="font-medium-1 text-warning"></i>
                                        <span class="fw-bold ms-75">In Process</span>
                                    </div>
                                    <span>{{ number_format($orders_by_status['in_process']) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <div class="d-flex align-items-center">
                                        <i data-feather="circle" class="font-medium-1 text-danger"></i>
                                        <span class="fw-bold ms-75">Shipped</span>
                                    </div>
                                    <span>{{ number_format($orders_by_status['shipped']) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <div class="d-flex align-items-center">
                                        <i data-feather="circle" class="font-medium-1 text-success"></i>
                                        <span class="fw-bold ms-75">Delivered</span>
                                    </div>
                                    <span>{{ number_format($orders_by_status['delivered']) }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <i data-feather="circle" class="font-medium-1 text-secondary"></i>
                                        <span class="fw-bold ms-75">Cancelled</span>
                                    </div>
                                    <span>{{ number_format($orders_by_status['cancelled']) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--/ Product Order Card -->

                    <!-- Earnings Card -->
                    <div class="col-12">
                        <div class="card earnings-card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-5">
                                        <h4 class="card-title mb-1">Earnings</h4>
                                        <div class="font-small-2">This Month</div>
                                        <h5 class="mb-1">${{ $earning }}</h5>
                                        <p class="card-text text-muted font-small-2">
                                            <span class="fw-bolder">
                                                
                                            </span><span>
                                                
                                            </span>
                                        </p>
                                    </div>
                                    <div class="col-7">
                                        <div id="earnings-donut-chart"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--/ Earnings Card -->
                </div>
            </div>
            @endcan
        </div>
        <div class="row">
            @can('product_view')
            <div class="col-md-9">
                <div class="row match-height">
                    <!-- Sales Line Chart Card -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header align-items-start">
                                <div>
                                    <h4 class="card-title mb-25">Products | <a href="product/report"><i
                                                data-feather="eye"></i> View Reports</a></h4>
                                    <p class="card-text mb-0">15 highest selling products</p>
                                </div>
                            </div>
                            <div class="card-body pb-0">
                                <div id="last-fifteen-days-highest-sale"></div>
                            </div>
                        </div>
                    </div>
                    <!--/ Customers Chart Card -->
                </div>
            </div>
            @endcan
        </div>
    </section>
@endsection


@section('page_js')
    <script src="{{ asset('admin/app-assets/vendors/js/charts/apexcharts.min.js') }}"></script>

    <script>
        var $strokeColor = '#ebe9f1';
        var $textMutedColor = '#b9b9c3';
        var $salesStrokeColor2 = '#df87f2';
        var $textHeadingColor = '#5e5873';
        var $salesStrokeColor2 = '#df87f2';
        var $white = '#fff';
        var $earningsStrokeColor2 = '#28c76f66';
        var $earningsStrokeColor3 = '#28c76f33';
        var $earningsStrokeColor4 = '#ea545570';
        var $salesLineChart = document.querySelector('#sales-line-chart');
        var $productOrderChart = document.querySelector('#product-order-chart');
        var $earningsChart = document.querySelector('#earnings-donut-chart');
        var $profitLineChart = document.querySelector('#profit-lines-chart');
        var $lastfifteendayshighestsale = document.querySelector('#last-fifteen-days-highest-sale');

        var fifteendayshighestsale = {
            series: [{
                name: "Sale",
                data: {!! $highest_product_prices !!}
            }],
            xaxis: {
                type: 'numeric'
            },
            yaxis: [{
                labels: {
                    style: {
                        colors: "#FF1654"
                    },
                    formatter: (value) => {
                        return '$' + value.toFixed(2)
                    },
                },
            }, ],
            chart: {
                height: 450,
                type: 'bar',
                zoom: {
                    enabled: true
                }
            },
            plotOptions: {
                bar: {
                    barHeight: '100%',
                    distributed: true,
                    horizontal: false,
                    dataLabels: {
                        position: 'bottom'
                    },
                }
            },
            colors: ['#33b2df', '#546E7A', '#d4526e', '#13d8aa', '#A5978B', '#2b908f', '#f9a3a4', '#90ee7e',
                '#f48024', '#69d2e7'
            ],
            dataLabels: {
                enabled: true,
                textAnchor: 'start',
                style: {
                    colors: ['#fff']
                },
                formatter: function(val, opt) {
                    return "$" + val
                },
                offsetX: 0,
                dropShadow: {
                    enabled: true
                }
            },
            stroke: {
                width: 1,
                colors: ['#fff']
            },
            // title: {
            //   text: 'Top 20 Products',
            //   align: 'left'
            // },
            grid: {
                row: {
                    colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                    opacity: 0
                },
            },
            xaxis: {
                categories: {!! $highest_product_names !!},
            }
        };

        lastfifteendayshighestsale = new ApexCharts($lastfifteendayshighestsale, fifteendayshighestsale);
        lastfifteendayshighestsale.render();

        var flatPicker = $('.flat-picker'),
            isRtl = $('html').attr('data-textdirection') === 'rtl',
            chartColors = {
                column: {
                    series1: '#826af9',
                    series2: '#d2b0ff',
                    bg: '#f8d3ff'
                },
                success: {
                    shade_100: '#7eefc7',
                    shade_200: '#06774f'
                },
                donut: {
                    series1: '#ffe700',
                    series2: '#00d4bd',
                    series3: '#826bf8',
                    series4: '#2b9bf4',
                    series5: '#FFA1A1'
                },
                area: {
                    series3: '#a4f8cd',
                    series2: '#60f2ca',
                    series1: '#2bdac7'
                }
            };

        // Sales Line Chart
        var options = {
            chart: {
                height: 450,
                type: "area",
                stacked: false
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 4
            },
            colors: ["#FF1654", "#247BA0"],
            series: [{
                    name: "Sales",
                    data: {!! $sales_by_month !!}
                },
                {
                    name: "Cost",
                    data: {{ $sales_by_month_cost }}
                }
            ],
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'July', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            },
            yaxis: [{
                    forceNiceScale: true,
                    floating: false,
                    axisTicks: {
                        show: true
                    },
                    axisBorder: {
                        show: true,
                        color: "#FF1654"
                    },
                    labels: {
                        style: {
                            colors: "#FF1654"
                        },
                        formatter: (value) => {
                            return value.toFixed(0)
                        },
                    },
                    title: {
                        text: "Sales",
                        style: {
                            color: "#FF1654",
                            fontSize: "20px"
                        }
                    }
                },
                {
                    opposite: true,
                    axisTicks: {
                        show: true
                    },
                    axisBorder: {
                        show: true,
                        color: "#247BA0"
                    },
                    labels: {
                        style: {
                            colors: "#247BA0"
                        },
                        formatter: function(val) {
                            return '$' + val;
                        }
                    },
                    title: {
                        text: "Cost",
                        style: {
                            color: "#247BA0",
                            fontSize: "20px"
                        }
                    }
                }
            ],
            tooltip: {
                shared: false,
                intersect: false,
                x: {
                    show: true
                }
            },
            legend: {
                horizontalAlign: "left",
                offsetX: 20
            }
        };

        var chart = new ApexCharts($salesLineChart, options);
        chart.render();

        //  profit line chart
        var salesLineChartOptions = {
            chart: {
                height: 450,
                type: "area",
                stacked: false
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 4
            },
            colors: [window.colors.solid.success, "#247BA0"],
            series: [{
                name: "profit",
                data: [{{ $profit_by_month['profit'] }}]
            }],
            xaxis: {
                categories: [{!! $profit_by_month['month'] !!}],
            },
            yaxis: [{
                forceNiceScale: true,
                floating: false,
                axisTicks: {
                    show: true
                },
                axisBorder: {
                    show: true,
                    color: window.colors.solid.secondary,
                    width: '2px'
                },
                labels: {
                    style: {
                        colors: "#FF1654"
                    },
                    formatter: (value) => {
                        return "$" + value
                    },
                },
                title: {
                    text: "Profit",
                    style: {
                        color: "#FF1654",
                        fontSize: "20px"
                    }
                }
            }],
            tooltip: {
                shared: false,
                intersect: false,
                x: {
                    show: true
                }
            },
            legend: {
                horizontalAlign: "left",
                offsetX: 20
            }
        };

        profitLineChart = new ApexCharts($profitLineChart, salesLineChartOptions);
        profitLineChart.render();

        //-----------------------------
        // salesLineChartOptions = {
        //     chart: {
        //         height: 240,
        //         toolbar: {
        //             show: false
        //         },
        //         zoom: {
        //             enabled: false
        //         },
        //         type: 'line',
        //         dropShadow: {
        //             enabled: true,
        //             top: 18,
        //             left: 2,
        //             blur: 5,
        //             opacity: 0.2
        //         },
        //         offsetX: -10
        //     },
        //     stroke: {
        //         curve: 'smooth',
        //         width: 4
        //     },
        //     grid: {
        //         borderColor: $strokeColor,
        //         padding: {
        //             top: -20,
        //             bottom: 5,
        //             left: 20
        //         }
        //     },
        //     legend: {
        //         show: false
        //     },
        //     colors: [$salesStrokeColor2],
        //     fill: {
        //         type: 'gradient',
        //         gradient: {
        //             shade: 'dark',
        //             inverseColors: false,
        //             gradientToColors: [window.colors.solid.primary],
        //             shadeIntensity: 1,
        //             type: 'horizontal',
        //             opacityFrom: 1,
        //             opacityTo: 1,
        //             stops: [0, 100, 100, 100]
        //         }
        //     },
        //     markers: {
        //         size: 0,

        //     },
        //     xaxis: {
        //         labels: {
        //             offsetY: 5,
        //             style: {
        //                 colors: $textMutedColor,
        //                 fontSize: '0.857rem'
        //             }
        //         },
        //         axisTicks: {
        //             show: false
        //         },
        //         categories: [{!! $profit_by_month['month'] !!}],
        //         axisBorder: {
        //             show: false
        //         },
        //         tickPlacement: 'on'
        //     },
        //     yaxis: {
        //         tickAmount: 5,
        //         labels: {
        //             style: {
        //                 colors: $textMutedColor,
        //                 fontSize: '0.857rem'
        //             },
        //             formatter: function (val) {
        //                 return val > 999 ? (val / 1000).toFixed(1) + 'k' : val;
        //             }
        //         }
        //     },
        //     tooltip: {
        //         x: {
        //             show: false
        //         }
        //     },
        //     series: [{
        //         type: 'line',
        //         name: 'Sales',
        //         data: [{{ $profit_by_month['profit'] }}]
        //     }]
        // };


        // Product Order Chart
        // -----------------------------
        orderChartOptions = {
            chart: {
                height: 325,
                type: 'radialBar'
            },
            colors: [window.colors.solid.primary, window.colors.solid.warning, window.colors.solid.danger, window.colors
                .solid.success, window.colors.solid.secondary
            ],
            stroke: {
                lineCap: 'round'
            },
            plotOptions: {
                radialBar: {
                    size: 150,
                    hollow: {
                        size: '20%'
                    },
                    track: {
                        strokeWidth: '100%',
                        margin: 15
                    },
                    dataLabels: {
                        value: {
                            fontSize: '1rem',
                            colors: $textHeadingColor,
                            fontWeight: '500',
                            offsetY: 5
                        },
                        total: {
                            show: true,
                            label: 'Total',
                            fontSize: '1.286rem',
                            colors: $textHeadingColor,
                            fontWeight: '500',

                            formatter: function(w) {
                                // By default this function returns the average of all series. The below is just an example to show the use of custom formatter function
                                return {{ $sales_count }};
                            }
                        }
                    }
                }
            },
            series: [{{ $orders_by_status_graph }}],
            labels: ['New Batches', 'In Process', 'Shipped', 'Delivered', 'Cancelled']
        };
        orderChart = new ApexCharts($productOrderChart, orderChartOptions);
        orderChart.render();


        // Earnings Chart
        // -----------------------------
        @if ($percentage == '0')
            '100%'
        @else
            {{ $percentage }}
        @endif;
        @if ($percentage == '0' || $percentage > 0)
            'Profit'
        @else
            'Loss'
        @endif;
        earningsChartOptions = {
            chart: {
                type: 'donut',
                height: 120,
                toolbar: {
                    show: false
                }
            },
            dataLabels: {
                enabled: false
            },
            series: [40000],
            legend: {
                show: false
            },
            comparedResult: [30000],
            labels: ['App', 'Service', 'Product'],
            stroke: {
                width: 0
            },
            colors: [$earningsStrokeColor2, $earningsStrokeColor3, window.colors.solid.success],
            grid: {
                padding: {
                    right: -20,
                    bottom: -8,
                    left: -20
                }
            },
            plotOptions: {
                pie: {
                    startAngle: -10,
                    donut: {
                        labels: {
                            show: true,
                            name: {
                                offsetY: 15
                            },
                            value: {
                                offsetY: -15,
                                formatter: function(val) {
                                    return parseInt(val) + '%';
                                }
                            },
                            total: {
                                show: true,
                                offsetY: 15,
                                label: status,
                                formatter: function(w) {
                                    return {{$percentage}}
                                }
                            }
                        }
                    }
                }
            },
            responsive: [{
                    breakpoint: 1325,
                    options: {
                        chart: {
                            height: 100
                        }
                    }
                },
                {
                    breakpoint: 1200,
                    options: {
                        chart: {
                            height: 120
                        }
                    }
                },
                {
                    breakpoint: 1065,
                    options: {
                        chart: {
                            height: 100
                        }
                    }
                },
                {
                    breakpoint: 992,
                    options: {
                        chart: {
                            height: 120
                        }
                    }
                }
            ]
        };
        earningsChart = new ApexCharts($earningsChart, earningsChartOptions);
        earningsChart.render();
    </script>
@endsection
