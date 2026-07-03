@extends('backEnd.layouts.master')
@section('title','Flash Sales Manage')
@section('css')
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<style>
#bulk-toolbar { display:none; }
#bulk-toolbar.visible { display:flex; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Flash Sales Manage</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    {{-- Bulk toolbar --}}
                    <div id="bulk-toolbar" class="align-items-center gap-2 mb-3 p-2 bg-light rounded">
                        <span id="selected-count" class="fw-bold text-secondary me-2">0 selected</span>
                        <button class="btn btn-warning btn-sm" id="bulk-yes"><i class="fe-check"></i> Set Flash Sale: Yes</button>
                        <button class="btn btn-secondary btn-sm" id="bulk-no"><i class="fe-x"></i> Set Flash Sale: No</button>
                        <button class="btn btn-outline-secondary btn-sm ms-2" id="bulk-clear">Clear selection</button>
                    </div>

                    <div class="table-responsive">
                        <table id="flashsales-datatable" class="table table-striped w-100">
                            <thead>
                                <tr>
                                    <th style="width:36px"><input type="checkbox" id="check-all" title="Select all"></th>
                                    <th>SL</th>
                                    <th>Image</th>
                                    <th style="min-width:200px">Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Flash Sale</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $key => $value)
                                <tr>
                                    <td><input type="checkbox" class="row-check" value="{{$value->id}}"></td>
                                    <td>{{$loop->iteration}}</td>
                                    <td><img src="{{asset($value->image ? $value->image->image : 'public/fallbacks/no-image.webp')}}" class="backend-image" alt="" onerror="this.src='{{asset('public/fallbacks/no-image.webp')}}'"></td>
                                    <td style="white-space:normal;word-break:break-word">{{$value->name}}</td>
                                    <td>{{$value->category ? $value->category->name : ''}}</td>
                                    <td>{{$value->new_price}}</td>
                                    <td>{{$value->stock}}</td>
                                    <td>
                                        <button class="btn btn-sm toggle-flashsale {{$value->flashsale ? 'btn-warning' : 'btn-outline-secondary'}}"
                                            data-id="{{$value->id}}" title="{{$value->flashsale ? 'Remove from Flash Sales' : 'Add to Flash Sales'}}">
                                            {{$value->flashsale ? 'Yes' : 'No'}}
                                        </button>
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
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function(){
    $('#flashsales-datatable').DataTable({
        pageLength: 25,
        responsive: false,
        columnDefs: [{ orderable: false, targets: [0, 1, 2, 7] }]
    });

    $('#check-all').on('change', function(){
        var checked = $(this).is(':checked');
        $('#flashsales-datatable tbody .row-check:visible').prop('checked', checked);
        updateToolbar();
    });

    $(document).on('change', '.row-check', function(){ updateToolbar(); });

    function updateToolbar(){
        var count = $('.row-check:checked').length;
        if(count > 0){
            $('#bulk-toolbar').addClass('visible');
            $('#selected-count').text(count + ' selected');
        } else {
            $('#bulk-toolbar').removeClass('visible');
        }
    }

    $('#bulk-clear').on('click', function(){
        $('.row-check, #check-all').prop('checked', false);
        updateToolbar();
    });

    function bulkSet(status){
        var ids = $('.row-check:checked').map(function(){ return $(this).val(); }).get();
        if(!ids.length) return;
        $.ajax({
            type: 'POST',
            url: '{{route('products.update_flash_sales')}}',
            data: { product_ids: ids, status: status, _token: '{{csrf_token()}}' },
            success: function(res){
                if(res.status === 'success'){
                    ids.forEach(function(id){
                        var btn = $('.toggle-flashsale[data-id="'+id+'"]');
                        if(status == 1){
                            btn.removeClass('btn-outline-secondary').addClass('btn-warning').text('Yes').attr('title','Remove from Flash Sales');
                        } else {
                            btn.removeClass('btn-warning').addClass('btn-outline-secondary').text('No').attr('title','Add to Flash Sales');
                        }
                    });
                    $('.row-check, #check-all').prop('checked', false);
                    updateToolbar();
                }
            }
        });
    }

    $('#bulk-yes').on('click', function(){ bulkSet(1); });
    $('#bulk-no').on('click',  function(){ bulkSet(0); });

    $(document).on('click', '.toggle-flashsale', function(){
        var btn = $(this);
        var id = btn.data('id');
        $.ajax({
            type: 'POST',
            url: '{{route('products.toggle_flash_sale')}}',
            data: { id: id, _token: '{{csrf_token()}}' },
            success: function(res){
                if(res.status === 'success'){
                    if(res.flashsale){
                        btn.removeClass('btn-outline-secondary').addClass('btn-warning').text('Yes').attr('title','Remove from Flash Sales');
                    } else {
                        btn.removeClass('btn-warning').addClass('btn-outline-secondary').text('No').attr('title','Add to Flash Sales');
                    }
                }
            }
        });
    });
});
</script>
@endsection
