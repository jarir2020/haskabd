@extends('backEnd.layouts.master')
@section('title', 'Product Inventory')
@section('css')
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
<style>
#bulk-toolbar { display:none; }
#bulk-toolbar.visible { display:flex; }
.inline-edit-val { cursor:pointer; border-bottom:1px dashed #aaa; }
.inline-edit-input { width:80px; display:none; }
@media print {
    body * { visibility: hidden; }
    #print-area, #print-area * { visibility: visible; }
    #print-area { position: absolute; top: 0; left: 0; width: 100%; }
    .save-row-btn, input[type="checkbox"] { display:none !important; }
}
</style>
@endsection

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right d-flex gap-2 align-items-center">
                    <a href="{{ route('admin.inventory.sample') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                        <i class="fe-download"></i> Sample CSV
                    </a>
                    <button data-bs-toggle="modal" data-bs-target="#importModal" class="btn btn-warning btn-sm rounded-pill">
                        <i class="fe-upload"></i> Import
                    </button>
                    <div class="btn-group">
                        <a href="{{ route('admin.inventory.export') }}" class="btn btn-success btn-sm" style="border-radius:50px 0 0 50px;">
                            <i class="fe-download-cloud"></i> Export All
                        </a>
                        <button type="button" class="btn btn-success btn-sm dropdown-toggle dropdown-toggle-split"
                            style="border-radius:0 50px 50px 0;"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Toggle</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.inventory.export') }}">
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
                <h4 class="page-title">Product Inventory</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    {{-- Filters --}}
                    <form method="GET" action="{{ route('admin.inventory.products') }}" class="row g-2 mb-3" id="filter-form">
                        <div class="col-sm-3">
                            <input type="text" name="search" id="search-input" value="{{ request('search') }}"
                                class="form-control" placeholder="Search name or code..." autocomplete="off">
                        </div>
                        <div class="col-sm-2">
                            <select name="category_id" class="form-select">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <select name="sort" class="form-select">
                                <option value="latest"        {{ request('sort','latest') === 'latest'        ? 'selected':'' }}>Newest First</option>
                                <option value="name"          {{ request('sort') === 'name'          ? 'selected':'' }}>Name A-Z</option>
                                <option value="stock_asc"     {{ request('sort') === 'stock_asc'     ? 'selected':'' }}>Stock ↑</option>
                                <option value="stock_desc"    {{ request('sort') === 'stock_desc'    ? 'selected':'' }}>Stock ↓</option>
                                <option value="purchase_asc"  {{ request('sort') === 'purchase_asc'  ? 'selected':'' }}>Purchase Price ↑</option>
                                <option value="purchase_desc" {{ request('sort') === 'purchase_desc' ? 'selected':'' }}>Purchase Price ↓</option>
                            </select>
                        </div>
                        <div class="col-sm-auto d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm rounded-pill"><i class="fe-search"></i> Filter</button>
                            <a href="{{ route('admin.inventory.products') }}" class="btn btn-secondary btn-sm rounded-pill">Reset</a>
                        </div>
                    </form>

                    {{-- Bulk toolbar --}}
                    <div id="bulk-toolbar" class="align-items-center gap-2 mb-3 p-2 bg-light rounded">
                        <span id="selected-count" class="fw-bold text-secondary me-2">0 selected</span>
                        <button class="btn btn-danger btn-sm" id="bulk-delete-btn"><i class="fe-trash-2"></i> Delete Selected</button>
                        <button class="btn btn-success btn-sm" id="bulk-export-btn"><i class="fe-download"></i> Export Selected</button>
                        <button class="btn btn-outline-secondary btn-sm ms-2" id="bulk-clear-btn">Clear</button>
                    </div>

                    <div id="print-area">
                    <div class="table-responsive">
                        <table class="table table-hover table-centered mb-0" id="inv-table">
                            <thead>
                                <tr>
                                    <th style="width:36px"><input type="checkbox" id="check-all"></th>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Category</th>
                                    <th>Stock</th>
                                    <th>Purchase Price</th>
                                    <th>Sale Price</th>
                                    <th>Status</th>
                                    <th>Save</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $p)
                                <tr data-id="{{ $p->id }}">
                                    <td><input type="checkbox" class="row-check" value="{{ $p->id }}"></td>
                                    <td>{{ $products->firstItem() + $loop->index }}</td>
                                    <td>{{ $p->name }}</td>
                                    <td><code>{{ $p->product_code }}</code></td>
                                    <td>{{ $p->category ? $p->category->name : '—' }}</td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm inline-field stock-field"
                                            data-field="stock" value="{{ $p->stock }}" min="0" style="width:80px">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm inline-field purchase-field"
                                            data-field="purchase_price" value="{{ $p->purchase_price }}" min="0" style="width:90px">
                                    </td>
                                    <td>{{ $p->new_price }}</td>
                                    <td>
                                        @if($p->status)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-xs btn-primary save-row-btn" data-id="{{ $p->id }}">
                                            <i class="fe-save"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="10" class="text-center text-muted py-4">No products found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    </div>{{-- /print-area --}}

                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            Showing {{ $products->firstItem() }}–{{ $products->lastItem() }} of {{ $products->total() }}
                        </small>
                        {{ $products->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- Import Modal --}}
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Inventory</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.inventory.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small mb-2">Upload .xlsx, .xls, or .csv. Columns: <code>name, product_code, stock, purchase_price</code>. Matched by product_code or name. Only existing products are updated.</p>
                    <a href="{{ route('admin.inventory.sample') }}" class="btn btn-sm btn-outline-secondary mb-3">
                        <i class="fe-download"></i> Download Sample CSV
                    </a>
                    <div class="mb-3">
                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning"><i class="fe-upload"></i> Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function(){

    // Realtime search debounce
    var timer = null;
    $('#search-input').on('input', function(){
        clearTimeout(timer);
        timer = setTimeout(function(){ $('#filter-form').submit(); }, 400);
    });

    // Auto-submit dropdowns
    $('#filter-form select').on('change', function(){ $('#filter-form').submit(); });

    // Checkbox select all
    $('#check-all').on('change', function(){
        $('.row-check').prop('checked', $(this).is(':checked'));
        updateToolbar();
    });
    $(document).on('change', '.row-check', updateToolbar);

    function updateToolbar(){
        var count = $('.row-check:checked').length;
        if(count > 0){
            $('#bulk-toolbar').addClass('visible');
            $('#selected-count').text(count + ' selected');
        } else {
            $('#bulk-toolbar').removeClass('visible');
        }
    }

    $('#bulk-clear-btn').on('click', function(){
        $('.row-check, #check-all').prop('checked', false);
        updateToolbar();
    });

    // Bulk delete
    $('#bulk-delete-btn').on('click', function(){
        var ids = $('.row-check:checked').map(function(){ return $(this).val(); }).get();
        if(!ids.length) return;
        if(!confirm('Delete ' + ids.length + ' product(s)? This cannot be undone.')) return;
        $.ajax({
            type: 'POST',
            url: '{{ route('admin.inventory.bulk_delete') }}',
            data: { ids: ids, _token: '{{ csrf_token() }}' },
            success: function(res){
                if(res.status === 'success'){
                    ids.forEach(function(id){ $('tr[data-id="'+id+'"]').remove(); });
                    $('.row-check, #check-all').prop('checked', false);
                    updateToolbar();
                    toastr.success(res.message);
                }
            }
        });
    });

    // Bulk export
    $('#bulk-export-btn').on('click', function(){
        var ids = $('.row-check:checked').map(function(){ return $(this).val(); }).get();
        if(!ids.length) return;
        window.location.href = '{{ route('admin.inventory.bulk_export') }}?ids=' + ids.join(',');
    });

    // PDF export
    window.exportPdf = function(){ window.print(); };

    // Inline save
    $(document).on('click', '.save-row-btn', function(){
        var btn = $(this);
        var row = btn.closest('tr');
        var id  = btn.data('id');
        var stock    = row.find('.stock-field').val();
        var purchase = row.find('.purchase-field').val();

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            type: 'POST',
            url: '{{ route('admin.inventory.update') }}',
            data: { id: id, stock: stock, purchase_price: purchase, _token: '{{ csrf_token() }}' },
            success: function(res){
                if(res.status === 'success'){
                    toastr.success('Saved.');
                }
            },
            error: function(){
                toastr.error('Save failed.');
            },
            complete: function(){
                btn.prop('disabled', false).html('<i class="fe-save"></i>');
            }
        });
    });

});
</script>
@endsection
