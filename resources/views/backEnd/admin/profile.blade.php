@extends('backEnd.layouts.master')
@section('title', 'Update Profile')
@section('css')
<link href="{{asset('public/backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
@endsection
@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Update Profile</li>
                    </ol>
                </div>
                <h4 class="page-title">Update Profile</h4>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-body">

                    {{-- Profile picture preview --}}
                    <div class="text-center mb-4">
                        <img id="profile-preview"
                             src="{{ asset($user->image ?? 'public/uploads/default/default.png') }}"
                             class="rounded-circle"
                             style="width:100px;height:100px;object-fit:cover;border:3px solid #6366f1;"
                             alt="Profile Picture">
                    </div>

                    <form action="{{ route('admin.profile.update') }}" method="POST"
                          class="row" enctype="multipart/form-data" data-parsley-validate="">
                        @csrf

                        <div class="col-sm-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Name *</label>
                                <input type="text" name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group mb-3">
                                <label class="form-label">Profile Picture <small class="text-muted">(jpg, png, webp — max 2MB)</small></label>
                                <input type="file" name="image" id="profile-image-input"
                                       class="form-control @error('image') is-invalid @enderror"
                                       accept="image/*">
                                @error('image')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-sm-12 mt-2">
                            <input type="submit" class="btn btn-success" value="Save Changes">
                            <a href="{{ route('change_password') }}" class="btn btn-outline-secondary ms-2">Change Password</a>
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
<script>
    document.getElementById('profile-image-input').addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            document.getElementById('profile-preview').src = URL.createObjectURL(file);
        }
    });
</script>
@endsection
