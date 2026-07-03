@extends('backEnd.layouts.master')
@section('title', 'View Message')
@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('admin.contact_messages.index') }}" class="btn btn-primary rounded-pill">
                        <i class="fe-arrow-left"></i> Back
                    </a>
                </div>
                <h4 class="page-title">View Message</h4>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">

                    <table class="table table-bordered">
                        <tr>
                            <th width="150">Name</th>
                            <td>{{ $msg->name }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td><a href="mailto:{{ $msg->email }}">{{ $msg->email }}</a></td>
                        </tr>
                        <tr>
                            <th>Phone</th>
                            <td>{{ $msg->phone }}</td>
                        </tr>
                        <tr>
                            <th>Subject</th>
                            <td>{{ $msg->subject }}</td>
                        </tr>
                        <tr>
                            <th>Date</th>
                            <td>{{ $msg->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                        <tr>
                            <th>Message</th>
                            <td style="white-space: pre-wrap;">{{ $msg->message }}</td>
                        </tr>
                    </table>

                    <form action="{{ route('admin.contact_messages.destroy', $msg->id) }}" method="POST" class="mt-3">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger delete-confirm">
                            <i class="fe-trash-2"></i> Delete Message
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>

</div>
@endsection
