@extends('backEnd.layouts.master')
@section('title', 'AI Chat Settings')
@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">AI Chat Settings</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.ai_chat.update') }}" method="POST" class="row" data-parsley-validate="">
                        @csrf

                        <div class="col-sm-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Provider</label>
                                <select class="form-control" name="provider">
                                    <option value="openrouter" @if(($setting->provider ?? 'openrouter') == 'openrouter') selected @endif>OpenRouter</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group mb-3">
                                <label class="form-label">
                                    Model
                                    <small class="text-muted ms-1">— live list from OpenRouter, sorted by context size</small>
                                </label>
                                <select class="form-control" name="model" required>
                                    @foreach($freeModels as $m)
                                        <option value="{{ $m['id'] }}" @if(($setting->model ?? '') === $m['id']) selected @endif>
                                            {{ $m['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group mb-3">
                                <label class="form-label">API Key <small class="text-muted">(leave blank to use key from .env)</small></label>
                                <input type="password" class="form-control" name="api_key"
                                       placeholder="{{ $setting->api_key ? '••••••••••••••••' : 'Using key from .env' }}"
                                       autocomplete="new-password">
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group mb-3">
                                <label class="form-label">Custom Instruction <small class="text-muted">(appended to site context — tell the AI extra info about your shop)</small></label>
                                <textarea class="form-control" name="custom_instruction" rows="4"
                                          placeholder="e.g. We offer free delivery on orders above 1000 BDT. Return policy is 7 days. Our products are 100% organic and certified.">{{ $setting->custom_instruction ?? '' }}</textarea>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group mb-3">
                                <label class="form-label">Max Response Length (tokens)</label>
                                <input type="number" class="form-control" name="max_tokens"
                                       value="{{ $setting->max_tokens ?? 300 }}" min="50" max="2000" required>
                                <small class="text-muted">50–2000. ~300 ≈ 2–3 sentences.</small>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group mb-3">
                                <label class="form-label d-block">Chat Widget Status</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="status" value="1"
                                           id="chat_status" @if(($setting->status ?? true)) checked @endif>
                                    <label class="form-check-label" for="chat_status">Enable on frontend</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <input type="submit" class="btn btn-success" value="Save AI Chat Settings">
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('script')
<script src="{{ asset('public/backEnd') }}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{ asset('public/backEnd') }}/assets/js/pages/form-validation.init.js"></script>
@endsection
