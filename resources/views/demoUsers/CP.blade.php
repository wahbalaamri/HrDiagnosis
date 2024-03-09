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
                    {{-- show three buttons on one row to take survey --}}
                    <div class="container">
                        <div class="row">
                            <div class="col-4">
                                <a href="{{ route('demo.survey', [$id,1]) }}" class="btn btn-primary btn-block">{{
                                    __('Take Survey as Manager') }}</a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('demo.survey', [$id,2]) }}" class="btn btn-primary btn-block">{{
                                    __('Take Survey as HR') }}</a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('demo.survey', [$id,3]) }}" class="btn btn-primary btn-block">{{
                                    __('Take Survey as Normal Emolpyee') }}</a>
                            </div>
                        </div>
                        {{-- Get Results --}}
                        <div class="row mt-3">
                            <div class="col-12">
                                <a href="{{ route('demo.result', $id) }}" class="btn btn-primary btn-block">{{ __('Get
                                    Results') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
