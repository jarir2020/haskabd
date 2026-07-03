@extends('backEnd.layouts.master')
@section('title','Product Manage')
@section('css')
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right d-flex gap-2 align-items-center">
                    {{-- Export dropdown -- added 2026-05-02 --}}
                    <div class="dropdown">
                        <button class="btn btn-secondary rounded-pill dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fe-download me-1"></i> Export Product
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#" id="export-csv"><i class="fe-file-text me-1"></i> Excel (CSV)</a>
                            <a class="dropdown-item" href="#" id="export-pdf"><i class="fe-file me-1"></i> PDF</a>
                        </div>
                    </div>
                    <a href="{{route('products.create')}}" class="btn btn-danger rounded-pill"><i class="fe-shopping-cart"></i> Add Product</a>
                </div>
                <h4 class="page-title">Product Manage</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-sm-12">
                            <ul class="action2-btn">
                                <li><a href="{{route('products.update_deals',['status'=>1])}}" class="btn rounded-pill btn-success hotdeal_update"><i class="fe-thumbs-up"></i> Deal</a></li>
                                <li><a href="{{route('products.update_deals',['status'=>0])}}" class="btn rounded-pill btn-danger hotdeal_update"><i class="fe-thumbs-down"></i> Deal</a></li>
                                <li><a href="{{route('products.update_status',['status'=>1])}}" class="btn rounded-pill btn-primary update_status"><i class="fe-thumbs-up"></i> Active</a></li>
                                <li><a href="{{route('products.update_status',['status'=>0])}}" class="btn rounded-pill btn-warning update_status"><i class="fe-thumbs-down"></i> Inactive</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="product-datatable" class="table table-striped dt-responsive w-100">
                            <thead>
                                <tr>
                                    <th style="width:2%"><input type="checkbox" class="checkall"></th>
                                    <th>SL</th>
                                    <th>Action</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Image</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Deal & Feature</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $key => $value)
                                <tr>
                                    <td><input type="checkbox" class="checkbox" value="{{$value->id}}"></td>
                                    <td>{{$loop->iteration}}</td>
                                    <td>
                                        <div class="button-list custom-btn-list">
                                            @if($value->status == 1)
                                            <form method="post" action="{{route('products.inactive')}}" class="d-inline">
                                                @csrf
                                                <input type="hidden" value="{{$value->id}}" name="hidden_id">
                                                <button type="button" class="change-confirm" title="Active"><i class="fe-thumbs-down"></i></button>
                                            </form>
                                            @else
                                            <form method="post" action="{{route('products.active')}}" class="d-inline">
                                                @csrf
                                                <input type="hidden" value="{{$value->id}}" name="hidden_id">
                                                <button type="button" class="change-confirm" title="Inactive"><i class="fe-thumbs-up"></i></button>
                                            </form>
                                            @endif
                                            <a href="{{route('products.edit',$value->id)}}" title="Edit"><i class="fe-edit"></i></a>
                                            <form method="post" action="{{route('products.destroy')}}" class="d-inline">
                                                @csrf
                                                <input type="hidden" value="{{$value->id}}" name="hidden_id">
                                                <button type="submit" class="delete-confirm" title="Delete"><i class="fe-trash-2"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                    <td style="white-space:normal;word-break:break-word;min-width:180px;max-width:260px">{{$value->name}}</td>
                                    <td>{{$value->category ? $value->category->name : ''}}</td>
                                    <td><img src="{{asset($value->image ? $value->image->image : 'public/fallbacks/no-image.webp')}}" class="backend-image" alt="" onerror="this.src='{{asset('public/fallbacks/no-image.webp')}}'"></td>
                                    <td>{{$value->new_price}}</td>
                                    <td>{{$value->stock}}</td>
                                    <td>
                                        <p class="m-0">Hot Deals: {{$value->topsale==1?'Yes':'No'}}</p>
                                        <p class="m-0">Top Feature: {{$value->feature_product==1?'Yes':'No'}}</p>
                                    </td>
                                    <td>
                                        @if($value->status==1)
                                            <span class="badge bg-soft-success text-success">Active</span>
                                        @else
                                            <span class="badge bg-soft-danger text-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<!-- DataTables JS -->
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/pdfmake/build/pdfmake.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/pdfmake/build/vfs_fonts.js"></script>
{{-- Kalpurush Bangla font for PDF -- added 2026-05-02 --}}
<script src="{{asset('/public/backEnd/assets/js/kalpurush_vfs.js')}}"></script>
<script>
pdfMake.fonts = {
    Kalpurush: { normal: 'kalpurush.ttf', bold: 'kalpurush.ttf', italics: 'kalpurush.ttf', bolditalics: 'kalpurush.ttf' },
    Roboto:    { normal: 'Roboto-Regular.ttf', bold: 'Roboto-Medium.ttf', italics: 'Roboto-Italic.ttf', bolditalics: 'Roboto-MediumItalic.ttf' }
};
</script>

