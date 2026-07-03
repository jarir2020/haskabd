@extends('backEnd.layouts.master') 
@section('title','Courier API')
@section('css')
<style>
  .increment_btn,
  .remove_btn {
    margin-top: -17px;
    margin-bottom: 10px;
  }
</style>
<link href="{{asset('public/backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('public/backEnd')}}/assets/libs/summernote/summernote-lite.min.css" rel="stylesheet" type="text/css" />
@endsection @section('content')
<div class="container-fluid">
  <!-- start page title -->
  <div class="row">
    <div class="col-12">
      <div class="page-title-box">
        <h4 class="page-title">Steadfast Courier</h4>
      </div>
    </div>
  </div>
  <!-- end page title -->
  <div class="row justify-content-center">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <form action="{{route('courierapi.update')}}" method="POST" class="row" data-parsley-validate="" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{$steadfast->id}}">
            <div class="col-sm-12">
              <div class="form-group mb-3">
                <label for="steadfast_url" class="form-label">API URL *</label>
                <input type="text" class="form-control @error('url') is-invalid @enderror" name="url" value="{{ $steadfast->url }}" id="steadfast_url" required="" />
                <small class="text-muted">e.g. https://portal.steadfast.com.bd/api/v1/create_order</small>
                @error('url')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
            </div>
            <!-- col-end -->
            <div class="col-sm-6">
              <div class="form-group mb-3">
                <label for="api_key" class="form-label">API key *</label>
                <input type="text" class="form-control @error('api_key') is-invalid @enderror" name="api_key" value="{{ $steadfast->api_key}}" id="api_key" required="" />
                @error('api_key')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
            </div>
            <!-- col-end -->
            <div class="col-sm-6">
              <div class="form-group mb-3">
                <label for="secret_key" class="form-label">Secret key *</label>
                <input type="text" class="form-control @error('secret_key') is-invalid @enderror" name="secret_key" value="{{ $steadfast->secret_key }}" id="secret_key" required="" />
                @error('secret_key')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
            </div>
            <!-- col-end -->

            <!-- col-end -->
            <div class="col-sm-3 mb-3">
              <div class="form-group">
                <label for="status" class="d-block">Status</label>
                <label class="switch">
                  <input type="checkbox" value="1" @if($steadfast->status==1)checked @endif name="status" />
                  <span class="slider round"></span>
                </label>
                @error('status')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
            </div>
            <!-- col end -->

            <div>
              <input type="submit" class="btn btn-success" value="Submit" />
            </div>
          </form>
        </div>
        <!-- end card-body-->
      </div>
      <!-- end card-->
    </div>
    <!-- end col-->
  </div>
  <!-------------new-start------------>
  <!-- start page title -->
  <div class="row">
    <div class="col-12">
      <div class="page-title-box">
        <h4 class="page-title">Pathao Courier</h4>
      </div>
    </div>
  </div>
  <!-- end page title -->
  <div class="row justify-content-center">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <form action="{{route('courierapi.update')}}" method="POST" class="row" data-parsley-validate="" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{ $pathao->id}}">

            {{-- API URL --}}
            <div class="col-sm-12">
              <div class="form-group mb-3">
                <label class="form-label">API Base URL *</label>
                <input type="text" class="form-control" name="url" value="{{ $pathao->url }}" required />
                <small class="text-muted">e.g. https://api-hermes.pathao.com/aladdin</small>
              </div>
            </div>

            {{-- Client ID (api_key) + Client Secret (secret_key) --}}
            <div class="col-sm-6">
              <div class="form-group mb-3">
                <label class="form-label">Client ID *</label>
                <input type="text" class="form-control" name="api_key" value="{{ $pathao->api_key }}" placeholder="Pathao Client ID" required />
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group mb-3">
                <label class="form-label">Client Secret *</label>
                <input type="text" class="form-control" name="secret_key" value="{{ $pathao->secret_key }}" placeholder="Pathao Client Secret" required />
              </div>
            </div>

            {{-- Username + Password --}}
            <div class="col-sm-6">
              <div class="form-group mb-3">
                <label class="form-label">Username (Email) *</label>
                <input type="email" class="form-control" name="username" value="{{ $pathao->username }}" placeholder="your@email.com" required />
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group mb-3">
                <label class="form-label">Password *</label>
                <input type="password" class="form-control" name="password" value="{{ $pathao->password }}" placeholder="Pathao account password" required />
              </div>
            </div>

            {{-- Access Token (read-only, refreshed via button) --}}
            <div class="col-sm-12">
              <div class="form-group mb-3">
                <label class="form-label">Access Token</label>
                <div class="input-group">
                  <input type="text" id="pathao-token-display" class="form-control font-monospace"
                         name="token" value="{{ $pathao->token }}" placeholder="Click 'Refresh Token' to generate" />
                  <button type="button" id="pathao-refresh-btn" class="btn btn-warning">
                    <i data-feather="refresh-cw"></i> Refresh Token
                  </button>
                </div>
                <small class="text-muted">Saved automatically when refreshed. Valid for ~90 days.</small>
                <div id="pathao-token-msg" class="mt-1"></div>
              </div>
            </div>

            {{-- Status --}}
            <div class="col-sm-3 mb-3">
              <div class="form-group">
                <label class="d-block">Status</label>
                <label class="switch">
                  <input type="checkbox" value="1" @if($pathao->status==1) checked @endif name="status" />
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="col-12">
              <input type="submit" class="btn btn-success" value="Save Settings" />
            </div>
          </form>
        </div>
        <!-- end card-body-->
      </div>
      <!-- end card-->
    </div>
    <!-- end col-->
  </div>
  
