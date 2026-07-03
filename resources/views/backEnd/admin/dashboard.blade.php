@extends('backEnd.layouts.master')
@section('title','Dashboard')
@section('css')
<!-- Plugins css -->
<link href="{{asset('public/backEnd/')}}/assets/libs/flatpickr/flatpickr.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('public/backEnd/')}}/assets/libs/selectize/css/selectize.bootstrap3.css" rel="stylesheet" type="text/css" />

@endsection
@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                
                </div>
                <h4 class="page-title">Dashboard</h4>
            </div>
        </div>
    </div>     
    <!-- end page title --> 

    <div class="row">
        <div class="col-md-6 col-xl-3">
            <div class="widget-rounded-circle card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-lg rounded-circle bg-soft-primary border-primary border">
                                <i class="fe-shopping-cart font-22 avatar-title text-primary"></i>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <h3 class="text-dark mt-1"><span data-plugin="counterup">{{$total_order}}</span></h3>
                                <p class="text-muted mb-1 text-truncate">Total Oreder</p>
                            </div>
                        </div>
                    </div> <!-- end row-->
                </div>
            </div> <!-- end widget-rounded-circle-->
        </div> <!-- end col-->

        <div class="col-md-6 col-xl-3">
            <div class="widget-rounded-circle card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-lg rounded-circle bg-soft-success border-success border">
                                <i class="fe-shopping-bag font-22 avatar-title text-success"></i>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <h3 class="text-dark mt-1"><span data-plugin="counterup">{{$today_order}}</span></h3>
                                <p class="text-muted mb-1 text-truncate">Today's Order</p>
                            </div>
                        </div>
                    </div> <!-- end row-->
                </div>
            </div> <!-- end widget-rounded-circle-->
        </div> <!-- end col-->

        <div class="col-md-6 col-xl-3">
            <div class="widget-rounded-circle card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-lg rounded-circle bg-soft-info border-info border">
                                <i class="fe-database font-22 avatar-title text-info"></i>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <h3 class="text-dark mt-1"><span data-plugin="counterup">{{$total_product}}</span></h3>
                                <p class="text-muted mb-1 text-truncate">Products</p>
                            </div>
                        </div>
                    </div> <!-- end row-->
                </div>
            </div> <!-- end widget-rounded-circle-->
        </div> <!-- end col-->

        <div class="col-md-6 col-xl-3">
            <div class="widget-rounded-circle card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-lg rounded-circle bg-soft-warning border-warning border">
                                <i class="fe-user font-22 avatar-title text-warning"></i>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <h3 class="text-dark mt-1"><span data-plugin="counterup">{{$total_customer}}</span></h3>
                                <p class="text-muted mb-1 text-truncate">Customer</p>
                            </div>
                        </div>
                    </div> <!-- end row-->
                </div>
            </div> <!-- end widget-rounded-circle-->
        </div> <!-- end col-->
    </div>
    <!-- end row-->


    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    {{-- Orders widget dropdown -- updated 2026-05-02 --}}
                    <div class="dropdown float-end">
                        <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#ordersEditModal">
                                <i class="fe-edit me-1"></i> Edit Report
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="{{ route('dashboard.orders.export', 'csv') }}" class="dropdown-item">
                                <i class="fe-download me-1"></i> Export as Excel (CSV)
                            </a>
                            <a href="{{ route('dashboard.orders.export', 'pdf') }}" target="_blank" class="dropdown-item">
                                <i class="fe-printer me-1"></i> Export as PDF
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="{{ route('dashboard.orders.reset') }}" class="dropdown-item text-danger"
                               onclick="return confirm('Reset orders widget to default (latest 5)?')">
                                <i class="fe-refresh-cw me-1"></i> Reset to Default
                            </a>
                        </div>
                    </div>

                    <h4 class="header-title mb-3">
                        Orders Report
                        <small class="text-muted fs-13 fw-normal ms-1">
                            ({{ $ordersLimit }} rows{{ $ordersStatus !== 'all' ? ', status: '.$ordersStatus : '' }})
                        </small>
                    </h4>

                    <div class="table-responsive">
                        <table class="table table-borderless table-hover table-nowrap table-centered m-0">

                            <thead class="table-light">
                                <tr>
                                    <th colspan="2">Id</th>
                                    <th>Invoice</th>
                                    <th>Amount</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($latest_order as $order)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td style="width: 36px;">
                                        <img src="{{asset($order->product?$order->product->image->image:'')}}" alt="contact-img" title="contact-img" class="rounded-circle avatar-sm" />
                                    </td>

                                    <td>
                                        {{$order->invoice_id}}
                                    </td>

                                    <td>
                                        {{$order->amount}}
                                    </td>

                                    <td>
                                        {{$order->customer?$order->customer->name:''}}
                                    </td>
                                    <td>
                                        {{$order->order_status}}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->

        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    {{-- Customers widget dropdown -- updated 2026-05-02 --}}
                    <div class="dropdown float-end">
                        <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#customersEditModal">
                                <i class="fe-edit me-1"></i> Edit Report
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="{{ route('dashboard.customers.export', 'csv') }}" class="dropdown-item">
                                <i class="fe-download me-1"></i> Export as Excel (CSV)
                            </a>
                            <a href="{{ route('dashboard.customers.export', 'pdf') }}" target="_blank" class="dropdown-item">
                                <i class="fe-printer me-1"></i> Export as PDF
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="{{ route('dashboard.customers.reset') }}" class="dropdown-item text-danger"
                               onclick="return confirm('Reset customers widget to default (latest 5)?')">
                                <i class="fe-refresh-cw me-1"></i> Reset to Default
                            </a>
                        </div>
                    </div>

                    <h4 class="header-title mb-3">
                        Customers Report
                        <small class="text-muted fs-13 fw-normal ms-1">({{ $customersLimit }} rows)</small>
                    </h4>

                    <div class="table-responsive">
                        <table class="table table-borderless table-nowrap table-hover table-centered m-0">

                            <thead class="table-light">
                                <tr>
                                    <th>Id</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($latest_customer as $customer)
                                <tr>
                                    <td>
                                        <h5 class="m-0 fw-normal">{{$loop->iteration}}</h5>
                                    </td>

                                    <td>
                                        {{$customer->name}}
                                    </td>

                                    <td>
                                        {{$customer->phone}}
                                    </td>

                                    <td>
                                        {{$customer->created_at->format('d-m-Y')}}
                                    </td>

                                    <td>
                                        {{$customer->status}}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div> <!-- end .table-responsive-->
                </div>
            </div> <!-- end card-->
        </div> <!-- end col -->
    </div>
    <!-- end row -->
    