<script>
$(document).ready(function(){

    // Init DataTable -- added 2026-05-02
    var table = $('#product-datatable').DataTable({
        lengthMenu: [[10, 20, 50, 100, -1], [10, 20, 50, 100, 'All']],
        pageLength: 10,
        lengthChange: true,
        // disable sorting on checkbox (col 0), action (col 2), image (col 5), deal&feature (col 8)
        responsive: false,
        columnDefs: [
            { orderable: false, targets: [0, 2, 5, 8] }
        ],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        language: {
            paginate: {
                previous: "<i class='mdi mdi-chevron-left'>",
                next: "<i class='mdi mdi-chevron-right'>"
            }
        },
        drawCallback: function(){
            $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
        }
    });

    // Wire Export CSV button to DataTables CSV export
    $('#export-csv').on('click', function(e){
        e.preventDefault();
        table.button('.buttons-csv').trigger();
    });
    $('#export-pdf').on('click', function(e){
        e.preventDefault();
        table.button('.buttons-pdf').trigger();
    });

    // Hidden DataTables buttons for CSV + PDF (triggered by dropdown above)
    new $.fn.dataTable.Buttons(table, {
        buttons: [
            {
                extend: 'csvHtml5',
                className: 'buttons-csv',
                title: 'Products Report',
                exportOptions: { columns: [1,3,4,6,7,9] }
            },
            {
                extend: 'pdf',
                className: 'buttons-pdf',
                title: 'Products Report',
                exportOptions: { columns: [1,3,4,6,7,9] },
                // Use Kalpurush for Bangla support -- added 2026-05-02
                customize: function(doc) {
                    doc.defaultStyle.font = 'Kalpurush';
                    doc.styles.tableHeader.font = 'Kalpurush';
                }
            },
        ]
    });

    // Checkbox: select all on current page
    $('.checkall').on('change', function(){
        $('#product-datatable tbody tr').find('input.checkbox').prop('checked', $(this).is(':checked'));
    });

    // Bulk deal update
    $(document).on('click', '.hotdeal_update', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        var ids = $('input.checkbox:checked').map(function(){ return $(this).val(); }).get();
        if(!ids.length){ toastr.error('Please select a product first!'); return; }
        $.get(url, {product_ids: ids}, function(res){
            if(res.status === 'success'){ toastr.success(res.message); location.reload(); }
            else { toastr.error('Something went wrong'); }
        });
    });

    // Bulk status update
    $(document).on('click', '.update_status', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        var ids = $('input.checkbox:checked').map(function(){ return $(this).val(); }).get();
        if(!ids.length){ toastr.error('Please select a product first!'); return; }
        $.get(url, {product_ids: ids}, function(res){
            if(res.status === 'success'){ toastr.success(res.message); location.reload(); }
            else { toastr.error('Something went wrong'); }
        });
    });

});
</script>
@endsection
