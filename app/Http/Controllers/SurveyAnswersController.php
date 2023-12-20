<?php

namespace App\Http\Controllers;

use App\Http\Requests\SurveyAnswerStoreRequest;
use App\Http\Requests\SurveyAnswerUpdateRequest;
use App\Models\Emails;
use App\Models\freeSurveyAnswers;
use App\Models\Functions;
use App\Models\PartnerShipPlans;
use App\Models\PracticeQuestions;
use App\Models\PrioritiesAnswers;
use App\Models\SurveyAnswers;
use App\Models\Surveys;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\Break_;
use Termwind\Components\Dd;

class SurveyAnswersController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'ShowFreeResult']);
    }
    public function index(Request $request)
    {
        // $surveys = Surveys::all();
        // $free_surveys = freeSurveyAnswers::select('SurveyId')->distinct('SurveyId')->get();
        // $data = [
        //     'surveys' => $surveys,
        //     'free_surveys' => $free_surveys,
        // ];
        // return view('SurveyAnswers.index')->with($data);
        return redirect('clients');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return view('SurveyAnswers.create');
    }

    /**
     * @param \App\Http\Requests\SurveyAnswersStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(SurveyAnswerStoreRequest $request)
    {
        $surveyAnswer = SurveyAnswers::create($request->validated());

        return redirect()->route('SurveyAnswers.index');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\SurveyAnswers $surveyAnswer
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, SurveyAnswers $surveyAnswer)
    {
        return view('SurveyAnswers.show', compact('SurveyAnswer'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\SurveyAnswers $surveyAnswer
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, SurveyAnswers $surveyAnswer)
    {
        return view('SurveyAnswers.edit', compact('SurveyAnswer'));
    }

    /**
     * @param \App\Http\Requests\SurveyAnswersUpdateRequest $request
     * @param \App\Models\SurveyAnswers $surveyAnswer
     * @return \Illuminate\Http\Response
     */
    public function update(SurveyAnswerUpdateRequest $request, SurveyAnswers $surveyAnswer)
    {
        $surveyAnswer->update($request->validated());

        return redirect()->route('SurveyAnswers.index');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\SurveyAnswers $surveyAnswer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, SurveyAnswers $surveyAnswer)
    {
        $surveyAnswer->delete();

        return redirect()->route('SurveyAnswers.index');
    }
    public function result($id)
    {
        $surveyEmails = Emails::where('SurveyId', $id)->get();
        $SurveyResult = SurveyAnswers::where('SurveyId', '=', $id)->get();
        $scaleSize = 5;
        if ($SurveyResult->count() == 0 && $surveyEmails->count() == 0) {
            $data = [
                'leaders' => 1,
                'hr' => 1,
                'emp' => 1,
                'leaders_answers' => 0,
                'hr_answers' => 0,
                'emp_answers' => 0,
                'total' => 1,
                'total_answers' => 0 + 0 + 0,
            ];
            return view('SurveyAnswers.notComplet')->with($data);
        }
        $SurveyResult = $SurveyResult->map(function ($item, $key) {
            $item['AnswerValue'] = $item['AnswerValue'] - 1;
            return $item;
        });
        $leaders_email = array();
        $hr_teames_email = array();
        $employees_email = array();
        list($leaders_email, $hr_teames_email, $employees_email) = $this->newFunc($surveyEmails, $leaders_email, $hr_teames_email, $employees_email);
        $leaders_answers = SurveyAnswers::where('SurveyId', '=', $id)->whereIn('AnsweredBy', $leaders_email)->get();
        $hr_teames_answers = SurveyAnswers::where('SurveyId', '=', $id)->whereIn('AnsweredBy', $hr_teames_email)->get();
        $employees_answers = SurveyAnswers::where('SurveyId', '=', $id)->whereIn('AnsweredBy', $employees_email)->get();
        $Answers_by_leaders = collect($leaders_answers)->unique('AnsweredBy')->count();
        $Answers_by_hr = collect($hr_teames_answers)->unique('AnsweredBy')->count();
        $Answers_by_emp = collect($employees_answers)->unique('AnsweredBy')->count();
        $planID = Surveys::where('id', $id)->first()->PlanId;
        $functions = Functions::where('PlanId', $planID)->get();
        $prioritiesRes = PrioritiesAnswers::where('SurveyId', $id)->get();

        $avgxx = $SurveyResult->avg('AnswerValue');
        $overallResult = round(($avgxx / 6), 2) * 100;
        $overallResultz = round(($avgxx / $scaleSize), 2) * 100;

        $priorities = array();
        $priority = array();
        $performences_ = array();
        $performence_ = array();
        //leader
        $leader_performences_ = array();
        $leader_performence_ = array();
        //hr
        $hr_performences_ = array();
        $hr_performence_ = array();
        //emp
        $emp_performences_ = array();
        $emp_performence_ = array();

        $overall_Practices = array();
        $leaders_practices = array();
        $hr_practices = array();
        $emp_practices = array();
        $function_Lables = array();
        foreach ($functions as $function) {
            $function_Lables[] = App()->getLocale() == 'ar' ? $function->FunctionTitleAr : $function->FunctionTitle;
            $total = 0;
            $leaders_total = 0;
            $hr_total = 0;
            $emp_total = 0;
            $totalz = 0;
            $leaders_totalz = 0;
            $hr_totalz = 0;
            $emp_totalz = 0;
            $counter = 0;
            $HRcounter = 0;
            $Empcounter = 0;
            $overall_Practice = array();
            $leaders_practice = array();
            $hr_practice = array();
            $emp_practice = array();
            //leasders flag
            $leaders_had_answers = false;
            //hr flag
            $hr_had_answers = false;
            //emp flag
            $emp_had_answers = false;
            foreach ($function->functionPractices as $functionPractice) {
                //genral data
                $practiceName = App()->getLocale() == 'ar' ? $functionPractice->PracticeTitleAr : $functionPractice->PracticeTitle;

                //leaders Caluclations
                $allans = $SurveyResult->where('QuestionId', '=', $functionPractice->practiceQuestions->id)->whereIn('AnsweredBy', $leaders_email)->avg('AnswerValue');
                // leaders answers avg
                $leaders_ans_avg = $allans;
                //check if $allans has a value or just empty

                $leaders_had_answers = isset($allans) ? true : false;
                $answers = $SurveyResult->where('QuestionId', '=', $functionPractice->practiceQuestions->id)->whereIn('AnsweredBy', $leaders_email);/* ->sum('AnswerValue') ;*/
                $leaders_Pract_w =  ($allans) / $scaleSize;
                $leaders_total += $leaders_Pract_w;
                $leaders_Pract_wz =  ($allans) / $scaleSize;
                $leaders_totalz += $leaders_Pract_wz;
                if ($answers) {
                    $counter++;
                }
                $leaders_practice = [
                    'name' => $practiceName,
                    'weight' => round($leaders_Pract_w, 2),
                    'weightz' => round($leaders_Pract_wz, 2),
                    'function_id' => $function->id,
                ];
                array_push($leaders_practices, $leaders_practice);
                // Hr Caluclations
                $allans = $SurveyResult->where('QuestionId', '=', $functionPractice->practiceQuestions->id)
                    ->whereIn('AnsweredBy', $hr_teames_email)->avg('AnswerValue');
                // HrTeam answers avg
                $hr_ans_avg = $allans;
                $hr_had_answers = isset($allans) ? true : false;
                $hr_practice_ans = $SurveyResult->where('QuestionId', '=', $functionPractice->practiceQuestions->id)
                    ->whereIn('AnsweredBy', $hr_teames_email);
                $hr_practice_weight =  round((($allans) / $scaleSize), 2);
                $hr_practice_weightz =  round((($allans) / $scaleSize), 2);
                $hr_total += $hr_practice_weight;
                $hr_totalz += $hr_practice_weightz;
                if ($hr_practice_ans) {
                    $HRcounter++;
                }
                $hr_practice = [
                    'name' => $practiceName,
                    'weight' => $hr_practice_weight,
                    'weightz' => $hr_practice_weightz,
                    'function_id' => $function->id,
                ];
                array_push($hr_practices, $hr_practice);
                // Emp Caluclations
                $allans = $SurveyResult->where('QuestionId', '=', $functionPractice->practiceQuestions->id)->whereIn('AnsweredBy', $employees_email)->avg('AnswerValue');
                // employees answers avg
                $emp_ans_avg = $allans;
                //check if employee group answer avg has value and assign flag
                $emp_had_answers = isset($allans) ? true : false;
                $emp_practice_ans = $SurveyResult->where('QuestionId', '=', $functionPractice->practiceQuestions->id)->whereIn('AnsweredBy', $employees_email);
                // $emp_practice_ans_count = $SurveyResult->where('QuestionId', '=', $functionPractice->practiceQuestions->id)->whereIn('AnsweredBy', $employees_email)->count();
                $emp_practice_weight =  round((($allans) / $scaleSize), 2);
                $emp_practice_weightz =  round((($allans) / $scaleSize), 2);
                if ($emp_practice_ans) {
                    $Empcounter++;
                }
                $emp_total += $emp_practice_weight;
                $emp_totalz += $emp_practice_weightz;
                $emp_practice = [
                    'name' => $practiceName,
                    'weight' => $emp_practice_weight,
                    'weightz' => $emp_practice_weightz,
                    'function_id' => $function->id,
                ];
                array_push($emp_practices, $emp_practice);
                // over all calculations
                $the_three_avg = 0;
                $avg_factor = 0;
                if ($leaders_had_answers) {
                    $the_three_avg += $leaders_ans_avg;
                    $avg_factor++;
                }
                if ($hr_had_answers) {
                    $the_three_avg += $hr_ans_avg;
                    $avg_factor++;
                }
                if ($emp_had_answers) {
                    $the_three_avg += $emp_ans_avg;
                    $avg_factor++;
                }
                // $OverAllAv = $SurveyResult->where('QuestionId', '=', $functionPractice->practiceQuestions->id)
                //     ->avg('AnswerValue');
                $OverAllAv = ($the_three_avg) / $avg_factor;

                $practiceWeight =  round((($OverAllAv) / $scaleSize), 2);
                $practiceWeightz =  round((($OverAllAv) / $scaleSize), 2);
                $overall_Practice = [
                    'name' => $practiceName,
                    'weight' => $practiceWeight,
                    'weightz' => $practiceWeightz,
                    'function_id' => $function->id,
                ];
                array_push($overall_Practices, $overall_Practice);
                // total calculations
                $total += $leaders_Pract_w + $hr_practice_weight + $emp_practice_weight;
                $totalz += $leaders_Pract_wz + $hr_practice_weightz + $emp_practice_weightz;
            }
            //get bluck of question id through function
            $practicesIDs = $function->functionPractices->pluck('id')->toArray();
            $questionsIDs = PracticeQuestions::whereIn('PracticeId', $practicesIDs)->pluck('id')->toArray();
            //get sum of this function overalll
            //get avg of this function
            $avg = $SurveyResult->whereIn('QuestionId', $questionsIDs)->avg('AnswerValue');
            $avg = round(($avg / $scaleSize), 2);
            //get sum of this function leaders
            //get avg of this function leaders
            $avgl = $SurveyResult->whereIn('QuestionId', $questionsIDs)->whereIn('AnsweredBy', $leaders_email)->avg('AnswerValue');
            $avgl = round(($avgl / $scaleSize), 2);
            //get sum of this function hr
            //get avg of this function hr
            $avgh = $SurveyResult->whereIn('QuestionId', $questionsIDs)->whereIn('AnsweredBy', $hr_teames_email)->avg('AnswerValue');
            $avgh = round(($avgh / $scaleSize), 2);
            //get sum of this function employees
            //get avg of this function employees
            $avge = $SurveyResult->whereIn('QuestionId', $questionsIDs)->whereIn('AnsweredBy', $employees_email)->avg('AnswerValue');
            $avge = round(($avge / $scaleSize), 2);
            //overall performence


            //leader performence
            $leader_performence_ = ["function" => App()->getLocale() == 'ar' ? $function->FunctionTitleAr : $function->FunctionTitle, "function_id" => $function->id, "performance" => ($avgl * 100), "performancez" => ($avgl * 100)];
            array_push($leader_performences_, $leader_performence_);

            //hr performence

            $hr_performence_ = ["function" => App()->getLocale() == 'ar' ? $function->FunctionTitleAr : $function->FunctionTitle, "function_id" => $function->id, "performance" => ($avgh * 100), "performancez" => ($avgh * 100)];
            array_push($hr_performences_, $hr_performence_);

            //emp performence
            $emp_performence_ = ["function" => App()->getLocale() == 'ar' ? $function->FunctionTitleAr : $function->FunctionTitle, "function_id" => $function->id, "performance" => ($avge * 100), "performancez" => ($avge * 100)];
            array_push($emp_performences_, $emp_performence_);
            //prioritiesRes
            $total_answers = $prioritiesRes->where('QuestionId', $function->id)->whereIn('AnsweredBy', $leaders_email)->sum('AnswerValue');
            $count_answers = $prioritiesRes->where('QuestionId', $function->id)->whereIn('AnsweredBy', $leaders_email)->count();
            $priorityVal = $count_answers > 0 ? round((($total_answers / $count_answers) / 3), 2) : 0;
            $priority = ["priority" => $priorityVal, "function" => App()->getLocale() == 'ar' ? $function->FunctionTitleAr : $function->FunctionTitle, "function_id" => $function->id, "performance" => $avgl, "performancez" => $avgl];
            array_push($priorities, $priority);
            $performence_ = ["function" => App()->getLocale() == 'ar' ? $function->FunctionTitleAr : $function->FunctionTitle, "function_id" => $function->id, "performance" => ($avg * 100), "performancez" => ($avg * 100), 'overall_Practices' => $overall_Practices, 'leaders_practices' => $leaders_practices, 'hr_practices' => $hr_practices, 'emp_practices' => $emp_practices];
            array_push($performences_, $performence_);
        }

        $overAllpractice = $overall_Practices;
        //sorte overAllpractice Asc
        usort($overAllpractice, function ($a, $b) {
            return $a['weight'] <=> $b['weight'];
        });
        $unsorted_performences = $performences_;
        $sorted_leader_performences = $leader_performences_;
        $sorted_hr_performences = $hr_performences_;
        $sorted_emp_performences = $emp_performences_;
        //sorte sorted_leader_performences descending
        usort($sorted_leader_performences, function ($a, $b) {
            return $b['performance'] <=> $a['performance'];
        });
        //sorte sorted_hr_performences descending
        usort($sorted_hr_performences, function ($a, $b) {
            return $b['performance'] <=> $a['performance'];
        });
        //sorte sorted_emp_performences descending
        usort($sorted_emp_performences, function ($a, $b) {
            return $b['performance'] <=> $a['performance'];
        });
        //sort performances_
        usort($performences_, function ($a, $b) {
            return $a['performance'] <=> $b['performance'];
        });
        $asc_perform = $performences_;
        usort($performences_, function ($a, $b) {
            return $b['performance'] <=> $a['performance'];
        });
        $leaders_perform_only = array();
        $hr_perform_only = array();
        $leaders_perform_onlyz = array();
        $hr_perform_onlyz = array();
        $count_z = 0;
        foreach ($functions as $function) {
            if ($leader_performences_[$count_z]['function_id'] == $function->id) {
                array_push($leaders_perform_only, $leader_performences_[$count_z]['performance']);
                array_push($leaders_perform_onlyz, $leader_performences_[$count_z]['performancez']);
            }
            if ($hr_performences_[$count_z]['function_id'] == $function->id) {
                array_push($hr_perform_onlyz, $hr_performences_[$count_z++]['performancez']);
            }
        }
        $desc_perfom = $performences_;
        $data = [
            'functions' => $functions,
            'priorities' => $priorities,
            'overallResult' => $overallResult,
            'overallResultz' => $overallResultz,
            'asc_perform' => $asc_perform,
            'desc_perfom' => $desc_perfom,
            'overall_Practices' => $overall_Practices,
            'overAllpractice' => $overAllpractice,
            // 'overall_PracticesAsc' => $overall_PracticesAsc,
            'unsorted_performences' => $unsorted_performences,
            'sorted_leader_performences' => $sorted_leader_performences,
            'sorted_hr_performences' => $sorted_hr_performences,
            'sorted_emp_performences' => $sorted_emp_performences,
            'function_Lables' => $function_Lables,
            'leaders_perform_only' => $leaders_perform_only,
            'hr_perform_only' => $hr_perform_only,
            'leaders_perform_onlyz' => $leaders_perform_onlyz,
            'hr_perform_onlyz' => $hr_perform_onlyz,
            "id" => $id,
            'Resp_overAll_res' => count($surveyEmails),
            'overAll_res' => $Answers_by_leaders + $Answers_by_hr + $Answers_by_emp,
            'prop_leadersResp' => count($leaders_email),
            'prop_hrResp' => count($hr_teames_email),
            'prop_empResp' => count($employees_email),
            'leaders_res' => $Answers_by_leaders,
            'hr_res' => $Answers_by_hr,
            'emp_res' => $Answers_by_emp,
        ];
        return view('SurveyAnswers.result')->with($data);
    }
    public function ShowFreeResult($id)
    {
        $SurveyResult = freeSurveyAnswers::where('SurveyId', $id)->get();
        $functions = Functions::where('PlanId', $SurveyResult->first()->PlanId)->get();
        $overall_Practices = array();
        $sumxx = $SurveyResult->sum('Answer_value');
        $countxx = $SurveyResult->count();
        $avgxx = $sumxx / $countxx;
        $overallResult = $avgxx / 6;
        $overallResult = round($overallResult, 2) * 100;
        $performences_ = array();
        $performence_ = array();
        $hr_performences_ = array();
        $hr_performence_ = array();
        foreach ($functions as $function) {
            $total = 0;
            $counter = 0;
            $hr_total = 0;
            $overall_Practice = array();

            foreach ($function->functionPractices as $functionPractice) {

                $counter++;

                $practiceName = $functionPractice->PracticeTitle;

                $practiceAns = $SurveyResult->where('QuestionId', '=', $functionPractice->practiceQuestions->id)->sum('Answer_value');
                $practiceAnsCount = $SurveyResult->where('QuestionId', '=', $functionPractice->practiceQuestions->id)->count();
                $practiceWeight = round((($practiceAns / $practiceAnsCount) / 6), 2);

                $overall_Practice = [
                    'name' => $practiceName,
                    'weight' => $practiceWeight,
                    'function_id' => $function->id,
                ];
                $hr_practice_ans = $SurveyResult->where('QuestionId', '=', $functionPractice->practiceQuestions->id)->sum('Answer_value');
                $hr_practice_ans_count = $SurveyResult->where('QuestionId', '=', $functionPractice->practiceQuestions->id)->count();
                $hr_practice_weight = round((($hr_practice_ans / $hr_practice_ans_count) / 6), 2);
                $hr_total += $hr_practice_weight;
                $hr_practice = [
                    'name' => $practiceName,
                    'weight' => $hr_practice_weight,
                    'function_id' => $function->id,
                ];
                array_push($overall_Practices, $overall_Practice);
                $total += $practiceWeight;
            }
            $performence = round(($total / $counter), 2);

            $performence_ = ["function" => App()->getLocale() == 'ar' ? $function->FunctionTitleAr : $function->FunctionTitle, "function_id" => $function->id, "performance" => ($performence * 100), 'overall_Practices' => $overall_Practices];
            array_push($performences_, $performence_);
            $hr_performence = round(($hr_total / $counter), 2);
            $hr_performence_ = ["function" => App()->getLocale() == 'ar' ? $function->FunctionTitleAr : $function->FunctionTitle, "function_id" => $function->id, "performance" => ($hr_performence * 100)];
            array_push($hr_performences_, $hr_performence_);
        }
        //sorte performences_ ascending
        usort($performences_, function ($a, $b) {
            return $a['performance'] <=> $b['performance'];
        });
        //sorte hr_performences_ descending
        usort($hr_performences_, function ($a, $b) {
            return $b['performance'] <=> $a['performance'];
        });

        // dd($plan);
        $data = [
            'functions' => $functions,
            'SurveyResult' => $SurveyResult,
            'overall_Practices' => $overall_Practices,
            'overallResult' => $overallResult,
            'asc_perform' => $performences_,
            'sorted_hr_performences' => $hr_performences_,
        ];
        return view('SurveyAnswers.freeResult')->with($data);
    }
    function newFunc($surveyEmails, $leaders_email, $hr_teames_email, $employees_email)
    {
        foreach ($surveyEmails as $surveyEmail) {
            if ($surveyEmail->EmployeeType == 1) {
                array_push($leaders_email, $surveyEmail->id);
            }
            if ($surveyEmail->EmployeeType == 2) {
                array_push($hr_teames_email, $surveyEmail->id);
            }
            if ($surveyEmail->EmployeeType == 3) {
                array_push($employees_email, $surveyEmail->id);
            }
        }
        return [$leaders_email, $hr_teames_email, $employees_email];
    }
}
