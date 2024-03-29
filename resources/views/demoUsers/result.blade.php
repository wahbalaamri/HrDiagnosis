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
            <div class="card mt-4">
                <div class="card-header">
                    <div class="card-title">
                        <h3>{{ $entity }}</h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="card">
                                <div class="mt-3 text-center">
                                    <h3>{{ __('Legend') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-12">
                                            <h4 class="h4 text-center">{{ __('Performance categories & color codes') }}
                                            </h4>
                                            <div class="p-3 m-3 rounded-2 text-white text-center bg-success">High: > 80%
                                            </div>
                                            <div class="p-3 m-3 rounded-2 text-white text-center bg-warning">Medium:
                                                >50% to 80%</div>
                                            <div class="p-3 m-3 rounded-2 text-white text-center bg-danger">Low: <=50%<
                                                    /div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-12">
                                            <h4 class="h4 text-center">
                                                {{ __('Definition of respondent groups') }}
                                            </h4>
                                            <div class="table-responsive">
                                                <table class="table table-borderless">
                                                    <tbody>
                                                        <tr>
                                                            <th>Leadership:</th>
                                                            <td>All managers and leaders who took part in the
                                                                diagnosis process</td>
                                                        </tr>
                                                        <tr>
                                                            <th>HR Team:</th>
                                                            <td>Members of the HR team both managers and employyees
                                                                – who take part of the diagnosis process</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Employees:</th>
                                                            <td>Employees who take part of the diagnosis process.
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="card">
                                {{-- card header --}}
                                <div class="mt-3 text-center">
                                    <h3>{{ __('Survey Response rate') }}</h3>
                                </div>
                                {{-- card body --}}
                                <div class="card-body text-center">
                                    {{-- survey divs --}}
                                    <div class="w-100">
                                        @if($Resp_overAll_res>0)
                                        <div class="circle-wrap m-2"
                                            style="left: 40%!important; position: relative !important;">
                                            <div class="circle">

                                                <div class="mask half">
                                                    <div
                                                        class="fill-{{ number_format(($overAll_res/$Resp_overAll_res)*100) }}">
                                                    </div>
                                                </div>

                                                <div
                                                    class="mask full-{{ number_format(($overAll_res/$Resp_overAll_res)*100) }}">
                                                    <div
                                                        class="fill-{{ number_format(($overAll_res/$Resp_overAll_res)*100) }}">
                                                    </div>
                                                </div>
                                                <div class="inside-circle">
                                                    {{ number_format(($overAll_res/$Resp_overAll_res)*100) }}%<p>
                                                        Responded</p>
                                                </div>


                                            </div>
                                        </div>

                                        @endif
                                        @if($prop_leadersResp>0)
                                        <div class="col-6 text-end function-lable mr-3">{{ __('Total Leaders
                                            Answers')
                                            }} {{
                                            $leaders_res }} {{ __('out of') }}
                                            {{ $prop_leadersResp }}</div>
                                        <div class="col-9 text-start function-progress">
                                            <div class="progress" style="height: 31px">
                                                <div class="progress-bar @if(($leaders_res/$prop_leadersResp)<0.5) bg-danger @elseif(($leaders_res/$prop_leadersResp)==1) bg-success @else bg-warning @endif"
                                                    role="progressbar"
                                                    style="width: {{ ($leaders_res/$prop_leadersResp)*100 }}%; font-size: 1rem"
                                                    aria-valuenow="{{ ($leaders_res/$prop_leadersResp)*100 }}"
                                                    aria-valuemin="0" aria-valuemax="100">{{
                                                    number_format(($leaders_res/$prop_leadersResp)*100) }}%</div>
                                            </div>
                                        </div>
                                        @endif
                                        @if($prop_hrResp>0)
                                        <div class="col-6 text-end function-lable mr-3">{{ __('Total HR Answers') }}
                                            {{
                                            $hr_res }} {{
                                            __('out of') }} {{
                                            $prop_hrResp }}</div>
                                        <div class="col-9 text-start function-progress">
                                            <div class="progress" style="height: 31px">
                                                <div class="progress-bar @if(($hr_res/$prop_hrResp)<0.5) bg-danger @elseif(($hr_res/$prop_hrResp)==1) bg-success @else bg-warning @endif"
                                                    role="progressbar"
                                                    style="width: {{ ($hr_res/$prop_hrResp)*100 }}%; font-size: 1rem"
                                                    aria-valuenow="{{ ($hr_res/$prop_hrResp)*100 }}" aria-valuemin="0"
                                                    aria-valuemax="100">{{
                                                    number_format(($hr_res/$prop_hrResp)*100) }}%
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        @if($prop_empResp>0)
                                        <div class="col-6 text-end function-lable mr-3">{{ __('Total Employee
                                            Answers')
                                            }} {{ $emp_res
                                            }} {{ __('out of') }} {{
                                            $prop_empResp }}</div>

                                        <div class="col-9 text-start function-progress">
                                            <div class="progress" style="height: 31px">
                                                <div class="progress-bar @if(($emp_res/$prop_empResp)<0.5) bg-danger @elseif(($emp_res/$prop_empResp)==1) bg-success @else bg-warning @endif"
                                                    role="progressbar"
                                                    style="width: {{ ($emp_res/$prop_empResp)*100 }}%; font-size: 1rem"
                                                    aria-valuenow="{{ ($emp_res/$prop_empResp)*100 }}" aria-valuemin="0"
                                                    aria-valuemax="100">{{
                                                    number_format(($emp_res/$prop_empResp)*100)
                                                    }}%</div>
                                            </div>
                                        </div>
                                        @endif
                                        {{-- end survey divs --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="Function" class="card mt-4" style="letter-spacing: 0.065rem;">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Result overview - by Functions and Practices') }}</h3>
                </div>
                <div class="card-body text-capitalize">
                    <div class="row">
                        <div class="col-1" style="max-width: 45px;"></div>
                        <div class="col-11">
                            <div class="col-{{ count($functions) }} {{ app()->getLocale()=='ar'?'text-right':'text-left' }} h3 text-white p-3 bg-info"
                                style="border-radius: 45px 45px 45px 45px;width: 89%; -webkit-box-shadow: 0px 0px 5px 1px #ABABAB;box-shadow: 0px 0px 5px 1px #ABABAB;">
                                {{ __('Key functions') }}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-1" style="max-width: 45px;"></div>
                        <div class="col-11">
                            <div class="row  padding-left-10px">
                                @foreach ($functions as $function )
                                <div class="text-center text-white m-1 bg-info"
                                    style="width:10.5%; border-radius: 10px; -webkit-box-shadow: 0px 0px 5px 1px #ABABAB;box-shadow: 0px 0px 5px 1px #ABABAB; font-size: 0.79rem">
                                    {{ app()->getLocale()=='ar'?$function->FunctionTitleAr:$function->FunctionTitle}}
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
                        justify-content: center;color:#376092; font-size: 1.5rem; font-weight: bold">
                            {{ __('Practices')}}
                        </div>
                        <div class="col-11">
                            <div class="row" style="width: 100%">
                                @foreach ($functions as $function )
                                <?php $firstofFirstLoop= $loop->first ; ?>
                                <div class="col-1 m-1 justify-content-center pb-1 pt-1"
                                    style="width: 10.5%; font-size: 0.79rem">
                                    @foreach( $overall_Practices as $overall_Practice)
                                    @if ( $overall_Practice['function_id'] == $function->id)
                                    <div class="text-center @if(!$loop->first) mt-1 p-2 @endif @if($firstofFirstLoop) p-2 @else p-2 m-1 @endif @if($overall_Practice['weight']<=0.5) bg-danger text-white @elseif (($overall_Practice['weight']>0.5)&&($overall_Practice['weight']<=0.8)) bg-warning text-black @else bg-success text-white @endif"
                                        style=" width:125%; border-radius: 10px; -webkit-box-shadow: 5px 5px 20px 5px #ABABAB;box-shadow: 5px 5px 20px 5px #ABABAB;">
                                        {{ $overall_Practice['name'] }} {{-- {{ $overall_Practice['weight'] }}--id:
                                        {{
                                        $overall_Practice['id'] }} --}}
                                    </div>
                                    @endif
                                    @endforeach
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button id="FunctionDownload" onclick="downloadResult('Function','Function')"
                class="btn btn-success mt-1">{{ __('Download') }}</button>
            <div id="key" class="card mt-4" style="letter-spacing: 0.065rem;">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Dashboard') }}</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-3 m-1 rounded text-center h3 p-3"
                            style="background-color: #DCE6F2 ; color:#376092 !important;">
                            {{ __('Overall Performance')}}
                            <div class="mt-5">
                                <div class="circle-wrap">
                                    <div class="circle">
                                        <div class="mask half">
                                            <div class="fill-{{ $overallResult}}"></div>
                                        </div>
                                        <div class="mask full-{{ $overallResult}}">
                                            <div class="fill-{{ $overallResult}}"></div>
                                        </div>
                                        <div class="{{app()->getLocale()=='ar'?'inside-circle-rtl':'inside-circle'}}">
                                            {{ $overallResult}}%<p>{{ __('Performance score') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="pt-3">
                                    {{ __('Overall performance of HR functionality') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-4 m-1 rounded text-center h3 p-3"
                            style="background-color: #DCE6F2 ; color:#376092 !important;">
                            {{ __('Key Improvement Areas') }}
                            <?php $hasWPoints=false;
                            usort($asc_perform, function ($a, $b) {
                                return $a['performance'] <=> $b['performance'];
                            }) ?>
                            @foreach (collect($asc_perform)->where('performance','<',80)->take(4) as $asc_perform_)
                                <div class="mt-5 text-start">
                                    <span class="h5"> {{
                                        $asc_perform_['function'] }}</span>
                                    <div class="progress" style="height: 31px">
                                        <div class="progress-bar
                                    @if($asc_perform_['performance']<=50) bg-danger @elseif($asc_perform_['performance']>50 && $asc_perform_['performance']<80) bg-warning @else bg-success @endif"
                                            role="progressbar"
                                            style="width: {{ $asc_perform_['performance']  }}%; font-size: 1rem;min-width: 2em;"
                                            aria-valuenow="{{ $asc_perform_['performance']  }}" aria-valuemin="2"
                                            aria-valuemax="100">{{
                                            number_format($asc_perform_['performance']) }}%
                                        </div>
                                    </div>
                                    <?php $hasWPoints=true; ?>

                                </div>
                                @endforeach
                                @if (!$hasWPoints)
                                <span class="h5" style="font-size: 2rem"> {{ __('None') }}</span>
                                @endif
                        </div>
                        <div class="col-4 m-1 rounded text-center h3 p-3"
                            style="background-color: #DCE6F2 ; color:#376092 !important;">{{ __('Strength Areas') }}
                            <?php $strengthcounter = 0; ?>

                            <?php $hasWPoints=false;
                        usort($asc_perform, function ($a, $b) {
                            return $b['performance'] <=> $a['performance'];
                        }) ?>
                            @foreach(collect($asc_perform)->where('performance','>',80)->take(4) as $asc_perform_)
                            <div class="mt-5 text-start">
                                <?php $strengthcounter++; ?>
                                <span class="h5"> {{ $asc_perform_['function'] }}</span>
                                <div class="progress" style="height: 31px">
                                    <div class="progress-bar @if($asc_perform_['performance']<=50) bg-danger @elseif($asc_perform_['performance']>50 && $asc_perform_['performance']<80) bg-warning @else bg-success @endif"
                                        role="progressbar"
                                        style="width: {{ $asc_perform_['performance']  }}%; font-size: 1rem;min-width: 2em;"
                                        aria-valuenow="{{ $asc_perform_['performance']  }}" aria-valuemin="0"
                                        aria-valuemax="100">{{ number_format($asc_perform_['performance']) }}%
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @if ($strengthcounter==0)
                            <h5 class="h5 mt-5 pt-5" style="font-size: 2rem"> {{ __('None') }}</h5>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <span class="legend-result"><b>{{ __('Legend:') }}</b></span> <span class="legend-levels"><b> {{
                            __('Low:') }}</b></span>
                    <=50% – <span class="legend-levels"><b>{{ __('Medium:') }}</b></span> > {{ __('50% to 80%') }} –
                        <span class="legend-levels"><b>{{ __('High:') }}</b></span> >80%

                </div>
            </div>
            <button id="keyDownload" onclick="downloadResult('key','Dashboard')"
                class="btn btn-success mt-1">{{__('Download
                key')}}</button>
            <div id="Laverages" class="card mt-4" style="letter-spacing: 0.065rem;">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Performance by Functions - Leadership') }}
                    </h3>
                </div>
                <div class="card-body" style="background-color: #DCE6F2 ; color:#376092 !important;">
                    <div class="row text-center">
                        <div class="m-1 rounded text-center h5 p-3" style="font-size: 1.7rem">
                            {{ __('People management performance – Leadership view average scores by people
                            functions')}}
                        </div>
                    </div>
                    <div class="row row-function">
                        <?php
                            //sort Descending sorted_leader_performences
                            usort($sorted_leader_performences, function ($a, $b) {
                                return $b['performance'] <=> $a['performance'];
                            })
                            ?>
                        @foreach ($sorted_leader_performences as $performence)
                        <div class="col-md-3 col-sm-6 text-center function-lable">
                            {{ $performence['function'] }}
                        </div>
                        <div class="col-md-9 col-sm-6 text-start function-progress">
                            <div class="progress" style="height: 31px">
                                <div class="progress-bar @if($performence['performance']>80 && $performence['performance']<=100) bg-success @elseif($performence['performance']>50 && $performence['performance']<=80) bg-warning @else bg-danger @endif"
                                    role="progressbar"
                                    style="width: {{ $performence['performance'] }}%; font-size: 1rem;min-width: 2em;"
                                    aria-valuenow="{{ $performence['performance'] }}" aria-valuemin="0"
                                    aria-valuemax="100">
                                    {{(number_format($performence['performance'])) }}%
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <button id="averagesDownload" onclick="downloadResult('Laverages','Leadership_View_Average')"
                class="btn btn-success mt-1">{{ __('Download') }}</button>
            <div id="HRaverages" class="card mt-4" style="letter-spacing: 0.065rem;">
                <div class="card-header">
                    <h3 class="card-title ">{{ __('Performance by Functions - HR Team') }}
                    </h3>
                </div>
                <div class="card-body" style="background-color: #DCE6F2 ; color:#376092 !important;">
                    <div class="row text-center">
                        <div class="m-1 rounded text-center h5 p-3" style="font-size: 1.7rem">
                            {{ __('People management performance – HR Team view Average scores by people functions')
                            }}
                        </div>
                    </div>
                    <div class="row row-function">
                        <?php
                            //sort Descending sorted_hr_performences
                            usort($sorted_hr_performences, function ($a, $b) {
                                return $b['performance'] <=> $a['performance'];
                            })
                            ?>
                        @foreach ($sorted_hr_performences as $performence)


                        <div class="col-md-3 col-sm-6 text-center function-lable">{{ $performence['function'] }}
                        </div>
                        <div class="col-md-9 col-sm-6 text-start function-progress">
                            {{-- @if($performence['performance']>0) --}}
                            <div class="progress" style="height: 31px">
                                <div class="progress-bar @if($performence['performance']>80 && $performence['performance']<=100) bg-success @elseif($performence['performance']>50 && $performence['performance']<=80) bg-warning @else bg-danger @endif"
                                    role="progressbar"
                                    style="width: {{ $performence['performance'] }}%; font-size: 1rem;min-width: 2em;"
                                    aria-valuenow="{{ $performence['performance'] }}" aria-valuemin="0"
                                    aria-valuemax="100">
                                    {{(number_format($performence['performance'])) }}%</div>
                            </div>
                            {{-- @else
                            not applicable
                            @endif --}}
                        </div>
                        @endforeach

                    </div>
                </div>
            </div>
            <button id="averagesDownload" class="btn btn-success mt-1"
                onclick="downloadResult('HRaverages','HR_View_Average')">{{ __('Download') }}</button>
            <div id="Empaverages" class="card mt-4" style="letter-spacing: 0.065rem;">
                <div class="card-header">
                    <h3 class="card-title ">{{ __('Performance by Functions - Employees') }}
                    </h3>
                </div>
                <div class="card-body" style="background-color: #DCE6F2 ; color:#376092 !important;">
                    <div class="row text-center">
                        <div class="m-1 rounded text-center h5 p-3" style="font-size: 1.7rem">
                            {{ __('People management performance – Employee view Average scores by people
                            functions') }}
                        </div>
                    </div>
                    <div class="row row-function">
                        <?php
                            //sort Descending sorted_emp_performences
                            usort($sorted_emp_performences, function ($a, $b) {
                                return $b['performance'] <=> $a['performance'];
                            })
                            ?>
                        @foreach ($sorted_emp_performences as $performence)


                        <div class="col-md-3 col-sm-6 text-center function-lable">{{
                            $performence['function'] }}</div>
                        <div class="col-md-9 col-sm-6 text-start function-progress">
                            @if($performence['applicable'])
                            <div class="progress" style="height: 31px">
                                <div class="progress-bar @if($performence['performance']>80 && $performence['performance']<=100) bg-success @elseif($performence['performance']>50 && $performence['performance']<=80) bg-warning @else bg-danger @endif"
                                    role="progressbar"
                                    style="width: {{ $performence['performance'] }}%; font-size: 1rem;min-width: 2em;"
                                    aria-valuenow="{{ $performence['performance'] }}" aria-valuemin="0"
                                    aria-valuemax="100">
                                    {{(number_format($performence['performance'])) }}%</div>
                            </div>
                            @else
                            {{ __('not applicable') }}
                            @endif
                        </div>
                        @endforeach

                    </div>
                </div>
            </div>
            <button id="averagesDownload" class="btn btn-success mt-1"
                onclick="downloadResult('Empaverages','Employee_View_Average')">{{ __('Download') }}</button>

            <div id="heatmap" class="card mt-4" style="letter-spacing: 0.065rem;">
                <div class="card-header">
                    <h3 class="card-title ">{{ __('High level heat map') }}
                    </h3>
                </div>
                <div class="card-body" style="background-color: #DCE6F2 ; color:#376092 !important;">
                    <div class="row text-center">
                        <div class="m-1 rounded text-center h5 p-3 text-danger" style="font-size: 1.7rem">
                            {{ __('High level heat map – ’Priorities vs Performances of Functions – Based on
                            Leadership
                            view’') }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3 text-center heat-map heat-map-priority heat-map-priority-v">{{__('Priority
                            by
                            leaders') }}</div>
                        <div class="col-9 heat-map"></div>

                        @for ($i = 0; $i < 3; $i++) <div
                            class="col-3 text-end heat-map heat-map-priority heat-map-priority-lable text-capitalize">
                            <span>
                                @switch($i)
                                @case(0)
                                {{ __('High') }}
                                @break

                                @case(1)
                                {{ __('Medium') }}
                                @break

                                @case(2)
                                {{ __('Low') }}
                                @break

                                @default
                                @endswitch
                            </span>
                    </div>
                    <div class="col-9 heat-map">
                        <div class="row">
                            @for ($j = 0; $j < 3; $j++) {{-- Start first --}} @if (($i==0 && $j==0) || ($i==0 && $j==1)
                                || ($i==1 && $j==0)) <div class="bg-danger heat-map-result">
                                @if ($i == 0)
                                @if ($j == 0)
                                <ul>
                                    @foreach ($priorities as $pri)
                                    @if ($pri['priority'] > 80 && $pri['priority'] <= 100) @if ($pri['performance']
                                        <=50) <li class="text-white">{{ $pri['function'] }}
                                        </li>
                                        @endif
                                        @endif
                                        @endforeach
                                </ul>
                                @elseif ($j == 1)
                                <ul>

                                    @foreach ($priorities as $pri)
                                    @if ($pri['priority'] > 80 && $pri['priority'] <= 100) @if ($pri['performance']>
                                        50
                                        && $pri['performance'] <= 80) <li class="text-white">{{ $pri['function'] }}
                                            </li>
                                            @endif
                                            @endif
                                            @endforeach
                                </ul>

                                @else
                                <ul>

                                    @foreach ($priorities as $pri)
                                    ffff
                                    @if ($pri['priority'] >50 && $pri['priority'] <80) @if ($pri['performance'] <=50)
                                        <li class="text-white">{{ $pri['function'] }}</li>
                                        @endif
                                        @endif
                                        @endforeach
                                </ul>
                                @endif
                                @else
                                @if ($j == 0)
                                <ul>

                                    @foreach ($priorities as $pri)
                                    @if ($pri['priority'] >50 && $pri['priority'] <80) @if ($pri['performance'] <=50)
                                        <li class="text-white">{{ $pri['function'] }}</li>
                                        @endif
                                        @endif
                                        @endforeach
                                </ul>
                                @endif
                                @endif
                        </div>
                        @endif
                        {{-- End first --}}
                        {{-- Start second --}}
                        @if (($i == 1 && $j == 1) || ($i == 2 && $j == 1) || ($i == 2 && $j == 0))
                        <div class="bg-warning heat-map-result">
                            @if ($i == 1)
                            @if ($j == 1)
                            <ul>

                                @foreach ($priorities as $pri)
                                @if ($pri['priority'] >50 && $pri['priority'] <80) @if ($pri['performance']> 50
                                    &&
                                    $pri['performance'] <= 80) <li class="text-black">{{ $pri['function'] }}</li>
                                        @endif
                                        @endif
                                        @endforeach
                            </ul>
                            @endif
                            @else
                            @if ($j == 0)
                            <ul>

                                @foreach ($priorities as $pri)
                                @if ($pri['priority'] <=50) @if ($pri['performance'] <=50) <li class="text-black">
                                    {{$pri['function'] }} {{ $pri['performance'] }}</li>
                                    @endif
                                    @endif
                                    @endforeach
                            </ul>
                            @endif
                            @if ($j == 1)
                            <ul>

                                @foreach ($priorities as $pri)
                                @if ($pri['priority'] <=50) @if ($pri['performance']> 50 && $pri['performance'] <= 80)
                                        <li class="text-black">{{ $pri['function'] }}</li>
                                        @endif
                                        @endif
                                        @endforeach
                            </ul>
                            @endif
                            @endif
                        </div>
                        @endif
                        {{-- End second --}}
                        {{-- Start third --}}
                        @if (($i == 0 && $j == 2) || ($i == 1 && $j == 2) || ($i == 2 && $j == 2))
                        <div class="bg-success heat-map-result">
                            @if ($i == 0)
                            @if ($j == 2)
                            <ul>

                                @foreach ($priorities as $pri)
                                @if ($pri['priority'] >80 && $pri['priority'] <= 100) @if ($pri['performance']> 80)
                                    <li class="text-white">{{ $pri['function'] }}</li>
                                    @endif
                                    @endif
                                    @endforeach
                            </ul>
                            @endif
                            @endif
                            @if ($i == 1)
                            @if ($j == 2)
                            <ul>

                                @foreach ($priorities as $pri)
                                @if ($pri['priority'] >50 && $pri['priority'] < 80) @if ($pri['performance']> 80)
                                    <li class="text-white">{{ $pri['function'] }}
                                    </li>
                                    @endif
                                    @endif
                                    @endforeach
                            </ul>
                            @endif
                            @endif
                            @if ($i == 2)
                            @if ($j == 2)
                            <ul>

                                @foreach ($priorities as $pri)
                                @if ($pri['priority'] <=50) @if ($pri['performance']> 80)
                                    <li class="text-white">{{ $pri['function'] }}
                                    </li>
                                    @endif
                                    @endif
                                    @endforeach
                            </ul>
                            @endif
                            @endif
                        </div>
                        @endif
                        @endfor
                    </div>
                </div>
                @endfor

                <div class="col-3 text-end heat-map">
                    <span></span>
                </div>
                <div class="col-9 heat-map">
                    <div class="row">
                        <div class="heat-map-bottom-label">{{ __('Low') }}</div>
                        <div class="heat-map-bottom-label">{{ __('Medium') }}</div>
                        <div class="heat-map-bottom-label">{{ __('High') }}</div>
                    </div>
                </div>
                <div class="col-3 text-end heat-map">
                    <span></span>
                </div>
                <div class="col-9 heat-map text-center p-3">
                    <div class="heat-map-bottom-title">
                        {{ __('People management performance score by leaders') }}
                    </div>
                </div>
            </div>
        </div>
        {{-- card footer --}}
        <div class="card-footer">
            <span class="legend-result"><b>{{ __('Legend:') }}</b></span> <span class="legend-levels"><b> {{
                    __('Low:')
                    }}</b></span>
            <=50% – <span class="legend-levels"><b>{{ __('Medium:') }}</b></span> > {{ __('50% to 80%') }} – <span
                    class="legend-levels"><b>{{ __('High:') }}</b></span> >80%

        </div>
    </div>
    <button id="heatmapDownload" class="btn btn-success mt-1" onclick="downloadResult('heatmap','heatmap')">
        {{ __('Download') }}
    </button>
    <div class="card mt-4" id="Linear" style="letter-spacing: 0.065rem;">
        <div class="card-header">
            <h4 class="card-title">{{ __('Comparison of Leadership and HR team results') }}</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="d-flex justify-content-center">
                    <div class="chart-container w-50">
                        <canvas id="myChart" width="400" height="400"></canvas>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div id="img-out"></div> --}}
        {{-- <button id="pptDownload" class="btn btn-success">Download PPT</button> --}}
    </div>
    <button id="heatmapDownload" class="btn btn-success mt-1" onclick="downloadResult('Linear','Linear')">
        {{ __('Download') }}</button>
    <div class="card mt-4" id="Consolidated" style="letter-spacing: 0.065rem;">
        <div class="card-header">
            <h4 class="card-title">{{ __('Consolidated results') }}</h4>
        </div>
        <div class="card-body" style="letter-spacing: 0.065rem;">
            <div class="row">
                <div class="col-12">
                    {{ __('Consolidated findings by function') }}
                </div>
            </div>
            <div class="row">
                <div class="col-sm-1 m-1 text-white bg-info" style="font-size: 0.84rem;
    border-radius: 10px;
    display: flex;
    justify-content: center;
    align-content: center;
    flex-direction: column;
    text-align: center;">
                    <span>{{ __('Functions') }}</span>
                </div>
                <?
                            usort($asc_perform, function ($a, $b) {
                                return $a['performance'] <=> $b['performance'];
                            }) ?>
                @foreach ($asc_perform as $perfomr)
                <div class="m-1 text-white bg-info" style="width: 10.4% !important; font-size: 0.8rem;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-content: center;
            flex-direction: column;
            text-align: center;">
                    {{ $perfomr['function'] }}
                </div>
                @endforeach
            </div>
            <div class="row">
                <div class="col-sm-1 m-1 text-white bg-primary" style="font-size: 0.84rem;
    border-radius: 10px;
    display: flex;
    justify-content: center;
    align-content: center;
    flex-direction: column;
    text-align: center;">
                    <span style="hyphens: auto;">{{ __('Improvement need') }}
                    </span>
                </div>
                @foreach ($asc_perform as $perfomr)
                <div class="m-1 @if($perfomr['performance']<=50) bg-danger text-white @elseif($perfomr['performance']>80) bg-success text-white @else bg-warning @endif"
                    style="width: 10.4% !important; font-size: 0.8rem border-radius: 10px;
                ">
                    @if($perfomr['performance']<=50) {{ __('Critical to improve') }} @elseif($perfomr['performance']>80)
                        No
                        {{ __('Improvement Needed') }}
                        @else {{ __('Need to improve') }}{{-- {{ $perfomr['performance'] }} --}}
                        @endif
                </div>
                @endforeach
            </div>
            <div class="row">
                <div class="col-sm-1 m-1 text-white bg-primary" style="font-size: 0.84rem;

            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-content: center;
            flex-direction: column;
            text-align: center;">
                    <span>
                        {{ __('Performance rating by Leaders, Employees and HR Team') }}
                    </span>
                </div>
                @foreach ($asc_perform as $perfomr)

                <div class="m-1 " style="width: 10.4% !important; font-size: 0.8rem border-radius: 10px;">
                    @foreach( $sorted_leader_performences as $leader)
                    @if($leader['function_id'] == $perfomr['function_id'])
                    <div class="row mt-2">
                        <div class="col-md-5">
                            <img src="{{ asset('assets/img/icon/LeadersIcon.png') }}" height="30" width="35" alt="">
                        </div>
                        @if($leader['applicable'])
                        {{ $leader['performance'] }}% <br>
                        @else
                        {{ __('N/A') }}<br>
                        @endif
                    </div>
                    @break;
                    @endif
                    @endforeach
                    {{-- hr --}}
                    @foreach ($sorted_hr_performences as $hr)
                    @if($hr['function_id'] == $perfomr['function_id'])
                    <div class="row mt-2">
                        <div class="col-md-5">
                            <img src="{{ asset('assets/img/icon/HRIcon.png') }}" height="30" width="35" alt="">
                        </div>
                        @if($hr['applicable'])
                        {{ $hr['performance'] }}% <br>
                        @else
                        {{ __('N/A') }}<br>
                        @endif
                    </div>
                    @break;
                    @endif
                    @endforeach
                    {{-- emp --}}
                    @foreach ($sorted_emp_performences as $emp)
                    @if($emp['function_id'] == $perfomr['function_id'])
                    <div class="row mt-2">
                        <div class="col-md-5">
                            <img src="{{ asset('assets/img/icon/EmployeIcon.png') }}" height="30" width="35" alt="">
                        </div>
                        @if($emp['applicable'])
                        {{ $emp['performance'] }}% <br>
                        @else
                        {{ __('N/A') }}<br>
                        @endif
                    </div>
                    @break;
                    @endif
                    @endforeach
                </div>
                @endforeach
            </div>
            <div class="row">
                <div class="col-sm-1 m-1 text-white bg-info" style="font-size: 0.84rem;
    border-radius: 10px;
    display: flex;
    justify-content: center;
    align-content: center;
    flex-direction: column;
    text-align: center;">
                    <span>{{ __('Priority') }}
                    </span>
                </div>
                @foreach ($asc_perform as $perfomr)
                <div class="m-1 " style="width: 10.4% !important; font-size: 0.8rem">
                    @foreach ($priorities as $pro)
                    @if($pro['function_id'] == $perfomr['function_id'])
                    <div class="@if( $pro['priority']<=50) bg-success text-white @elseif($pro['priority']>50 && $pro['priority']<=80) bg-warning text-black @else bg-danger text-white @endif"
                        style="border-radius: 10px;     display: flex;
    justify-content: center;
    align-content: center;
    flex-direction: column;
    text-align: center;
    height: 2rem;
    font-size: 1rem;">
                        @if( $pro['priority']<=50) {{ __('Low') }} @elseif($pro['priority']>50 && $pro['priority']
                            <=80) {{ __('Medium') }} @else {{ __('High') }} @endif </div>

                                @break
                                @endif
                                @endforeach
                    </div>
                    @endforeach
                </div>
                <div class="row">
                    <div class="col-sm-1 m-1 bg-primary text-white" style="font-size: 0.84rem;
    border-radius: 10px;
    display: flex;
    justify-content: center;
    align-content: center;
    flex-direction: column;
    text-align: center;">

                        <span>{{ __('Key improvement areas by practices') }}
                        </span>
                    </div>
                    @foreach ($asc_perform as $perfomr)
                    <?php $count=0; ?>
                    <div class="m-1 " style="width: 10.4% !important; font-size: 0.8rem">
                        <ul class="list-group" style="width: 100%; border-radius: 10px;">
                            @foreach ( $overall_Practices as $practice)
                            @if($practice['function_id'] == $perfomr['function_id'])
                            <li class="list-group-item list-group-item p-2 text-center">
                                {{ $practice['name'] }}
                            </li>
                            <?php $count++; ?>
                            @endif
                            @if($count==3)
                            @break;
                            @endif
                            @endforeach
                        </ul>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="card-footer">
                <span class="legend-result"><b>{{ __('Legend:') }}</b></span> <span class="legend-levels"><b> {{
                        __('Leadership:') }}</b></span>
                <img src="{{ asset('assets/img/icon/LeadersIcon.png') }}" height="20" width="25" alt=""> – <span
                    class="legend-levels"><b>{{ __('HR Team') }}:</b></span> <img
                    src="{{ asset('assets/img/icon/HRIcon.png') }}" height="20" width="25" alt=""> – <span
                    class="legend-levels"><b>{{ __('Employee') }}:</b></span> <img
                    src="{{ asset('assets/img/icon/EmployeIcon.png') }}" height="20" width="25" alt="">

            </div>
        </div>
        <button id="heatmapDownload" class="btn btn-success mt-1" style="border-radius: 10px;
    -webkit-box-shadow: 5px 5px 20px 5px #ababab;
    box-shadow: 5px 5px 20px 5px #ababab;" onclick="downloadResult('Consolidated','Consolidated')">
            {{ __('Download') }}
        </button>

        <div class="card mt-4" style="letter-spacing: 0.065rem;">
            <div class="card-header">
                <h3 class="card-title">{{ __('Downloads') }}</h3>
            </div>
            <div class="card-body text-capitalize">

            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
{{-- <script src="{{ asset('assets/js/libs/jszip.min.js') }}"></script>
<script src="{{ asset('assets/js/dist/pptxgen.min.js') }}"></script> --}}
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.3.2/html2canvas.min.js">
</script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.3.2/html2canvas.esm.js">
</script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.3.2/html2canvas.js">
</script>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    Labels= @json($function_Lables);
    Leaders= @json($leaders_perform_onlyz);
    hr=@json($hr_perform_onlyz);

    const ctx = document.getElementById('myChart');
//get max of hr
var max =0;
var max_hr = hr.reduce(function(prev, current) {
    return (prev > current) ? prev : current
})
//get max of leaders
var max_leaders = Leaders.reduce(function(prev, current) {
    return (prev > current) ? prev : current
})
max=max_hr>max_leaders?max_hr:max_leaders;
max=(100-max)>10?parseInt(max)+5:100;
const myChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: Labels,
        datasets: [
            {
            label: "{{ __('Leadership responses') }}",
            data: Leaders,
            backgroundColor: [
                'rgba(0, 74, 159, 1)'//,
                // 'rgba(54, 162, 235, 0.2)',
                // 'rgba(255, 206, 86, 0.2)',
                // 'rgba(75, 192, 192, 0.2)',
                // 'rgba(153, 102, 255, 0.2)',
                // 'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(0, 74, 159, 1)'//,
                // 'rgba(54, 162, 235, 1)',
                // 'rgba(255, 206, 86, 1)',
                // 'rgba(75, 192, 192, 1)',
                // 'rgba(153, 102, 255, 1)',
                // 'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        },
            {
            label: "{{ __('HR responses') }}",
            data: hr,
            backgroundColor: [
                // 'rgba(255, 99, 132, 0.2)',
                // 'rgba(54, 162, 235, 0.2)',
                // 'rgba(255, 206, 86, 0.2)',
                // 'rgba(75, 192, 192, 0.2)',
                // 'rgba(153, 102, 255, 0.2)',
                'rgba(0, 153, 204,1)'
            ],
            borderColor: [
                // 'rgba(255, 99, 132, 1)',
                // 'rgba(54, 162, 235, 1)',
                // 'rgba(255, 206, 86, 1)',
                // 'rgba(75, 192, 192, 1)',
                // 'rgba(153, 102, 255, 1)',
                'rgba(0, 159, 204, 1)'
            ],
            borderWidth: 1
        },
    ]
    },
    options: {
        scales: {
            y: {
                suggestedMin: 0,
                suggestedMax: max,
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Score'
                }
            }
        }
    }
});
$("#DownloadAll").click(function(){
    window.setTimeout(function(){
                 // do whatever you want to do
                 downloadResult('Function','Function' )
                  }, 3000);
    window.setTimeout(function(){
                 // do whatever you want to do
                 downloadResult('key','Dashboard' )
                  }, 3000);
    window.setTimeout(function(){
                 // do whatever you want to do
                 downloadResult('Laverages','Leadership_View_Average' )
                  }, 3000);
    window.setTimeout(function(){
                 // do whatever you want to do
                 downloadResult('HRaverages','HR_View_Average' )
                  }, 3000);
                  window.setTimeout(function(){
                             // do whatever you want to do
                             downloadResult('Empaverages','Employee_View_Average' )
                              }, 3000);
                window.setTimeout(function(){
                             // do whatever you want to do
                             downloadResult('heatmap','heatmap' )
                              }, 3000);
                window.setTimeout(function(){
                             // do whatever you want to do
                             downloadResult('Linear','Linear' )
                              }, 3000);
                window.setTimeout(function(){
                             // do whatever you want to do
                             downloadResult('Consolidated','Consolidated' )
                              }, 3000);
                });

                // $("#heatmapDownload").click(function() {

    //     html2canvas(document.getElementById("heatmap")).then(function(canvas) {
    //         downloadImage(canvas.toDataURL(), "heatmap.png");
    //     });


    // });
    // $("#FunctionDownload").click(function() {

    //     html2canvas(document.getElementById("Function")).then(function(canvas) {
    //         downloadImage(canvas.toDataURL(), "Function.png");
    //     });


    // });
    // $("#keyDownload").click(function() {

    //     html2canvas(document.getElementById("key")).then(function(canvas) {
    //         downloadImage(canvas.toDataURL(), "key.png");
    //     });
// });
    function downloadResult(Resultcard, filename = 'untitled') {
        console.log(Resultcard);
        html2canvas(document.getElementById(Resultcard)).then(function(canvas) {
            downloadImage(canvas.toDataURL(), filename+".png");
        });
    }

    function downloadImage(uri, filename) {
        var link = document.createElement('a');
        if (typeof link.download !== 'string') {
            window.open(uri);
        } else {
            link.href = uri;
            link.download = filename;
            accountForFirefox(clickLink, link);
        }
    }

    function clickLink(link) {
        link.click();
    }

    function accountForFirefox(click) {
        var link = arguments[1];
        document.body.appendChild(link);
        click(link);
        document.body.removeChild(link);
    }
</script>
@endsection
