@extends('backEnd.layouts.master')
@section('title', 'FraudBD Settings')
@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active">FraudBD Settings</li>
                    </ol>
                </div>
                <h4 class="page-title">FraudBD Settings</h4>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Get your API key from <a href="https://fraudbd.com" target="_blank">fraudbd.com</a>.
                        It is used to check courier fraud status in the Orders page.
                    </p>

                    <form action="{{ route('admin.fraud.setting.update') }}" method="POST"
                          class="row" data-parsley-validate="">
                        @csrf

                        <div class="col-sm-12">
                            <div class="form-group mb-3">
                                <label class="form-label">FraudBD API Key *</label>
                                <input type="text" name="api_key"
                                       class="form-control @error('api_key') is-invalid @enderror"
                                       value="{{ old('api_key', $apiKey) }}"
                                       placeholder="Enter your FraudBD Bearer token" required>
                                @error('api_key')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-sm-12 mt-2">
                            <input type="submit" class="btn btn-success" value="Save API Key">
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('script')
<script src="{{asset('public/backEnd')}}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{asset('public/backEnd')}}/assets/js/pages/form-validation.init.js"></script>
@endsection
