@extends('backEnd.layouts.master')
@section('title','Customer Reviews')

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Customer Reviews</h4>
            </div>
        </div>
    </div>

    {{-- Upload card --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Upload Review Screenshots</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.customer_reviews.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row align-items-end g-3">
                            <div class="col-md-8">
                                <label class="form-label">Select Images <small class="text-muted">(jpg, jpeg, png, webp — max 2MB each, multiple allowed)</small></label>
                                <input type="file" name="images[]" class="form-control" accept=".jpg,.jpeg,.png,.webp" multiple required>
                                @error('images') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                @error('images.*') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fe-upload me-1"></i> Upload
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Image grid --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">All Review Images <span class="badge bg-secondary ms-1">{{ $reviews->count() }}</span></h5>
                    <small class="text-muted">Active images show on the homepage carousel.</small>
                </div>
                <div class="card-body">
                    @if($reviews->isEmpty())
                        <p class="text-muted text-center py-4">No review images yet. Upload some above.</p>
                    @else
                    <div class="row g-3" id="review-grid">
                        @foreach($reviews as $review)
                        <div class="col-6 col-md-4 col-lg-3" id="card-{{ $review->id }}">
                            <div class="card h-100 border">
                                <img src="{{ asset($review->image) }}"
                                     class="card-img-top"
                                     style="object-fit:cover;height:180px;"
                                     alt="Review">
                                <div class="card-body p-2">
                                    <div class="d-flex justify-content-between align-items-center gap-1">
                                        <span class="badge status-badge {{ $review->status ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $review->status ? 'Active' : 'Inactive' }}
                                        </span>
                                        <button class="btn btn-xs btn-outline-secondary btn-toggle"
                                                data-id="{{ $review->id }}"
                                                title="Toggle Status">
                                            <i class="fe-refresh-cw"></i>
                                        </button>
                                        <button class="btn btn-xs btn-danger btn-delete"
                                                data-id="{{ $review->id }}"
                                                title="Delete">
                                            <i class="mdi mdi-close"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('script')
<script>
const csrfToken = '{{ csrf_token() }}';

// Toggle status
$(document).on('click', '.btn-toggle', function () {
    const id  = $(this).data('id');
    const url = '{{ url("admin/customer-reviews") }}/' + id + '/toggle';

    $.post(url, { _token: csrfToken }, function (res) {
        if (res.status === 'success') {
            const badge = $('#card-' + id + ' .status-badge');
            if (res.active) {
                badge.removeClass('bg-secondary').addClass('bg-success').text('Active');
            } else {
                badge.removeClass('bg-success').addClass('bg-secondary').text('Inactive');
            }
        }
    }).fail(function () {
        toastr.error('Failed to update status.');
    });
});

// Delete
$(document).on('click', '.btn-delete', function () {
    const id = $(this).data('id');
    if (!confirm('Delete this review image?')) return;

    const url = '{{ url("admin/customer-reviews") }}/' + id + '/delete';

    $.post(url, { _token: csrfToken }, function (res) {
        if (res.status === 'success') {
            $('#card-' + id).fadeOut(300, function () { $(this).remove(); });
            toastr.success('Image deleted.');
        }
    }).fail(function () {
        toastr.error('Failed to delete image.');
    });
});
</script>
@endsection
