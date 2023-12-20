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
                {{-- card to Service Requests --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="">{{ __('Service Requests') }}</h3>
                    </div>
                    <div class="card-body">
                        @if (count($requests) > 0)
                            {{-- table requests --}}
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr class="text-center">
                                            <th>#</th>
                                            <th>{{ __('Service') }}</th>
                                            <th>{{ __('Company name') }}</th>
                                            <th>{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($requests as $request)
                                            <tr>
                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                <td class="text-center">{{ app()->getLocale()=='ar'?$request->plan->PlanTitleAr:$request->plan->PlanTitle }}</td>
                                                <td class="text-center">{{ $request->company_name }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('service-request.show', $request->id) }}"
                                                        class="btn btn-primary btn-sm">{{ __('View') }}</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