</div>
@endsection @section('script')
<script src="{{asset('public/backEnd/')}}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-validation.init.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/libs/select2/js/select2.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-advanced.init.js"></script>
<script>
// Pathao Refresh Token -- added 2026-04-15
$('#pathao-refresh-btn').on('click', function () {
    var btn = $(this);
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Fetching…');
    $('#pathao-token-msg').html('');

    $.ajax({
        type: 'POST',
        url: '{{ route("pathao.refresh_token") }}',
        data: { _token: '{{ csrf_token() }}' },
        success: function (res) {
            $('#pathao-token-display').val('Token saved — reload page to see it.');
            var exp = res.expires_in ? ' Expires in ' + Math.round(res.expires_in / 86400) + ' days.' : '';
            $('#pathao-token-msg').html('<span class="text-success"><i class="fe-check-circle"></i> ' + res.message + exp + '</span>');
        },
        error: function (xhr) {
            var msg = xhr.responseJSON?.error ?? 'Something went wrong.';
            $('#pathao-token-msg').html('<span class="text-danger"><i class="fe-alert-circle"></i> ' + msg + '</span>');
        },
        complete: function () {
            btn.prop('disabled', false).html('<i data-feather="refresh-cw"></i> Refresh Token');
            feather.replace();
        }
    });
});
</script>
<!-- Plugins js -->
<script src="{{asset('public/backEnd/')}}/assets/libs//summernote/summernote-lite.min.js"></script>
<script>
  $(".summernote").summernote({
    placeholder: "Enter Your Text Here",
  });
</script>
<script type="text/javascript">
  $(document).ready(function () {
    $(".btn-increment").click(function () {
      var html = $(".clone").html();
      $(".increment").after(html);
    });
    $("body").on("click", ".btn-danger", function () {
      $(this).parents(".control-group").remove();
    });
  });
</script>
<script type="text/javascript">
  $(document).ready(function () {
    $(".increment_btn").click(function () {
      var html = $(".clone_price").html();
      $(".increment_price").after(html);
    });
    $("body").on("click", ".remove_btn", function () {
      $(this).parents(".increment_control").remove();
    });

    $(".select2").select2();
  });
</script>
@endsection