{{-- extends --}}
@extends('layouts.main')
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/CircularProgress.css') }}">
@endpush
{{-- content --}}
@section('content')
{{-- container --}}
<div class="container pt-5 mt-5">
    <div class="">
        <div class="col-12 mt-5 pt-5">
            <!-- side bar menu -->
            @include('layouts.sidebar')
        </div>
        <div class="col-12" id="finalResult">
            <div class="card">
                {{-- card header --}}
                <div class="card-header">
                    <h3>{{ __('Survey Response rate') }}</h3>
                </div>
                <div class="card-body text-center">
                    {{-- survey divs --}}
                    <div class="w-50">
                        @if($Resp_overAll_res>0)
                        <div class="col-5 text-end function-lable mr-3">{{ __('Total Answers') }} {{ $overAll_res }} {{
                            __('out of') }} {{
                            $Resp_overAll_res }}</div>
                        <div class="col-9 text-start function-progress">
                            <div class="progress" style="height: 31px">
                                <div class="progress-bar @if(($overAll_res/$Resp_overAll_res)<0.5) bg-danger @elseif(($overAll_res/$Resp_overAll_res)==1) bg-success @else bg-warning @endif"
                                    role="progressbar"
                                    style="width: {{ ($overAll_res/$Resp_overAll_res)*100 }}%; font-size: 1rem"
                                    aria-valuenow="{{ ($overAll_res/$Resp_overAll_res)*100 }}" aria-valuemin="0"
                                    aria-valuemax="100">{{ number_format(($overAll_res/$Resp_overAll_res)*100) }}%</div>
                            </div>
                        </div>
                        @endif
                        @if($prop_leadersResp>0)
                        <div class="col-5 text-end function-lable mr-3">{{ __('Total Leaders Answers') }} {{
                            $leaders_res }} {{ __('out of') }}
                            {{ $prop_leadersResp }}</div>
                        <div class="col-9 text-start function-progress">
                            <div class="progress" style="height: 31px">
                                <div class="progress-bar @if(($leaders_res/$prop_leadersResp)<0.5) bg-danger @elseif(($leaders_res/$prop_leadersResp)==1) bg-success @else bg-warning @endif"
                                    role="progressbar"
                                    style="width: {{ ($leaders_res/$prop_leadersResp)*100 }}%; font-size: 1rem"
                                    aria-valuenow="{{ ($leaders_res/$prop_leadersResp)*100 }}" aria-valuemin="0"
                                    aria-valuemax="100">{{ number_format(($leaders_res/$prop_leadersResp)*100) }}%</div>
                            </div>
                        </div>
                        @endif
                        @if($prop_hrResp>0)
                        <div class="col-5 text-end function-lable mr-3">{{ __('Total HR Answers') }} {{ $hr_res }} {{
                            __('out of') }} {{
                            $prop_hrResp }}</div>
                        <div class="col-9 text-start function-progress">
                            <div class="progress" style="height: 31px">
                                <div class="progress-bar @if(($hr_res/$prop_hrResp)<0.5) bg-danger @elseif(($hr_res/$prop_hrResp)==1) bg-success @else bg-warning @endif"
                                    role="progressbar" style="width: {{ ($hr_res/$prop_hrResp)*100 }}%; font-size: 1rem"
                                    aria-valuenow="{{ ($hr_res/$prop_hrResp)*100 }}" aria-valuemin="0"
                                    aria-valuemax="100">{{ number_format(($hr_res/$prop_hrResp)*100) }}%</div>
                            </div>
                        </div>
                        @endif
                        @if($prop_empResp>0)
                        <div class="col-5 text-end function-lable mr-3">{{ __('Total Employee Answers') }} {{ $emp_res
                            }} {{ __('out of') }} {{
                            $prop_empResp }}</div>

                        <div class="col-9 text-start function-progress">
                            <div class="progress" style="height: 31px">
                                <div class="progress-bar @if(($emp_res/$prop_empResp)<0.5) bg-danger @elseif(($emp_res/$prop_empResp)==1) bg-success @else bg-warning @endif"
                                    role="progressbar"
                                    style="width: {{ ($emp_res/$prop_empResp)*100 }}%; font-size: 1rem"
                                    aria-valuenow="{{ ($emp_res/$prop_empResp)*100 }}" aria-valuemin="0"
                                    aria-valuemax="100">{{ number_format(($emp_res/$prop_empResp)*100) }}%</div>
                            </div>
                        </div>
                        @endif
                        {{-- end survey divs --}}
                    </div>
                </div>
            </div>
            {{-- =========================================================================================== --}}
            <div id="Function" class="card mt-4" style="letter-spacing: 0.065rem;">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Result overview - by Functions and Practices') }}</h3>
                </div>
                <div class="card-body text-capitalize">
                    <div class="row">
                        <div class="col-1" style="max-width: 45px;"></div>
                        <div class="col-11">
                            <div class="col-{{ count($functions) }} {{ app()->getLocale()=='ar'?'text-right':'text-left' }} h3 text-white p-3 bg-info"
                                style="border-radius: 45px 45px 45px 45px;width: 89%; -webkit-box-shadow: 0px 0px 5px 1px #ABABAB;
                        box-shadow: 0px 0px 5px 1px #ABABAB;">{{ __('Key functions') }}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-1" style="max-width: 45px;"></div>
                        <div class="col-11">
                            <div class="row  padding-left-10px">
                                @foreach ($functions as $function )
                                <div class="text-center text-white m-1 bg-info" style="width:10.5%; border-radius: 10px; -webkit-box-shadow: 0px 0px 5px 1px #ABABAB;
                        box-shadow: 0px 0px 5px 1px #ABABAB; font-size: 0.79rem">
                                    {{ app()->getLocale()=='ar'?$function->FunctionTitleAr:$function->FunctionTitle }}
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-1" style="background-color: #DCE6F2;    background-color: #DCE6F2;
                        writing-mode: vertical-lr;
                        transform: rotate(180deg);
                        min-width: 19px;
                        max-width: 45px;
                        display: flex;
                        align-items: center;
                        justify-content: center;color:#376092; font-size: 1.5rem; font-weight: bold">{{ __('Practices')
                            }}</div>
                        <div class="col-11">
                            <div class="row" style="width: 100%">
                                @foreach ($functions as $function )
                                <?php $firstofFirstLoop= $loop->first ; ?>
                                <div class="col-1 m-1 justify-content-center pb-1 pt-1"
                                    style="width: 10.5%; font-size: 0.79rem">
                                    @foreach( $overall_Practices as $overall_Practice)
                                    @if ( $overall_Practice['function_id'] == $function->id)
                                    <div class="text-center @if(!$loop->first) mt-1 p-2 @endif @if($firstofFirstLoop) p-2 @else p-2 m-1 @endif @if($overall_Practice['weight']<=0.6) bg-danger text-white @elseif (($overall_Practice['weight']>0.6)&&($overall_Practice['weight']<=0.8)) bg-warning text-black @else bg-success text-white @endif"
                                        style=" width:125%; border-radius: 10px; -webkit-box-shadow: 5px 5px 20px 5px #ABABAB;
                                box-shadow: 5px 5px 20px 5px #ABABAB;">
                                        {{ $overall_Practice['name'] }} {{-- {{ $overall_Practice['weightz'] }} --}}
                                    </div>
                                    @endif
                                    @endforeach
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                {{-- card footer --}}
                <div class="card-footer">
                    <button id="FunctionDownload" onclick="downloadResult('Function','Function')"
                        class="btn btn-success mt-1">{{ __('Download') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
{{-- scripts --}}
