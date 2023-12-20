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
            @if(count($partnerShipPlans)>0)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Plans') }}</h3>
                </div>
                <div class="card-body">
                    <div class="accordion" id="accordionExample">
                        @foreach($partnerShipPlans as $partnerShipPlan)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $loop->iteration }}">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse{{ $loop->iteration }}" aria-expanded="{{ $loop->iteration==1?'true':'false' }}" aria-controls="collapse{{ $loop->iteration }}">
                                    {{ app()->getLocale()=='ar'?$partnerShipPlan->PlanTitleAr:$partnerShipPlan->PlanTitle }}
                                </button>
                            </h2>
                            <div id="collapse{{ $loop->iteration }}" class="accordion-collapse collapse {{ $loop->iteration==1?'show':'' }}" aria-labelledby="heading{{ $loop->iteration }}"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <div class="list-group w-50">
                                       {{--  <div class="list-group-item" style="display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        height: 5rem;color: #eead35">
                                            <div class="h3">
                                                {{ $partnerShipPlan->PlanTitle }}
                                            </div>
                                        </div>--}}
                                        <div class="list-group-item" style="background-color: #fff; color:#eead35">
                                            <span class="font-weight-bold float-start">{{ __('Objective') }}</span>
                                            <span class="float-end">{!! app()->getLocale()=='ar'?$partnerShipPlan->ObjectiveAr:$partnerShipPlan->Objective !!}</span>
                                        </div>
                                        <div class="list-group-item" style="background-color: #fff; color:#eead35"><span class="font-weight-bold float-start">{{
                                                __('Process') }}</span>
                                            <span class="float-end">{!! app()->getLocale()=='ar'?$partnerShipPlan->ProcessAr:$partnerShipPlan->Process !!}</span>
                                        </div>
                                        <div class="list-group-item" style="display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        height: 5rem; background-color: #fff; color:#eead35"><a
                                                href="{{ route('partner-ship-plans.show', $partnerShipPlan->id) }}"
                                                class="btn btn-primary" >{{ __('View More Details') }}</a></div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        @endforeach
                    </div>

                </div>
            </div>
            @else
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Plans') }}</h3>
                </div>
                <div class="card-body">
                    <p>{{ __('Plans') }}</p>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('No Plans') }}</h3>
                                </div>
                                <div class="card-body">
                                    <p>{{ __('No Plans') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @if(count($plans)>0)
            <div class="card mt-3">
                <div class="card-header">
                    <span class="card-title h3">{{__('Remote Plans (ON')}}</span><span class="h5"> <a href="https://www.hrfactoryapp.com"
                            class="badge text-bg-success">{{ __('HRFactoryApp') }}</a></span> <span class="card-title h3">{{ __('Database)') }}</span>
                </div>
                <div class="card-body">
                    <div class="row">

                        @foreach ($plans as $plan)
                        <div class="col-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">{{ app()->getLocale()=='ar'?$plan->PlanTitleAr:$plan->PlanTitle }}</h3>
                                </div>
                                <div class="card-body">
                                    <p>{!! app()->getLocale()=='ar'?$plan->ObjectiveAr:$plan->Objective !!}</p>
                                    <p>{{app()->getLocale()=='ar'? $plan->ProcessAr: $plan->Process }}</p>
                                    <p>{{app()->getLocale()=='ar'? $plan->ReportAr: $plan->Report }}</p>
                                    <p>{{app()->getLocale()=='ar'? $plan->DeliveryModeAr: $plan->DeliveryMode }}</p>
                                    <p>{!! app()->getLocale()=='ar'?$plan->LimitationsAr:$plan->Limitations !!}</p>
                                    <p>
                                        @switch($plan->Audience)
                                        @case(1)
                                        {{ __('Only HR Factory App Members') }}
                                        @break

                                        @case(2)
                                        {{ __('Only HR Employees') }}
                                        @break

                                        @case(3)
                                        {{ __('Only Employees') }}
                                        @break

                                        @case(4)
                                        {{ __('Only Managers') }}
                                        @break

                                        @case(5)
                                        {{ __('Only HR Employees & Employees') }}
                                        @break

                                        @case(6)
                                        {{ __('Only Managers & Employees') }}
                                        @break

                                        @case(7)
                                        {{ __('Only Managers & HR Employees') }}
                                        @break

                                        @case(8)
                                        {{ __('Only Managers, HR Employees & Employees') }}
                                        @break

                                        @case(9)
                                        {{ __('All Employees') }}
                                        @break

                                        @case(10)
                                        {{ __('Public') }}
                                        @break

                                        @default
                                        Default case...
                                        @endswitch
                                    </p>
                                    <p><a href="https://www.hrfactoryapp.com{{ $plan->TamplatePath }}"><span
                                                class="badge text-bg-secondary">{{ __('View Tamplate') }}</span></a></p>
                                    <p>{{ $plan->Price }} OMR</p>
                                    <p>
                                        @switch($plan->PaymentMethod)
                                        @case(1)
                                        {{ __('Free') }}
                                        @break

                                        @case(2)
                                        {{ __('On Service Required Payment') }}
                                        @break

                                        @case(3)
                                        {{ __('Subscribe') }}
                                        @break

                                        @default
                                        Default case...
                                        @endswitch
                                    </p>
                                    <p>{{ $plan->Status ? __('Active') : __('Inactive') }}</p>
                                    <a href="{{ route('partner-ship-plans.getPlan',$plan->id) }}"
                                        class="btn btn-primary">{{ __('Download') }}</a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            @endif
        </div>
    </div>
</div>
@endsection
