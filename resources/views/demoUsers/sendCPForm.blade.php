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
                    <form method="POST" action="{{ route('demo.sendMailToUser') }}">
                        @csrf
                        {{-- show errors --}}
                        {{-- @if ($errors->any())
                        {!! implode('', $errors->all('<li class="text text-danger">:message</li>')) !!}
                        @endif --}}
                        <div class="container">
                            <div class="row">
                                {{-- Client name optional --}}
                                <div class="form-group col-sm-12">
                                    <label for="client_email" class="col-form-label text-md-right">{{ __('Client Email')
                                        }}</label>
                                    <input id="email" type="email" class="form-control" name="email"
                                        value="{{ old('email') }}">
                                    <small class="form-text text-muted">{{ __('Required') }}</small>
                                    {{-- validation --}}
                                    @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group col-sm-12">
                                <button type="submit" class="btn btn-primary">{{ __('Send CP') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
