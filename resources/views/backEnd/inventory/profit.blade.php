@extends('backEnd.layouts.master')
@section('title', 'Profit Calculation')
@section('css')
<link href="{{asset('/public/backEnd/')}}/assets/libs/flatpickr/flatpickr.min.css" rel="stylesheet" />
<style>
.rounded-pill-start { border-radius: 50px 0 0 50px !important; }
.rounded-pill-end   { border-radius: 0 50px 50px 0 !important; }
@media print {
    body * { visibility: hidden; }
    #print-area, #print-area * { visibility: visible; }
    #print-area { position: absolute; top: 0; left: 0; width: 100%; }
}
</style>
@endsection

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-success btn-sm rounded-pill-start" onclick="exportExcel()">
                            <i class="fe-download"></i> Export
                        </button>
                        <button type="button" class="btn btn-success btn-sm dropdown-toggle dropdown-toggle-split rounded-pill-end"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Toggle</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="#" onclick="exportExcel(); return false;">
                                    <i class="fe-file" style="color:#1d6f42"></i> Excel (.xlsx)
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="exportPdf(); return false;">
                                    <i class="fe-file" style="color:#e53935"></i> PDF
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <h4 class="page-title">Profit Calculation
                    <small class="text-muted fs-6 fw-normal ms-2">Based on completed orders only</small>
                </h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    {{-- Filters --}}
                    <form method="GET" action="{{ route('admin.inventory.profit') }}" class="row g-2 mb-3" id="filter-form">
                        <div class="col-sm-3">
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="form-control" placeholder="Search product name..." id="search-input" autocomplete="off">
                        </div>
                        <div class="col-sm-2">
                            <input type="text" name="start_date" id="start-date" value="{{ request('start_date') }}"
                                class="form-control" placeholder="Start date">
                        </div>
                        <div class="col-sm-2">
                            <input type="text" name="end_date" id="end-date" value="{{ request('end_date') }}"
                                class="form-control" placeholder="End date">
                        </div>
                        <div class="col-sm-auto d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm rounded-pill"><i class="fe-search"></i> Filter</button>
                            <a href="{{ route('admin.inventory.profit') }}" class="btn btn-secondary btn-sm rounded-pill">Reset</a>
                        </div>
                    </form>

                    <div id="print-area">
                    <div class="table-responsive" id="profit-table-wrap">
                        <table class="table table-hover table-centered mb-0" id="profit-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product Name</th>
                                    <th>Qty Sold</th>
                                    <th>Stock Remaining</th>
                                    <th>Total Revenue</th>
                                    <th>Total Cost</th>
                                    <th>Profit / Loss</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rows as $row)
                                <tr>
                                    <td>{{ $rows->firstItem() + $loop->index }}</td>
                                    <td>{{ $row->product_name }}</td>
                                    <td>{{ number_format($row->total_sold) }}</td>
                                    <td>{{ $stockMap->get($row->product_id, '—') }}</td>
                                    <td>৳ {{ number_format($row->total_revenue) }}</td>
                                    <td>৳ {{ number_format($row->total_cost) }}</td>
                                    <td>
                                        @if($row->profit >= 0)
                                            <span class="text-success fw-bold">৳ {{ number_format($row->profit) }}</span>
                                        @else
                                            <span class="text-danger fw-bold">৳ {{ number_format($row->profit) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No completed order data found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            @if($rows->total() > 0)
                            Showing {{ $rows->firstItem() }}–{{ $rows->lastItem() }} of {{ $rows->total() }} products
                            @endif
                        </small>
                        {{ $rows->links() }}
                    </div>

                    {{-- Grand Totals --}}
                    <div class="mt-4 d-flex justify-content-center">
                        <table class="table table-bordered" style="max-width:400px;">
                            <tbody>
                                <tr class="table-light">
                                    <th>Grand Total Revenue</th>
                                    <td class="fw-bold">৳ {{ number_format($grand_revenue) }}</td>
                                </tr>
                                <tr class="table-light">
                                    <th>Grand Total Cost</th>
                                    <td class="fw-bold">৳ {{ number_format($grand_cost) }}</td>
                                </tr>
                                <tr class="{{ $grand_profit >= 0 ? 'table-success' : 'table-danger' }}">
                                    <th>Grand Profit / Loss</th>
                                    <td class="fw-bold {{ $grand_profit >= 0 ? 'text-success' : 'text-danger' }}">
                                        ৳ {{ number_format($grand_profit) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    </div>{{-- /print-area --}}

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{asset('/public/backEnd/')}}/assets/libs/flatpickr/flatpickr.min.js"></script>
<script src="{{ asset('public/cdn/js/jquery.table2excel.min.js') }}"></script>
<script>
flatpickr('#start-date', { dateFormat: 'Y-m-d' });
flatpickr('#end-date',   { dateFormat: 'Y-m-d' });

var searchTimer;
$('#search-input').on('input', function(){
    clearTimeout(searchTimer);
    searchTimer = setTimeout(function(){ $('#filter-form').submit(); }, 400);
});

function exportExcel(){
    $('#profit-table').table2excel({
        filename: 'profit-calculation-{{ date('Y-m-d') }}',
        fileext: '.xlsx',
        exclude: '.no-export',
    });
}

function exportPdf(){
    window.print();
}
</script>
@endsection
