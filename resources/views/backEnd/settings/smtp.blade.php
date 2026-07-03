@extends('backEnd.layouts.master')
@section('title', 'SMTP Settings')

@section('content')
<div class="container-fluid">

    {{-- page title --}}
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">SMTP Settings</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">

                    <form action="{{ route('admin.smtp.update') }}" method="POST" class="row" data-parsley-validate="">
                        @csrf

                        <div class="col-sm-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Mail Mailer</label>
                                <select class="form-control" name="MAIL_MAILER">
                                    <option value="smtp"  @if($smtp['MAIL_MAILER'] == 'smtp')  selected @endif>smtp</option>
                                    <option value="sendmail" @if($smtp['MAIL_MAILER'] == 'sendmail') selected @endif>sendmail</option>
                                    <option value="log"   @if($smtp['MAIL_MAILER'] == 'log')   selected @endif>log (debug)</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Encryption</label>
                                <select class="form-control" name="MAIL_ENCRYPTION">
                                    <option value="tls" @if($smtp['MAIL_ENCRYPTION'] == 'tls') selected @endif>TLS</option>
                                    <option value="ssl" @if($smtp['MAIL_ENCRYPTION'] == 'ssl') selected @endif>SSL</option>
                                    <option value=""    @if($smtp['MAIL_ENCRYPTION'] == '')    selected @endif>None</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-8">
                            <div class="form-group mb-3">
                                <label class="form-label">Mail Host *</label>
                                <input type="text" class="form-control @error('MAIL_HOST') is-invalid @enderror"
                                       name="MAIL_HOST" value="{{ old('MAIL_HOST', $smtp['MAIL_HOST']) }}" required>
                                @error('MAIL_HOST')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group mb-3">
                                <label class="form-label">Mail Port *</label>
                                <input type="number" class="form-control @error('MAIL_PORT') is-invalid @enderror"
                                       name="MAIL_PORT" value="{{ old('MAIL_PORT', $smtp['MAIL_PORT']) }}" required>
                                @error('MAIL_PORT')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Mail Username (Email) *</label>
                                <input type="email" class="form-control @error('MAIL_USERNAME') is-invalid @enderror"
                                       name="MAIL_USERNAME" value="{{ old('MAIL_USERNAME', $smtp['MAIL_USERNAME']) }}" required>
                                @error('MAIL_USERNAME')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Mail Password</label>
                                <input type="password" class="form-control" name="MAIL_PASSWORD"
                                       value="{{ old('MAIL_PASSWORD', $smtp['MAIL_PASSWORD']) }}"
                                       autocomplete="new-password">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group mb-3">
                                <label class="form-label">From Address *</label>
                                <input type="email" class="form-control @error('MAIL_FROM_ADDRESS') is-invalid @enderror"
                                       name="MAIL_FROM_ADDRESS" value="{{ old('MAIL_FROM_ADDRESS', $smtp['MAIL_FROM_ADDRESS']) }}" required>
                                @error('MAIL_FROM_ADDRESS')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group mb-3">
                                <label class="form-label">From Name *</label>
                                <input type="text" class="form-control @error('MAIL_FROM_NAME') is-invalid @enderror"
                                       name="MAIL_FROM_NAME" value="{{ old('MAIL_FROM_NAME', $smtp['MAIL_FROM_NAME']) }}" required>
                                @error('MAIL_FROM_NAME')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <input type="submit" class="btn btn-success" value="Save SMTP Settings">
                        </div>

                    </form>

                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
    </div>

</div>
@endsection

@section('script')
<script src="{{ asset('public/backEnd') }}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{ asset('public/backEnd') }}/assets/js/pages/form-validation.init.js"></script>
@endsection
