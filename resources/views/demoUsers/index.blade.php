@extends('layouts.main')
@section('content')
<div class="container pt-5 mt-5">
    <div class="row">
        <div class="col-12">
            {{-- centered card showing registration form --}}
            <div class="card mx-auto col-9">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Register for Demo') }}</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('demo.store') }}">
                        @csrf
                        <div class="container">
                            <div class="row">
                                {{-- Client name optional --}}
                                <div class="form-group col-md-6 col-sm-12">
                                    <label for="client_name" class="col-form-label text-md-right">{{ __('Client Name')
                                        }}</label>
                                    <input id="client_name" type="text" class="form-control" name="client_name"
                                        value="{{ old('client_name') }}">
                                    <small class="form-text text-muted">{{ __('Optional') }}</small>
                                </div>
                                {{-- Client focal_point_name optional --}}
                                <div class="form-group col-md-6 col-sm-12">
                                    <label for="focal_point_name" class="col-form-label text-md-right">{{ __('Focal
                                        Point Name')
                                        }}</label>
                                    <input id="focal_point_name" type="text" class="form-control"
                                        name="focal_point_name" value="{{ old('focal_point_name') }}">
                                    <small class="form-text text-muted">{{ __('Optional') }}</small>
                                </div>
                                {{-- Client Email required --}}
                                <div class="form-group col-md-6 col-sm-12">
                                    <label for="client_email" class="col-form-label text-md-right">{{ __('Client Email')
                                        }}</label>
                                    <input id="client_email" type="email" class="form-control" name="client_email"
                                        value="{{ old('client_email') }}" required>
                                    <small class="form-text text-muted">{{ __('Required') }}</small>
                                </div>
                                {{-- Client mobile optional --}}
                                <div class="form-group col-md-6 col-sm-12">
                                    <label for="client_mobile" class="col-form-label text-md-right">{{ __('Client
                                        Mobile')
                                        }}</label>
                                    <input id="client_mobile" type="text" class="form-control" name="client_mobile"
                                        value="{{ old('client_mobile') }}">
                                    <small class="form-text text-muted">{{ __('Optional') }}</small>
                                </div>
                                {{-- Client country optional --}}
                                <div class="form-group col-md-6 col-sm-12">
                                    <label for="country" class="col-form-label text-md-right">{{ __('Country')}}</label>
                                    <input id="country" type="text" class="form-control" name="country"
                                        value="{{ old('country') }}">
                                    <small class="form-text text-muted">{{ __('Optional') }}</small>
                                </div>
                            </div>
                            <div class="row">
                                {{-- submit button --}}
                                <div class="form-group col-sm-12">
                                    <button type="submit" class="btn btn-primary float-end">
                                        {{ __('Register') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
