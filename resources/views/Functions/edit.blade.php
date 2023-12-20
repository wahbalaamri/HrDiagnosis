{{-- extends --}}
@extends('layouts.main')

{{-- content --}}
@section('content')
    {{-- container --}}
    <div class="container pt-5 mt-5">
        <div class="">
            <div class="col-12 mt-5 pt-5 {{ App()->getLocale()=='ar' ? 'custom-fixed-top-rtl' : 'custom-fixed-top' }}">
                <!-- side bar menu -->
                @include('layouts.sidebar')
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Functions</h3>
                    </div>
                    <div class="card-body">
                        <p>Functions</p>
                        <a href="{{ route('functions.create') }}">create</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
