@extends('backEnd.layouts.master')
@section('title', 'Contact Messages')
@section('css')
<style>
#search-input.searching { border-color: #00acc1; box-shadow: 0 0 0 2px rgba(0,172,193,.2); }
</style>
@endsection
@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">
                    Contact Messages
                    @if($unreadCount > 0)
                        <span class="badge bg-warning text-dark ms-1">{{ $unreadCount }} unread</span>
                    @endif
                </h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    {{-- Filters --}}
                    <form method="GET" action="{{ route('admin.contact_messages.index') }}" class="row g-2 mb-3">
                        <div class="col-sm-4">
                            <input type="text" name="search" id="search-input" value="{{ request('search') }}"
                                class="form-control" placeholder="Search name, email, phone, subject..." autocomplete="off">
                        </div>
                        <div class="col-sm-2">
                            <select name="status" class="form-select">
                                <option value="">All Messages</option>
                                <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Unread</option>
                                <option value="read"   {{ request('status') === 'read'   ? 'selected' : '' }}>Read</option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <select name="sort" class="form-select">
                                <option value="latest"  {{ request('sort','latest') === 'latest'  ? 'selected' : '' }}>Newest First</option>
                                <option value="oldest"  {{ request('sort') === 'oldest'  ? 'selected' : '' }}>Oldest First</option>
                                <option value="name"    {{ request('sort') === 'name'    ? 'selected' : '' }}>Name A-Z</option>
                            </select>
                        </div>
                        <div class="col-sm-auto d-flex gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill"><i class="fe-search"></i> Filter</button>
                            <a href="{{ route('admin.contact_messages.index') }}" class="btn btn-secondary rounded-pill">Reset</a>
                        </div>
                    </form>

                    <table class="table table-hover table-centered mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Subject</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($messages as $m)
                            <tr class="{{ $m->is_read ? '' : 'table-warning fw-bold' }}">
                                <td>{{ $messages->firstItem() + $loop->index }}</td>
                                <td>{{ $m->name }}</td>
                                <td>{{ $m->email }}</td>
                                <td>{{ $m->phone }}</td>
                                <td>{{ \Str::limit($m->subject, 40) }}</td>
                                <td>{{ $m->created_at->format('d M Y, h:i A') }}</td>
                                <td>
                                    @if($m->is_read)
                                        <span class="badge bg-secondary">Read</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Unread</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.contact_messages.show', $m->id) }}"
                                       class="btn btn-sm btn-info rounded-pill">
                                        <i class="fe-eye"></i> View
                                    </a>
                                    <form action="{{ route('admin.contact_messages.destroy', $m->id) }}"
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger rounded-pill delete-confirm">
                                            <i class="fe-trash-2"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No messages found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            Showing {{ $messages->firstItem() }}–{{ $messages->lastItem() }} of {{ $messages->total() }} messages
                        </small>
                        {{ $messages->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('script')
<script>
(function(){
    var input  = document.getElementById('search-input');
    var form   = input.closest('form');
    var timer  = null;

    input.addEventListener('input', function(){
        clearTimeout(timer);
        input.classList.add('searching');
        timer = setTimeout(function(){
            input.classList.remove('searching');
            form.submit();
        }, 400);
    });

    // Auto-submit on dropdown change too
    form.querySelectorAll('select').forEach(function(sel){
        sel.addEventListener('change', function(){ form.submit(); });
    });
})();
</script>
@endsection
