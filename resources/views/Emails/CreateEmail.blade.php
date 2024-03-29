{{-- extends --}}
@extends('layouts.main')

{{-- content --}}
{{-- Emails List --}}
@section('content')
<div class="container pt-5 mt-5">
        <div class="">
            <div class="col-12 mt-5 pt-5 {{ App()->getLocale()=='ar' ? 'custom-fixed-top-rtl' : 'custom-fixed-top' }}">
            <!-- side bar menu -->
            @include('layouts.sidebar')
        </div>
        <div class="col-12">
            {{-- email contents card view --}}
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 text-start">
                            <h3 class="card-title">{{ __('Create Email Content') }}</h3>
                        </div>
                        <div class="col-6 text-end">
                            <h3 class="">
                                <a href="{{ route('emails.manage') }}" class="btn btn-primary btn-sm"
                                    style="letter-spacing: 1px;">{{ __('Back') }}</a>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('emails.sendTheSurvey') }}" method="post">
                        @csrf
                        <div class="row">
                            <input type="hidden" name="reminder" id="reminder" value="{{ $reminder }}">
                            @if($reminder==2)
                            <input type="hidden" name="respondentID" id="respondentID" value="{{ $respondentID }}">
                            @endif
                            {{-- select Client --}}
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="client_id">{{ __('Select Client') }}</label>
                                    <select name="client_id" id="client_id"
                                        class="form-control @error('client_id') is-invalid @enderror" disabled>
                                        <option value="" selected>{{ __('Select Client') }}</option>
                                        @foreach ($clients as $client)
                                        <option value="{{ $client->id }}" @selected(old('client_id',$clientId)==$client->id)>{{
                                            $client->ClientName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            {{-- select Survey --}}
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="survey_id">{{ __('Select Survey') }}</label>
                                    <select name="survey_id" id="survey_id" class="form-control @error('survey_id') is-invalid
                            @enderror" disabled>
                                        <option value="" selected>{{ __('Select Survey') }}</option>
                                        @foreach ($surveys as $survey)
                                        <option value="{{ $survey->id }}" @selected(old('survey_id',$surveyId)==$survey->id)>{{
                                            $survey->SurveyTitle }}</option>
                                        @endforeach
                                    </select>
                                    @error('survey_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="SectorId">{{ __('Sector') }}</label>
                                    <select name="SectorId" id="SectorId" onchange="setUpComapny('SectorId','CompanyId')"
                                        class="form-control @error('SectorId') is-invalid @enderror" @if ($reminder!=2)
                                        required
                                        @else
                                        disabled
                                        @endif>
                                        <option value="">{{ __('Select Sector') }}</option>
                                        @foreach ($sectors as $sector)
                                        <option value="{{ $sector->id }}" @if (old('SectorId')==$sector->id) selected
                                            @endif>
                                            {{ app()->getLocale()=='ar'? $sector->sector_name_ar:$sector->sector_name_en
                                            }}</option>
                                        @endforeach
                                    </select>
                                    {{-- validation --}}
                                    @error('SectorId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            {{-- select company --}}
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="CompanyId">{{ __('Company') }}</label>
                                    <select name="CompanyId" id="CompanyId"
                                        class="form-control @error('CompanyId') is-invalid @enderror" @if ($reminder!=2)
                                        required
                                        @else
                                        disabled
                                        @endif>
                                        <option value="">{{ __('Select Company') }}</option>

                                    </select>
                                    {{-- validation --}}
                                    @error('CompanyId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            {{-- Subject --}}
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="subject">{{ __('Subject') }}</label>
                                    <input type="text" name="subject" id="subject" value="{{ old('subject') }}"
                                        class="form-control @error('subject') is-invalid @enderror">
                                    @error('subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            {{-- subject_ar --}}
                            {{-- <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="subject_ar">Subject (Arabic)</label>
                                    <input type="text" name="subject_ar" id="subject_ar" value="{{ old('subject_ar') }}"
                                        class="form-control @error('subject_ar') is-invalid @enderror">
                                    @error('subject_ar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> --}}
                            {{-- Email body_header textare --}}
                            <div class="col-md-8 col-sm-12">
                                <div class="form-group">
                                    <label for="body_header">{{ __('Email Body Header') }}</label>
                                    <textarea name="body_header" id="body_header" cols="30" rows="10"
                                        class="form-control @error('body_header') is-invalid @enderror">{{ old('body_header') }}</textarea>
                                    @error('body_header')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            {{-- Email body_header_ar textare --}}
                            {{-- <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="body_header_ar">Email Body Header (Arabic)</label>
                                    <textarea name="body_header_ar" id="body_header_ar" cols="30" rows="10"
                                        class="form-control @error('body_header_ar') is-invalid @enderror">{{ old('body_header_ar') }}</textarea>
                                    @error('body_header_ar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> --}}
                            {{-- Email body_footer textare --}}
                            <div class="col-md-8 col-sm-12">
                                <div class="form-group">
                                    <label for="body_footer">{{ __('Email Body Footer') }}</label>
                                    <textarea name="body_footer" id="body_footer" cols="30" rows="10"
                                        class="form-control @error('body_footer') is-invalid @enderror">{{ old('body_footer') }}</textarea>
                                    @error('body_footer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            {{-- Email body_footer_ar textare --}}
                            {{-- <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="body_footer_ar">Email Body Footer (Arabic)</label>
                                    <textarea name="body_footer_ar" id="body_footer_ar" cols="30" rows="10"
                                        class="form-control @error('body_footer_ar') is-invalid @enderror">{{ old('body_footer_ar') }}</textarea>
                                    @error('body_footer_ar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> --}}
                        </div>
                        <div class="row">
                            <div class="col-12 text-end mt-1">
                                <button type="submit" class="btn btn-primary">{{ __('Send') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
{{-- summernote cdn --}}
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>

<script>
    //summernote
    //summernote
    $(document).ready(function() {
        $('#body_header').summernote({
            placeholder: "{{ __('Email Body Header') }}",
        tabsize: 2,
        height: 120,
        toolbar: [
          ['style', ['style']],
          ['font', ['bold', 'underline', 'clear']],
          ['color', ['color']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['table', ['table']],
          ['insert', ['link', 'picture', 'video']],
          ['view', ['fullscreen',  'help']]
        ]
        });
        $('#body_header_ar').summernote({
            placeholder: "{{ __('Email Body Header in (Arabic)') }}",
        tabsize: 2,
        height: 120,
        toolbar: [
          ['style', ['style']],
          ['font', ['bold', 'underline', 'clear']],
          ['color', ['color']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['table', ['table']],
          ['insert', ['link', 'picture', 'video']],
          ['view', ['fullscreen',  'help']]
        ]
        });
        $('#body_footer').summernote({
            placeholder: "{{ __('Email Body Footer') }}",
        tabsize: 2,
        height: 120,
        toolbar: [
          ['style', ['style']],
          ['font', ['bold', 'underline', 'clear']],
          ['color', ['color']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['table', ['table']],
          ['insert', ['link', 'picture', 'video']],
          ['view', ['fullscreen',  'help']]
        ]
        });
        $('#body_footer_ar').summernote({
            placeholder: 'Email Body Footer in (Arabic)',
        tabsize: 2,
        height: 120,
        toolbar: [
          ['style', ['style']],
          ['font', ['bold', 'underline', 'clear']],
          ['color', ['color']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['table', ['table']],
          ['insert', ['link', 'picture', 'video']],
          ['view', ['fullscreen',  'help']]
        ]
        });
        setUpComapny =(sec,comp) =>{
        var SectorId = $("#"+sec).val();
        if(SectorId){
            $.ajax({
                type:"GET",
                url:"{{ url('companies/getForSelect') }}/"+SectorId,
                success:function(res){
                    if(res){
                        $("#"+comp).empty();
                        $("#"+comp).append('<option value="">{{ __("Select Company") }}</option>');
                        $.each(res,function(key,value){
                            $("#"+comp).append('<option value="'+value.id+'">'+value.company_name_en+'</option>');
                        });
                    }else{
                        $("#"+comp).empty();
                    }
                }
            });
        }else{
            $("#"+comp).empty();
        }
    }
    });
    $('form').submit(function () {
        //disable client id
        $('#client_id').attr('disabled', false);
        $('#survey_id').attr('disabled', false);
    });
</script>
@endsection