</div> <!-- container -->

{{-- Orders Edit Report Modal -- added 2026-05-02 --}}
<div class="modal fade" id="ordersEditModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Orders Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('dashboard.orders.config') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Rows to show</label>
                        <input type="number" name="limit" class="form-control"
                               value="{{ $ordersLimit }}" min="1" max="100" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Filter by Status</label>
                        <select name="status" class="form-control">
                            <option value="all"       @if($ordersStatus=='all') selected @endif>All</option>
                            <option value="pending"   @if($ordersStatus=='pending') selected @endif>Pending</option>
                            <option value="delivered" @if($ordersStatus=='delivered') selected @endif>Delivered</option>
                            <option value="cancelled" @if($ordersStatus=='cancelled') selected @endif>Cancelled</option>
                            <option value="processing"@if($ordersStatus=='processing') selected @endif>Processing</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Customers Edit Report Modal -- added 2026-05-02 --}}
<div class="modal fade" id="customersEditModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Customers Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('dashboard.customers.config') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Rows to show</label>
                        <input type="number" name="limit" class="form-control"
                               value="{{ $customersLimit }}" min="1" max="100" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@section('script')
 <!-- Plugins js-->
        <script src="{{asset('public/backEnd/')}}/assets/libs/flatpickr/flatpickr.min.js"></script>
        <script src="{{asset('public/backEnd/')}}/assets/libs/apexcharts/apexcharts.min.js"></script>
        <script src="{{asset('public/backEnd/')}}/assets/libs/selectize/js/standalone/selectize.min.js"></script>

    <script>

    var colors = ["#f1556c"],
    dataColors = $("#total-revenue").data("colors");
    dataColors && (colors = dataColors.split(","));
    var options = {
          
          chart: {
             height: 242,
             type: "radialBar"
          },
          plotOptions: {
             radialBar: {
                hollow: {
                   size: "65%"
                }
             }
          },
          colors: colors,
          labels: ["Delivery"]
       },
        chart = new ApexCharts(document.querySelector("#total-revenue"), options);
        chart.render();
        colors = ["#1abc9c", "#4a81d4"];
        (dataColors = $("#sales-analytics").data("colors")) && (colors = dataColors.split(","));
        options = {
           series: [{
              name: "Revenue",
              type: "column",
              data: [@foreach($monthly_sale as $sale) {{$sale->amount}}, @endforeach]
           }, {
              name: "Sales",
              type: "line",
              data: [@foreach($monthly_sale as $sale) {{$sale->amount}}, @endforeach]
           }],
           chart: {
              height: 378,
              type: "line",
           },
           stroke: {
              width: [2, 3]
           },
           plotOptions: {
              bar: {
                 columnWidth: "50%"
              }
           },
           colors: colors,
           dataLabels: {
              enabled: !0,
              enabledOnSeries: [1]
           },
           labels: [@foreach($monthly_sale as $sale) {{date('d', strtotime($sale->date))}} + '-' + {{date('m', strtotime($sale->date))}}+ '-' + {{date('Y', strtotime($sale->date))}}, @endforeach],
           legend: {
              offsetY: 7
           },
           grid: {
              padding: {
                 bottom: 20
              }
           },
           fill: {
              type: "gradient",
              gradient: {
                 shade: "light",
                 type: "horizontal",
                 shadeIntensity: .25,
                 gradientToColors: void 0,
                 inverseColors: !0,
                 opacityFrom: .75,
                 opacityTo: .75,
                 stops: [0, 0, 0]
              }
           },
           yaxis: [{
              title: {
                 text: "Net Revenue"
              }
           }]
        };
        (chart = new ApexCharts(document.querySelector("#sales-analytics"), options)).render(), $("#dash-daterange").flatpickr({
           altInput: !0,
           mode: "range",
        });
    </script>
@endsection