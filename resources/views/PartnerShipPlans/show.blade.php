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
            {{-- card to show plan details --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $partnerShipPlan->PlanTitle }} {{ __('plan details.') }}</h3>
                </div>
                <div class="card-body">
                    <div class="justify-content-center text-center">
                        <div class="row">
                            <div class="col-6">
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <span class="font-weight-bold float-start">{{ __('Plan Title') }}</span>
                                        <span class="float-end" style="color:#eead35 !important">{{
                                            app()->getLocale()=='ar'?$partnerShipPlan->PlanTitleAr:$partnerShipPlan->PlanTitle }}</span>
                                    </li>
                                    <li class="list-group-item">
                                        <span class="font-weight-bold float-start">{{ __('Objective') }}</span>
                                        <span class="float-end" style="color:#eead35 !important">{!!
                                            app()->getLocale()=='ar'?$partnerShipPlan->ObjectiveAr:$partnerShipPlan->Objective !!}</span>
                                    </li>
                                    <li class="list-group-item">
                                        <span class="font-weight-bold float-start">{{ __('Process') }}</span>
                                        <span class="float-end" style="color:#eead35 !important">{{
                                            app()->getLocale()=='ar'?$partnerShipPlan->ProcessAr:$partnerShipPlan->Process }}</span>
                                    </li>
                                    <li class="list-group-item">
                                        <span class="font-weight-bold float-start">{{ __('Participants') }}</span>
                                        <span class="float-end" style="color:#eead35 !important">
                                            @switch($partnerShipPlan->Audience)
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
                                            @endswitch</span>
                                    </li>
                                    <li class="list-group-item">
                                        <span class="font-weight-bold float-start">{{ __('Report') }}</span>
                                        <span class="float-end" style="color:#eead35 !important">{{
                                            app()->getLocale()=='ar'?$partnerShipPlan->ReportAr:$partnerShipPlan->Report }}</span>
                                    </li>
                                    <li class="list-group-item">
                                        <span class="font-weight-bold float-start">{{ __('Delivery Mode') }}</span>
                                        <span class="float-end" style="color:#eead35 !important">{{
                                            app()->getLocale()=='ar'?$partnerShipPlan->DeliveryModeAr:$partnerShipPlan->DeliveryMode }}</span>
                                    </li>
                                    <li class="list-group-item">
                                        <span class="font-weight-bold float-start">{{ __('Limitations') }}</span>
                                        <span class="float-end" style="color:#eead35 !important">{!!
                                            app()->getLocale()=='ar'?$partnerShipPlan->LimitationsAr:$partnerShipPlan->Limitations !!}</span>
                                    </li>
                                    <li class="list-group-item">
                                        <span class="font-weight-bold float-start">{{ __('Price') }}</span>
                                        <span class="float-end" style="color:#eead35 !important">
                                            @if($partnerShipPlan->PaymentMethod!=1)
                                            <p>{{ $partnerShipPlan->Price }} OMR</p>
                                            @endif
                                            <p>
                                                @switch($partnerShipPlan->PaymentMethod)
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
                                        </span>
                                    </li>
                                    <li class="list-group-item">

                                        <button onclick="getFunctions('{{ $partnerShipPlan->id }}')"
                                            class="btn btn-primary float-start">{{ __('View Functions') }}</button>
                                        <div class="form-check form-switch float-end">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="flexSwitchCheckChecked" {{ $partnerShipPlan->Status? 'checked':''
                                            }}>
                                            <label class="form-check-label" for="flexSwitchCheckChecked">{{
                                                $partnerShipPlan->Status? __('Active'):__('In-Active') }}</label>
                                        </div>
                                        {{-- <a
                                            href="{{ route('practice-questions.CreateNewQuestion', $partnerShipPlan->id) }}"
                                            class="btn btn-primary float-end">Add New Question</a> --}}
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="row">
                            <div class="card-body">
                                {{-- @if (count($countries) > 0) --}}
                                <div class="table-responsive">
                                    <table id="data-table" class="table table-bordered data-table">
                                        <thead>
                                            <tr>
                                                <th>{{ __('#') }}</th>
                                                <th>{{ __('Function Title') }}</th>
                                                <th>{{ __('Respondent') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Practices') }}</th>
                                                {{-- <th>{{ __('Action') }}</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="Practice" aria-hidden="true" aria-labelledby="PracticeLabel" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="PracticeLabel">{{ __('Practices') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="practice" class="table-responsive">
                    <table id="practice-data-table" class="table table-bordered data-table">
                        <thead>
                            <tr>
                                <th>{{ __('#') }}</th>
                                <th>{{ __('Practice Title') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Questions') }}</th>
                                {{-- <th>{{ __('Action') }}</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="Questions" aria-hidden="true" aria-labelledby="QuestionsLabel" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="QuestionsLabel">{{ __('Questions') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="questions" class="table-responsive">
                    <table id="questions-data-table" class="table table-bordered data-table">
                        <thead>
                            <tr>
                                <th>{{ __('#') }}</th>
                                <th>{{ __('Question') }}</th>
                                <th>{{ __('Status') }}</th>

                                {{-- <th>{{ __('Action') }}</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" data-bs-target="#Practice" data-bs-toggle="modal">
                    {{ __('Back Practices') }}
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')

<script>
    function getFunctions(id){
        $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                bDestroy: true,
                ajax: "{{ url('/functions/getfunctions') }}/"+id,
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'FunctionTitle', name: 'FunctionTitle'},
                {data: 'Respondent', name: 'Respondent'},
                {data: 'Status', name: 'Status'},
                {data: 'practices', name: 'practices'},
                // {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });

    }
    // function getPractices(id){
        getPractice =(id)=>{
            //data-table
            $('#practice-data-table').DataTable({
                processing: true,
                serverSide: true,
                bDestroy: true,
                ajax: "{{ url('/function-practice/getpractices') }}/"+id,
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'PracticeTitle', name: 'PracticeTitle'},
                {data: 'Status', name: 'Status'},
                 {data: 'questions', name: 'questions'},
                // {data: 'action', name: 'action', orderable: false, searchable: false},
                ]

            })

        }
        ShowQuestion =(id)=>{
            //data-table
            $('#questions-data-table').DataTable({
                processing: true,
                serverSide: true,
                bDestroy: true,
                ajax: "{{ url('/practice-questions/getquestions') }}/"+id,
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'Question', name: 'Question'},
                {data: 'Status', name: 'Status'},
                // {data: 'action', name: 'action', orderable: false, searchable: false},
                ]

            })
        }
        $('#flexSwitchCheckChecked').change(function(){
            //is checkbox checked

            currentStat=$(this).is(':checked')
            console.log(currentStat);
            //post request
            $.ajax({
                url:"{{ url('/functions/change-status') }}",
                type:"POST",
                data:{
                    _token:"{{ csrf_token()}}",
                    id:"{{ $partnerShipPlan->id }}"
                },
                success:function(response){
                    if(response.msg=='success')
                    {
                        alert("Status has been change.")
                        $('.form-check-label').html(response.label);
                    }
                    else{
                        alert("Status has not been change.")
                        //uncheck checkbox
                        $('#flexSwitchCheckChecked').prop('checked',!currentStat);
                    }

                }
            })
        });
</script>
@endsection
