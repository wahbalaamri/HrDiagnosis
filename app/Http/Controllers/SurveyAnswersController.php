<?php

namespace App\Http\Controllers;

use App\Http\Requests\SurveyAnswerStoreRequest;
use App\Http\Requests\SurveyAnswerUpdateRequest;
use App\Models\Companies;
use App\Models\Emails;
use App\Models\freeSurveyAnswers;
use App\Models\Functions;
use App\Models\PartnerShipPlans;
use App\Models\PracticeQuestions;
use App\Models\PrioritiesAnswers;
use App\Models\Sectors;
use App\Models\SurveyAnswers;
use App\Models\Surveys;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\Break_;
use Termwind\Components\Dd;

class SurveyAnswersController extends Controller
{
    private $number_response;
    private $id;
    private $clientID;
    private $number_emails;
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
    public function result($id, $type, $type_id = null)
    {
        ini_set('max_execution_time', 400);
        if ($type == "comp") {
            $data = $this->company_results($id, $type, $type_id);
        } elseif ($type == "sec") {
            $data = $this->sector_results($id, $type, $type_id);
        } else {
            $data = $this->group_results($id, $type, $type_id);
        }
        if ($data['data_size'] <= 0)
            return view('errors.404');
        return view('SurveyAnswers.result')->with($data);
    }
    function company_results($id, $type, $type_id = null)
    {
        $surveyEmails = Emails::where([['SurveyId', $id], ['comp_id', $type_id]])->select(['id', 'EmployeeType'])->get();
        if (count($surveyEmails) <= 0)
            return ['data_size' => 0];
        $surveyEmails_ids = $surveyEmails->pluck('id')->all();
        $SurveyResult = SurveyAnswers::where('SurveyId', '=', $id)->whereIn('AnsweredBy', $surveyEmails_ids)->select(['AnswerValue', 'QuestionId', 'AnsweredBy'])->get();
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
        $Answers_by_leaders = $SurveyResult->whereIn('AnsweredBy', $leaders_email)->unique('AnsweredBy')->count();
        $Answers_by_hr = $SurveyResult->whereIn('AnsweredBy', $hr_teames_email)->unique('AnsweredBy')->count();
        $Answers_by_emp = $SurveyResult->whereIn('AnsweredBy', $employees_email)->unique('AnsweredBy')->count();
        $HR_score = $SurveyResult->whereIn('AnsweredBy', $hr_teames_email)->avg('AnswerValue');
        $Emp_score = $SurveyResult->whereIn('AnsweredBy', $employees_email)->avg('AnswerValue');
        $Leaders_score = $SurveyResult->whereIn('AnsweredBy', $leaders_email)->avg('AnswerValue');
        $_all_score = ($HR_score + $Emp_score + $Leaders_score) / 3;
        if ($Answers_by_leaders == 0 || $Answers_by_hr == 0 || $Answers_by_emp == 0)
            return ['data_size' => 0];
        $planID = Surveys::where('id', $id)->first()->PlanId;
        $functions = Functions::where('PlanId', $planID)->select(['id', 'FunctionTitleAr', 'FunctionTitle'])->get();
        $prioritiesRes = PrioritiesAnswers::where('SurveyId', $id)->select(['AnswerValue', 'QuestionId', 'AnsweredBy'])->get();

        // $avgxx = $SurveyResult->avg('AnswerValue');
        $overallResult = number_format(($_all_score / $scaleSize) * 100);

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
            $Leader_function_flag = false;
            //hr flag
            $hr_had_answers = false;
            $hr_function_flag = false;
            //emp flag
            $emp_had_answers = false;
            $emp_function_flag = false;
            $function_w = 0;
            $p_count_ = 0;
            Log::info($function->id . ' ' . $function->FunctionTitle);

            foreach ($function->functionPractices as $functionPractice) {
                //genral data
                $practiceName = App()->getLocale() == 'ar' ? $functionPractice->PracticeTitleAr : $functionPractice->PracticeTitle;

                //leaders Caluclations
                $allans = $SurveyResult->where('QuestionId', '=', $functionPractice->practiceQuestions->id)->whereIn('AnsweredBy', $leaders_email)->avg('AnswerValue');
                // leaders answers avg
                $leaders_ans_avg = $allans;
                //check if $allans has a value or just empty
                // if (!$leaders_had_answers)
                $leaders_had_answers = isset($allans) ? true : false;
                $answers = $SurveyResult->where('QuestionId', '=', $functionPractice->practiceQuestions->id)->whereIn('AnsweredBy', $leaders_email);/* ->sum('AnswerValue') ;*/
                $leaders_Pract_w =  ($allans) / $scaleSize;
                $leaders_total += $leaders_Pract_w;
                // $leaders_Pract_wz =  ($allans) / $scaleSize;
                // $leaders_totalz += $leaders_Pract_wz;
                if ($answers) {
                    $counter++;
                }
                $leaders_practice = [
                    'name' => $practiceName,
                    'id' => $functionPractice->id,
                    'weight' => round($leaders_Pract_w, 2),
                    // 'weightz' => round($leaders_Pract_wz, 2),
                    'function_id' => $function->id,
                ];
                array_push($leaders_practices, $leaders_practice);
                // Hr Caluclations
                $allans = $SurveyResult->where('QuestionId', '=', $functionPractice->practiceQuestions->id)
                    ->whereIn('AnsweredBy', $hr_teames_email)->avg('AnswerValue');
                // HrTeam answers avg
                $hr_ans_avg = $allans;
                // if (!$hr_had_answers)
                $hr_had_answers = isset($allans) ? true : false;
                $hr_practice_ans = $SurveyResult->where('QuestionId', '=', $functionPractice->practiceQuestions->id)
                    ->whereIn('AnsweredBy', $hr_teames_email);
                $hr_practice_weight =  round((($allans) / $scaleSize), 2);
                // $hr_practice_weightz =  round((($allans) / $scaleSize), 2);
                $hr_total += $hr_practice_weight;
                // $hr_totalz += $hr_practice_weightz;
                if ($hr_practice_ans) {
                    $HRcounter++;
                }
                $hr_practice = [
                    'name' => $practiceName,
                    'id' => $functionPractice->id,
                    'weight' => $hr_practice_weight,
                    // 'weightz' => $hr_practice_weightz,
                    'function_id' => $function->id,
                ];
                array_push($hr_practices, $hr_practice);
                // Emp Caluclations
                $allans = $SurveyResult->where('QuestionId', '=', $functionPractice->practiceQuestions->id)->whereIn('AnsweredBy', $employees_email)->avg('AnswerValue');
                // employees answers avg
                $emp_ans_avg = $allans;
                //check if employee group answer avg has value and assign flag
                // if (!$emp_had_answers)
                $emp_had_answers = isset($allans) ? true : false;
                $emp_practice_ans = $SurveyResult->where('QuestionId', '=', $functionPractice->practiceQuestions->id)->whereIn('AnsweredBy', $employees_email);
                // $emp_practice_ans_count = $SurveyResult->where('QuestionId', '=', $functionPractice->practiceQuestions->id)->whereIn('AnsweredBy', $employees_email)->count();
                $emp_practice_weight =  round((($allans) / $scaleSize), 2);
                // $emp_practice_weightz =  round((($allans) / $scaleSize), 2);
                if ($emp_practice_ans) {
                    $Empcounter++;
                }
                $emp_total += $emp_practice_weight;
                // $emp_totalz += $emp_practice_weightz;
                $emp_practice = [
                    'name' => $practiceName,
                    'id' => $functionPractice->id,
                    'weight' => $emp_practice_weight,
                    'function_id' => $function->id,
                ];
                array_push($emp_practices, $emp_practice);
                // over all calculations
                $the_three_avg = 0;
                $avg_factor = 0;
                if ($leaders_had_answers) {
                    $the_three_avg += $leaders_ans_avg;
                    $avg_factor++;
                    if (!$Leader_function_flag) $Leader_function_flag = true;
                }
                if ($hr_had_answers) {
                    $the_three_avg += $hr_ans_avg;
                    $avg_factor++;
                    if (!$hr_function_flag) $hr_function_flag = true;
                }
                if ($emp_had_answers) {
                    $the_three_avg += $emp_ans_avg;
                    $avg_factor++;
                    if (!$emp_function_flag) $emp_function_flag = true;
                }

                Log::info($practiceName . ' = ' . $the_three_avg);
                Log::info('average = ' . $avg_factor);
                // $OverAllAv = $SurveyResult->where('QuestionId', '=', $functionPractice->practiceQuestions->id)
                //     ->avg('AnswerValue');
                if ($avg_factor <= 0)
                    return ['data_size' => 0];
                $OverAllAv = ($the_three_avg) / $avg_factor;
                $practiceWeight =  round((($OverAllAv) / $scaleSize), 2);
                $function_w += $practiceWeight;
                $p_count_++;
                // $practiceWeightz =  round((($OverAllAv) / $scaleSize), 2);
                $overall_Practice = [
                    'name' => $practiceName,
                    'id' => $functionPractice->id,
                    'weight' => $practiceWeight,
                    'function_id' => $function->id,
                ];
                array_push($overall_Practices, $overall_Practice);
                // total calculations
                $total += $leaders_Pract_w + $hr_practice_weight + $emp_practice_weight;
                // $totalz += $leaders_Pract_wz + $hr_practice_weightz + $emp_practice_weightz;
            }
            Log::info('=============================================');
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
            $leader_performence_ = [
                "function" => App()->getLocale() == 'ar' ? $function->FunctionTitleAr : $function->FunctionTitle,
                "function_id" => $function->id,
                "performance" => number_format($avgl * 100),
                "applicable" => $Leader_function_flag
            ];
            array_push($leader_performences_, $leader_performence_);

            //hr performence

            $hr_performence_ = [
                "function" => App()->getLocale() == 'ar' ? $function->FunctionTitleAr : $function->FunctionTitle,
                "function_id" => $function->id,
                "performance" => number_format($avgh * 100),
                "applicable" => $hr_function_flag
            ];
            array_push($hr_performences_, $hr_performence_);

            //emp performence
            $emp_performence_ = [
                "function" => App()->getLocale() == 'ar' ? $function->FunctionTitleAr : $function->FunctionTitle,
                "function_id" => $function->id,
                "performance" => number_format($avge * 100),
                "applicable" => $emp_function_flag
            ];
            array_push($emp_performences_, $emp_performence_);
            //prioritiesRes
            $total_answers = $prioritiesRes->where('QuestionId', $function->id)->whereIn('AnsweredBy', $leaders_email)->sum('AnswerValue');
            $count_answers = $prioritiesRes->where('QuestionId', $function->id)->whereIn('AnsweredBy', $leaders_email)->count();
            $priorityVal = $count_answers > 0 ? round((($total_answers / $count_answers) / 3), 2) : 0;
            $priority = ["priority" => $priorityVal, "function" => App()->getLocale() == 'ar' ? $function->FunctionTitleAr : $function->FunctionTitle, "function_id" => $function->id, "performance" => $avgl, "performance" => $avgl];
            array_push($priorities, $priority);
            $performence_ = [
                "function" => App()->getLocale() == 'ar' ? $function->FunctionTitleAr : $function->FunctionTitle,
                "function_id" => $function->id,
                "performance" => number_format(($function_w / $p_count_) * 100),
                // "performance" => ($avg * 100),
                // 'overall_Practices' => $overall_Practices,
                // 'leaders_practices' => $leaders_practices,
                // 'hr_practices' => $hr_practices,
                // 'emp_practices' => $emp_practices
            ];
            array_push($performences_, $performence_);
        }

        // $overAllpractice = $overall_Practices;
        //sorte overAllpractice Asc
        // usort($overAllpractice, function ($a, $b) {
        //     return $a['weight'] <=> $b['weight'];
        // });
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
        // usort($performences_, function ($a, $b) {
        //     return $a['performance'] <=> $b['performance'];
        // });
        $asc_perform = $performences_;
        usort($performences_, function ($a, $b) {
            return $b['performance'] <=> $a['performance'];
        });
        // $leaders_perform_only = array();
        // $hr_perform_only = array();
        $leaders_perform_onlyz = array();
        $hr_perform_onlyz = array();
        $count_z = 0;
        foreach ($functions as $function) {
            if ($leader_performences_[$count_z]['function_id'] == $function->id) {
                array_push($leaders_perform_onlyz, $leader_performences_[$count_z]['performance']);
            }
            if ($hr_performences_[$count_z]['function_id'] == $function->id) {
                array_push($hr_perform_onlyz, $hr_performences_[$count_z++]['performance']);
            }
        }
        $desc_perfom = $performences_;
        $data = [
            'data_size' => count($surveyEmails),
            'functions' => $functions,
            'priorities' => $priorities,
            'overallResult' => $overallResult,
            'asc_perform' => $asc_perform,
            'desc_perfom' => $desc_perfom,
            'overall_Practices' => $overall_Practices,
            // 'overAllpractice' => $overAllpractice,
            // 'overall_PracticesAsc' => $overall_PracticesAsc,
            // 'unsorted_performences' => $unsorted_performences,
            'sorted_leader_performences' => $sorted_leader_performences,
            'sorted_hr_performences' => $sorted_hr_performences,
            'sorted_emp_performences' => $sorted_emp_performences,
            'function_Lables' => $function_Lables,
            // 'leaders_perform_only' => $leaders_perform_only,
            // 'hr_perform_only' => $hr_perform_only,
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
            'leader_performences' => $leader_performences_,
            'hr_performences' => $hr_performences_,
            'type' => $type,
            'type_id' => $type_id,
            'entities' => null,
            'entity' => Companies::find($type_id)->company_name_en . ' Result Company-wise'
        ];
        return $data;
    }
    function sector_results($id, $type, $type_id = null)
    {
        $sector = Sectors::find($type_id);
        $sector_data = [];
        foreach ($sector->companies as $company) {
            $comp_d = $this->company_results($id, 'comp', $company->id);
            if ($comp_d['data_size'] > 0)
                array_push($sector_data, $comp_d);
        }
        $overallResult = 0; //Done
        $priorities_data = [];
        $asc_perform_data = [];
        $desc_perfom_data = [];
        $overall_Practices_data = [];
        $overAllpractice_data = [];
        $unsorted_performences_data = [];
        $sorted_leader_performences_data = [];
        $sorted_hr_performences_data = [];
        $sorted_emp_performences_data = [];
        $leader_performences_data = [];
        $hr_performences_data = [];
        $Resp_overAll_res = 0;
        $overAll_res = 0;
        $prop_leadersResp = 0;
        $prop_hrResp = 0;
        $prop_empResp = 0;
        $leaders_res = 0;
        $hr_res = 0;
        $emp_res = 0;
        foreach ($sector_data  as $comp_data) {

            $overallResult += $comp_data['overallResult'];
            $priorities_data = array_merge($priorities_data, $comp_data['priorities']);
            $asc_perform_data = array_merge($asc_perform_data, $comp_data['asc_perform']);
            $desc_perfom_data = array_merge($desc_perfom_data, $comp_data['desc_perfom']);
            $overall_Practices_data = array_merge($overall_Practices_data, $comp_data['overall_Practices']);
            $sorted_leader_performences_data = array_merge($sorted_leader_performences_data, $comp_data['sorted_leader_performences']);
            $sorted_hr_performences_data = array_merge($sorted_hr_performences_data, $comp_data['sorted_hr_performences']);
            $sorted_emp_performences_data = array_merge($sorted_emp_performences_data, $comp_data['sorted_emp_performences']);
            $leader_performences_data = array_merge($leader_performences_data, $comp_data['leader_performences']);
            $hr_performences_data = array_merge($hr_performences_data, $comp_data['hr_performences']);
            $Resp_overAll_res += $comp_data['Resp_overAll_res'];
            $overAll_res += $comp_data['overAll_res'];
            $prop_leadersResp += $comp_data['prop_leadersResp'];
            $prop_hrResp += $comp_data['prop_hrResp'];
            $prop_empResp += $comp_data['prop_empResp'];
            $leaders_res += $comp_data['leaders_res'];
            $hr_res += $comp_data['hr_res'];
            $emp_res += $comp_data['emp_res'];
        }
        if (count($sector_data) == 0)
            return ['data_size' => count($sector_data)];
        $overallResult = count($sector_data) != 0 ? number_format($overallResult / count($sector_data)) : 0;
        $functions = Surveys::find($id)->plan->functions;
        $priorities = [];
        $asc_perform = [];
        $desc_perfom = [];
        $overall_Practices = [];
        $leaders_practices = [];
        $overAllpractice = [];
        $unsorted_performences = [];
        $sorted_leader_performences = [];
        $sorted_hr_performences = [];
        $sorted_emp_performences = [];
        $leader_performences_ = [];
        $hr_performences_ = [];
        $leaders_perform_onlyz = [];
        $hr_perform_onlyz = [];
        $function_Lables = [];
        $performences_ = [];
        $hr_practices = [];
        $emp_practices = [];
        foreach ($functions as $function) {
            $function_Lables[] = App()->getLocale() == 'ar' ? $function->FunctionTitleAr : $function->FunctionTitle;
            $priority = [
                "priority" => number_format((collect($priorities_data)->where('function_id', $function->id)->sum('priority')) / count($sector_data), 2),
                "function" => App()->getLocale() == 'ar' ? $function->FunctionTitleAr : $function->FunctionTitle,
                "function_id" => $function->id,
                "performance" => number_format((collect($priorities_data)->where('function_id', $function->id)->sum('performance')) / count($sector_data), 2),
            ];
            array_push($priorities, $priority);
            $overall_Practices_data = collect($overall_Practices_data);
            // $leaders_practice_befor = collect($asc_perform_data)->where('function_id', $function->id)->first()['leaders_practices'];
            // $hr_practices_befor = collect($asc_perform_data)->where('function_id', $function->id)->first()['hr_practices'];
            // $emp_practices_befor = collect($asc_perform_data)->where('function_id', $function->id)->first()['emp_practices'];
            foreach ($function->functionPractices as $practice) {
                $practiceName = App()->getLocale() == 'ar' ? $practice->PracticeTitleAr : $practice->PracticeTitle;
                $overall_Practice = [
                    'name' => $practiceName,
                    'id' => $practice->id,
                    'weight' => number_format((collect($overall_Practices_data)->where('id', $practice->id)->where('function_id', $function->id)->sum('weight')) / count($sector_data), 2),
                    'weightz' => number_format((collect($overall_Practices_data)->where('id', $practice->id)->where('function_id', $function->id)->sum('weightz')) / count($sector_data), 2),
                    'function_id' => $function->id,
                ];
                array_push($overall_Practices, $overall_Practice);
                // $leaders_practice = [
                //     'name' => $practiceName,
                //     'id' => $practice->id,
                //     'weight' => number_format((collect($leaders_practice_befor)->where('id', $practice->id)->where('function_id', $function->id)->sum('weight')) / count($sector_data), 2),
                //     'weightz' => number_format((collect($leaders_practice_befor)->where('id', $practice->id)->where('function_id', $function->id)->sum('weightz')) / count($sector_data), 2),
                //     'function_id' => $function->id,
                // ];
                // array_push($leaders_practices, $leaders_practice);
                // $hr_practice = [
                //     'name' => $practiceName,
                //     'id' => $practice->id,
                //     'weight' => number_format((collect($hr_practices_befor)->where('id', $practice->id)->where('function_id', $function->id)->sum('weight')) / count($sector_data), 2),
                //     'weightz' => number_format((collect($hr_practices_befor)->where('id', $practice->id)->where('function_id', $function->id)->sum('weightz')) / count($sector_data), 2),
                //     'function_id' => $function->id,
                // ];
                // array_push($hr_practices, $hr_practice);
                // $emp_practice = [
                //     'name' => $practiceName,
                //     'id' => $practice->id,
                //     'weight' => number_format((collect($emp_practices_befor)->where('id', $practice->id)->where('function_id', $function->id)->sum('weight')) / count($sector_data), 2),
                //     'weightz' => number_format((collect($emp_practices_befor)->where('id', $practice->id)->where('function_id', $function->id)->sum('weightz')) / count($sector_data), 2),
                //     'function_id' => $function->id,
                // ];
                // array_push($emp_practices, $emp_practice);
            }
            $performence_ = [
                "function" => App()->getLocale() == 'ar' ? $function->FunctionTitleAr : $function->FunctionTitle,
                "function_id" => $function->id,
                "performance" => number_format((collect($asc_perform_data)->where('function_id', $function->id)->sum('performance')) / count($sector_data), 2),
                // "performance" => number_format((collect($asc_perform_data)->where('function_id', $function->id)->sum('performance')) / count($sector_data), 2),
                // 'overall_Practices' => $overall_Practices,
                // 'leaders_practices' => $leaders_practices,
                // 'hr_practices' => $hr_practices,
                // 'emp_practices' => $emp_practices
            ];
            array_push($performences_, $performence_);
            $emp_performence_ = [
                "function" => App()->getLocale() == 'ar' ? $function->FunctionTitleAr : $function->FunctionTitle,
                "function_id" => $function->id,
                "performance" => number_format((collect($sorted_emp_performences_data)->where('function_id', $function->id)->sum('performance')) / count($sector_data), 2),
                "applicable" => collect($sorted_emp_performences_data)->where('function_id', $function->id)->first()['applicable'] == 1 ? true : false
                // "performance" => number_format((collect($sorted_emp_performences_data)->where('function_id', $function->id)->sum('performance')) / count($sector_data), 2)
            ];
            array_push($sorted_emp_performences, $emp_performence_);
            $hr_performance = number_format((collect($sorted_hr_performences_data)->where('function_id', $function->id)->sum('performance')) / count($sector_data), 2);
            $hr_performence_ = [
                "function" => App()->getLocale() == 'ar' ? $function->FunctionTitleAr : $function->FunctionTitle,
                "function_id" => $function->id,
                "performance" => number_format((collect($sorted_hr_performences_data)->where('function_id', $function->id)->sum('performance')) / count($sector_data), 2),
                "applicable" => collect($sorted_hr_performences_data)->where('function_id', $function->id)->first()['applicable'] == 1 ? true : false
                // "performance" => $hr_performance
            ];
            array_push($sorted_hr_performences, $hr_performence_);
            array_push($hr_perform_onlyz, $hr_performance);
            $L_performance = number_format((collect($sorted_leader_performences_data)->where('function_id', $function->id)->sum('performance')) / count($sector_data), 2);
            $leader_performence_ = [
                "function" => App()->getLocale() == 'ar' ? $function->FunctionTitleAr : $function->FunctionTitle,
                "function_id" => $function->id,
                "performance" => number_format((collect($sorted_leader_performences_data)->where('function_id', $function->id)->sum('performance')) / count($sector_data), 2),
                "applicable" => collect($sorted_leader_performences_data)->where('function_id', $function->id)->first()['applicable'] == 1 ? true : false
                // "performance" => $L_performance
            ];
            array_push($sorted_leader_performences, $leader_performence_);
            array_push($leaders_perform_onlyz, $L_performance);
        }
        $asc_perform = $performences_;
        usort($asc_perform, function ($a, $b) {
            return $a['performance'] <=> $b['performance'];
        });
        $desc_perfom = $performences_;
        usort($desc_perfom, function ($a, $b) {
            return $b['performance'] <=> $a['performance'];
        });
        $leader_performences_ = $sorted_leader_performences;
        $hr_performences_ = $sorted_hr_performences;
        $overAllpractice = $overall_Practices;
        $data = [
            'data_size' => count($sector_data),
            'functions' => $functions,
            'priorities' => $priorities,
            'overallResult' => $overallResult,
            // 'overallResult' => $overallResult,
            'asc_perform' => $asc_perform,
            'desc_perfom' => $desc_perfom,
            'overall_Practices' => $overall_Practices,
            // 'overAllpractice' => $overAllpractice,
            // 'overall_PracticesAsc' => $overall_PracticesAsc,
            'unsorted_performences' => $unsorted_performences,
            'sorted_leader_performences' => $sorted_leader_performences,
            'sorted_hr_performences' => $sorted_hr_performences,
            'sorted_emp_performences' => $sorted_emp_performences,
            'function_Lables' => $function_Lables,
            // 'leaders_perform_only' => $leaders_perform_only,
            // 'hr_perform_only' => $hr_perform_only,
            'leaders_perform_onlyz' => $leaders_perform_onlyz,
            'hr_perform_onlyz' => $hr_perform_onlyz,
            "id" => $id,
            'Resp_overAll_res' => $Resp_overAll_res,
            'overAll_res' => $overAll_res,
            'prop_leadersResp' => $prop_leadersResp,
            'prop_hrResp' => $prop_hrResp,
            'prop_empResp' => $prop_empResp,
            'leaders_res' => $leaders_res,
            'hr_res' => $hr_res,
            'emp_res' => $emp_res,
            'leader_performences' => $leader_performences_,
            'hr_performences' => $hr_performences_,
            'type' => $type,
            'type_id' => $type_id,
            'entities' => $sector->companies,
            'entity' => $sector->sector_name_en . ' Result Sector-wise'
        ];
        return $data;
    }
    function group_results($id, $type, $type_id = null)
    {
        $sectors = Surveys::find($id)->clients->sectors;
        $sector_data = [];
        foreach ($sectors as $sector) {
            $comp_d = $this->sector_results($id, 'sec', $sector->id);
            if ($comp_d['data_size'] > 0)
                array_push($sector_data, $comp_d);
        }
        $overallResult = 0; //Done
        $priorities_data = [];
        $asc_perform_data = [];
        $desc_perfom_data = [];
        $overall_Practices_data = [];
        $overAllpractice_data = [];
        $unsorted_performences_data = [];
        $sorted_leader_performences_data = [];
        $sorted_hr_performences_data = [];
        $sorted_emp_performences_data = [];
        $leader_performences_data = [];
        $hr_performences_data = [];
        $Resp_overAll_res = 0;
        $overAll_res = 0;
        $prop_leadersResp = 0;
        $prop_hrResp = 0;
        $prop_empResp = 0;
        $leaders_res = 0;
        $hr_res = 0;
        $emp_res = 0;
        foreach ($sector_data  as $comp_data) {

            $overallResult += $comp_data['overallResult'];
            $priorities_data = array_merge($priorities_data, $comp_data['priorities']);
            $asc_perform_data = array_merge($asc_perform_data, $comp_data['asc_perform']);
            $desc_perfom_data = array_merge($desc_perfom_data, $comp_data['desc_perfom']);
            $overall_Practices_data = array_merge($overall_Practices_data, $comp_data['overall_Practices']);
            $sorted_leader_performences_data = array_merge($sorted_leader_performences_data, $comp_data['sorted_leader_performences']);
            $sorted_hr_performences_data = array_merge($sorted_hr_performences_data, $comp_data['sorted_hr_performences']);
            $sorted_emp_performences_data = array_merge($sorted_emp_performences_data, $comp_data['sorted_emp_performences']);
            $leader_performences_data = array_merge($leader_performences_data, $comp_data['leader_performences']);
            $hr_performences_data = array_merge($hr_performences_data, $comp_data['hr_performences']);
            $Resp_overAll_res += $comp_data['Resp_overAll_res'];
            $overAll_res += $comp_data['overAll_res'];
            $prop_leadersResp += $comp_data['prop_leadersResp'];
            $prop_hrResp += $comp_data['prop_hrResp'];
            $prop_empResp += $comp_data['prop_empResp'];
            $leaders_res += $comp_data['leaders_res'];
            $hr_res += $comp_data['hr_res'];
            $emp_res += $comp_data['emp_res'];
        }
        $overallResult = number_format($overallResult / count($sector_data));
        $functions = Surveys::find($id)->plan->functions;
        $priorities = [];
        $asc_perform = [];
        $desc_perfom = [];
        $overall_Practices = [];
        $leaders_practices = [];
        $overAllpractice = [];
        $unsorted_performences = [];
        $sorted_leader_performences = [];
        $sorted_hr_performences = [];
        $sorted_emp_performences = [];
        $leader_performences_ = [];
        $hr_performences_ = [];
        $leaders_perform_onlyz = [];
        $hr_perform_onlyz = [];
        $function_Lables = [];
        $performences_ = [];
        $hr_practices = [];
        $emp_practices = [];
        foreach ($functions as $function) {
            $function_Lables[] = App()->getLocale() == 'ar' ? $function->FunctionTitleAr : $function->FunctionTitle;
            $priority = [
                "priority" => number_format((collect($priorities_data)->where('function_id', $function->id)->sum('priority')) / count($sector_data), 2),
                "function" => App()->getLocale() == 'ar' ? $function->FunctionTitleAr : $function->FunctionTitle,
                "function_id" => $function->id,
                "performance" => number_format((collect($priorities_data)->where('function_id', $function->id)->sum('performance')) / count($sector_data), 2),
                // "performance" => number_format((collect($priorities_data)->where('function_id', $function->id)->sum('performance')) / count($sector_data), 2)
            ];
            array_push($priorities, $priority);
            $overall_Practices_data = collect($overall_Practices_data);
            // $leaders_practice_befor = collect($asc_perform_data)->where('function_id', $function->id)->first()['leaders_practices'];
            // $hr_practices_befor = collect($asc_perform_data)->where('function_id', $function->id)->first()['hr_practices'];
            // $emp_practices_befor = collect($asc_perform_data)->where('function_id', $function->id)->first()['emp_practices'];
            foreach ($function->functionPractices as $practice) {
                $practiceName = App()->getLocale() == 'ar' ? $practice->PracticeTitleAr : $practice->PracticeTitle;
                $overall_Practice = [
                    'name' => $practiceName,
                    'id' => $practice->id,
                    'weight' => number_format((collect($overall_Practices_data)->where('id', $practice->id)->where('function_id', $function->id)->sum('weight')) / count($sector_data), 2),
                    'weightz' => number_format((collect($overall_Practices_data)->where('id', $practice->id)->where('function_id', $function->id)->sum('weightz')) / count($sector_data), 2),
                    'function_id' => $function->id,
                ];
                array_push($overall_Practices, $overall_Practice);
                // $leaders_practice = [
                //     'name' => $practiceName,
                //     'id' => $practice->id,
                //     'weight' => number_format((collect($leaders_practice_befor)->where('id', $practice->id)->where('function_id', $function->id)->sum('weight')) / count($sector_data), 2),
                //     'weightz' => number_format((collect($leaders_practice_befor)->where('id', $practice->id)->where('function_id', $function->id)->sum('weightz')) / count($sector_data), 2),
                //     'function_id' => $function->id,
                // ];
                // array_push($leaders_practices, $leaders_practice);
                // $hr_practice = [
                //     'name' => $practiceName,
                //     'id' => $practice->id,
                //     'weight' => number_format((collect($hr_practices_befor)->where('id', $practice->id)->where('function_id', $function->id)->sum('weight')) / count($sector_data), 2),
                //     'weightz' => number_format((collect($hr_practices_befor)->where('id', $practice->id)->where('function_id', $function->id)->sum('weightz')) / count($sector_data), 2),
                //     'function_id' => $function->id,
                // ];
                // array_push($hr_practices, $hr_practice);
                // $emp_practice = [
                //     'name' => $practiceName,
                //     'id' => $practice->id,
                //     'weight' => number_format((collect($emp_practices_befor)->where('id', $practice->id)->where('function_id', $function->id)->sum('weight')) / count($sector_data), 2),
                //     'weightz' => number_format((collect($emp_practices_befor)->where('id', $practice->id)->where('function_id', $function->id)->sum('weightz')) / count($sector_data), 2),
                //     'function_id' => $function->id,
                // ];
                // array_push($emp_practices, $emp_practice);
            }
            $performence_ = [
                "function" => App()->getLocale() == 'ar' ? $function->FunctionTitleAr : $function->FunctionTitle,
                "function_id" => $function->id,
                "performance" => number_format((collect($asc_perform_data)->where('function_id', $function->id)->sum('performance')) / count($sector_data), 2),
                // "performance" => number_format((collect($asc_perform_data)->where('function_id', $function->id)->sum('performance')) / count($sector_data), 2),
                // 'overall_Practices' => $overall_Practices,
                // 'leaders_practices' => $leaders_practices,
                // 'hr_practices' => $hr_practices,
                // 'emp_practices' => $emp_practices
            ];
            array_push($performences_, $performence_);
            $emp_performence_ = [
                "function" => App()->getLocale() == 'ar' ? $function->FunctionTitleAr : $function->FunctionTitle,
                "function_id" => $function->id,
                "performance" => number_format((collect($sorted_emp_performences_data)->where('function_id', $function->id)->sum('performance')) / count($sector_data), 2),
                "applicable" => collect($sorted_emp_performences_data)->where('function_id', $function->id)->first()['applicable'] == 1 ? true : false
                // "performance" => number_format((collect($sorted_emp_performences_data)->where('function_id', $function->id)->sum('performance')) / count($sector_data), 2)
            ];
            array_push($sorted_emp_performences, $emp_performence_);
            $hr_performance = number_format((collect($sorted_hr_performences_data)->where('function_id', $function->id)->sum('performance')) / count($sector_data), 2);
            $hr_performence_ = [
                "function" => App()->getLocale() == 'ar' ? $function->FunctionTitleAr : $function->FunctionTitle,
                "function_id" => $function->id,
                // "performance" => number_format((collect($sorted_hr_performences_data)->where('function_id', $function->id)->sum('performance')) / count($sector_data), 2),
                "performance" => $hr_performance,
                "applicable" => collect($sorted_hr_performences_data)->where('function_id', $function->id)->first()['applicable'] == 1 ? true : false
            ];
            array_push($sorted_hr_performences, $hr_performence_);
            array_push($hr_perform_onlyz, $hr_performance);
            $L_performance = number_format((collect($sorted_leader_performences_data)->where('function_id', $function->id)->sum('performance')) / count($sector_data), 2);
            $leader_performence_ = [
                "function" => App()->getLocale() == 'ar' ? $function->FunctionTitleAr : $function->FunctionTitle,
                "function_id" => $function->id,
                // "performance" => number_format((collect($sorted_leader_performences_data)->where('function_id', $function->id)->sum('performance')) / count($sector_data), 2),
                "performance" => $L_performance,
                "applicable" => collect($sorted_leader_performences_data)->where('function_id', $function->id)->first()['applicable'] == 1 ? true : false
            ];
            array_push($sorted_leader_performences, $leader_performence_);
            array_push($leaders_perform_onlyz, $L_performance);
        }
        $asc_perform = $performences_;
        usort($asc_perform, function ($a, $b) {
            return $a['performance'] <=> $b['performance'];
        });
        $desc_perfom = $performences_;
        usort($desc_perfom, function ($a, $b) {
            return $b['performance'] <=> $a['performance'];
        });
        $leader_performences_ = $sorted_leader_performences;
        $hr_performences_ = $sorted_hr_performences;
        $overAllpractice = $overall_Practices;
        $data = [
            'data_size' => count($sector_data),
            'functions' => $functions,
            'priorities' => $priorities,
            'overallResult' => $overallResult,
            // 'overallResult' => $overallResult,
            'asc_perform' => $asc_perform,
            'desc_perfom' => $desc_perfom,
            'overall_Practices' => $overall_Practices,
            // 'overAllpractice' => $overAllpractice,
            // 'overall_PracticesAsc' => $overall_PracticesAsc,
            // 'unsorted_performences' => $unsorted_performences,
            'sorted_leader_performences' => $sorted_leader_performences,
            'sorted_hr_performences' => $sorted_hr_performences,
            'sorted_emp_performences' => $sorted_emp_performences,
            'function_Lables' => $function_Lables,
            // 'leaders_perform_only' => $leaders_perform_only,
            // 'hr_perform_only' => $hr_perform_only,
            'leaders_perform_onlyz' => $leaders_perform_onlyz,
            'hr_perform_onlyz' => $hr_perform_onlyz,
            "id" => $id,
            'Resp_overAll_res' => $Resp_overAll_res,
            'overAll_res' => $overAll_res,
            'prop_leadersResp' => $prop_leadersResp,
            'prop_hrResp' => $prop_hrResp,
            'prop_empResp' => $prop_empResp,
            'leaders_res' => $leaders_res,
            'hr_res' => $hr_res,
            'emp_res' => $emp_res,
            'leader_performences' => $leader_performences_,
            'hr_performences' => $hr_performences_,
            'type' => $type,
            'type_id' => $type_id,
            'entities' => $sectors,
            'entity' => "AL-Zubair Group Result Organizational-wise"
        ];
        return $data;
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
                    'id' => $functionPractice->id,
                    'weight' => $practiceWeight,
                    'function_id' => $function->id,
                ];
                $hr_practice_ans = $SurveyResult->where('QuestionId', '=', $functionPractice->practiceQuestions->id)->sum('Answer_value');
                $hr_practice_ans_count = $SurveyResult->where('QuestionId', '=', $functionPractice->practiceQuestions->id)->count();
                $hr_practice_weight = round((($hr_practice_ans / $hr_practice_ans_count) / 6), 2);
                $hr_total += $hr_practice_weight;
                $hr_practice = [
                    'name' => $practiceName,
                    'id' => $functionPractice->id,
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
    function statistics($id, $clientID)
    {
        //get all emails where survey id =$id
        $number_all_respondent = 0;
        $minutes = 5;
        $this->id = $id;
        $this->clientID = $clientID;
        $number_all_respondent =  Emails::where('SurveyId', $id)->count();
        // Log::info($number_all_respondent);
        // $number_all_respondent = 5551;
        $number_all_respondent_answers = Cache::remember('number_all_respondent_answers', $minutes, function () use ($id) {
            return SurveyAnswers::where('SurveyId', $id)->distinct('AnsweredBy')->count();
        });
        //get all sectors where client id =clientID
        $sectors = Cache::remember('sectors', $minutes, function () use ($clientID) {
            return Sectors::where('client_id', $clientID)->get();
        });
        //pluck sector IDs to an array
        $sectors_ids = $sectors->pluck('id')->all();
        //get all companies for each sector
        $companies_list = collect([]);
        //get all departments
        $dep_list = collect([]);
        foreach ($sectors as $sector) {
            $companies = $sector->companies;
            //push companies to  companies_list object
            $companies_list = $companies_list->merge($companies);
            foreach ($companies as $company) {
                $dep_list = $dep_list->merge($company->departments);
            }
        }
        //pluck companies IDs to an array
        $companies_ids = $companies_list->pluck('id')->all();
        //pluck departments IDs to an array
        $dep_ids = $dep_list->pluck('id')->all();

        //get all emails where survey id =$id and foreach sectors
        $sector_emails = [];

        $companies_emails = [];
        $sectors_details = [];
        $companies_details = [];
        //sector-wise statistics
        foreach ($sectors as $sector) {
            $sector_emails = [];
            $number_of_emails = 0;
            switch ($sector->sector_name_en) {
                case 'THE ZUBAIR CORPORATION':
                    $number_of_emails = 84;
                    break;
                case 'Zubair Investments':
                    $number_of_emails = 125;
                    break;
                case 'Digitizationan & Information Technology':
                    $number_of_emails = 93;
                    break;
                case 'Education':
                    $number_of_emails = 377;
                    break;
                case 'Energy & Natural Resources':
                    $number_of_emails = 220;
                    break;
                case 'Fast Moving Consumer Good':
                    $number_of_emails = 2451;
                    break;
                case 'Industrial & Chemical':
                    $number_of_emails = 574;
                    break;
                case 'Mobility & Equipment':
                    $number_of_emails = 686;
                    break;
                case 'Real Estate':
                    $number_of_emails = 80;
                    break;
                case 'Smart Electrification & Automation':
                    $number_of_emails = 867;
                    break;
                case 'Other':
                    $number_of_emails = 21;
                    break;
                default:
                    $number_of_emails = 1;
                    break;
            }
            $Sector_emails_count = Emails::where([['SurveyId', $id], ['sector_id', $sector->id]])->count();
            $this->number_emails += $Sector_emails_count;
            foreach (Emails::where([['SurveyId', $id], ['sector_id', $sector->id]])->get() as $em) {
                array_push($sector_emails, $em->id);
            }
            $Sector_emails_count > 0 ?
                $this->number_response = SurveyAnswers::where('SurveyId', $id)->whereIn('AnsweredBy', $sector_emails)->distinct('AnsweredBy')->count('AnsweredBy') : 0;
            // chunck survey answers to get count

            $sector_details = [
                'sector_name' => App()->getLocale() == 'en' ? $sector->sector_name_en : $sector->sector_name_ar,
                'sector_id' => $sector->id,
                'sector_emails' => $Sector_emails_count,
                'sector_answers' => $this->number_response,
            ];
            array_push($sectors_details, $sector_details);
            foreach ($sector->companies as $company) {
                //pluck of id
                $number_of_emails_comp = 0;
                // Log::info("Survey: " . $id);
                // Log::info("Sector: " . $sector->id);
                // Log::info("Company: " . $company->id);

                $emails_comp = Emails::where([['SurveyId', $id], ['sector_id', $sector->id], ['comp_id', $company->id]])->pluck('id')->all();
                $this->number_response = SurveyAnswers::where('SurveyId', $id)->whereIn('AnsweredBy', $emails_comp)->distinct('AnsweredBy')->count('AnsweredBy');
                if (count($emails_comp) > 0) {
                    Log::info("I am inside");
                    switch ($company->company_name_en) {
                        case 'The Zubair Corporation':
                            $number_of_emails_comp = 76;
                            break;
                        case 'BAZF':
                            $number_of_emails_comp = 34;
                            break;
                        case 'JO':
                            $number_of_emails_comp = 15;
                            break;
                        case 'PC-Imaging':
                            $number_of_emails_comp = 9;
                            break;
                        case 'PHOTOCENTRE':
                            $number_of_emails_comp = 12;
                            break;
                        case 'SPARK':
                            $number_of_emails_comp = 11;
                            break;
                        case 'OMAN COMPUTER SERVICES LLC':
                            $number_of_emails_comp = 93;
                            break;
                        case 'Azzan Bin Qais International School':
                            $number_of_emails_comp = 130;
                            break;
                        case 'As Seeb International School':
                            $number_of_emails_comp = 124;
                            break;
                        case 'Sohar International School':
                            $number_of_emails_comp = 116;
                            break;
                        case 'ARA PETROLEUM OMAN B44 LIMITED':
                            $number_of_emails_comp = 63;
                            break;
                        case 'ARA Petroleum E&P LLC':
                            $number_of_emails_comp = 154;
                            break;
                        case 'AL MUZN':
                            $number_of_emails_comp = 59;
                            break;
                        case 'OLC':
                            $number_of_emails_comp = 51;
                            break;
                        case 'OWC':
                            $number_of_emails_comp = 1076;
                            break;
                        case 'Mobility & Equipment':
                            $number_of_emails_comp = 686;
                            break;
                        case 'Romana Water':
                            $number_of_emails_comp = 716;
                            break;
                        case 'Al Arabiya Mineral Water and Packaging Factory':
                            $number_of_emails_comp = 549;
                            break;
                        case 'ELCO':
                            $number_of_emails_comp = 289;
                            break;
                        case 'Jaidah Energy LLC':
                            $number_of_emails_comp = 84;
                            break;
                        case 'Oman Oil Industry Supplies & Services Company LLC':
                            $number_of_emails_comp = 148;
                            break;
                        case 'Solentis':
                            $number_of_emails_comp = 25;
                            break;
                        case 'GAC':
                            $number_of_emails_comp = 475;
                            break;
                        case 'IHE':
                            $number_of_emails_comp = 139;
                            break;
                        case 'SRT':
                            $number_of_emails_comp = 33;
                            break;
                        case 'ZAG':
                            $number_of_emails_comp = 44;
                            break;
                        case 'Barr Al Jissah':
                            $number_of_emails_comp = 21;
                            break;
                        case 'HEMZ UAE':
                            $number_of_emails_comp = 5;
                            break;
                        case 'INMA PROPERTY DEVELOPMENT LLC':
                            $number_of_emails_comp = 47;
                            break;
                        case 'ZUBAIR ELECTRIC':
                            $number_of_emails_comp = 53;
                            break;
                        case 'Federal Transformers & Switchgears LLC':
                            $number_of_emails_comp = 67;
                            break;
                        case 'Business International Group LLC':
                            $number_of_emails_comp = 86;
                            break;
                        case 'AL ZUBAIR GENERAL TRADING LLC':
                            $number_of_emails_comp = 134;
                            break;
                        case 'ZAKHER ELECTRIC WARE EST':
                            $number_of_emails_comp = 16;
                            break;
                        case 'AL ZUBAIR ELECTRICAL APPLIANCES':
                            $number_of_emails_comp = 12;
                            break;
                        case 'SPECTRA INTERNATIONAL':
                            $number_of_emails_comp = 42;
                            break;
                        case 'ZEEMAN SERVICES & SOLUTIONS WLL':
                            $number_of_emails_comp = 14;
                            break;
                        case '4000-Federal Transformers Company LLC':
                            $number_of_emails_comp = 388;
                            break;
                        case 'Zakher Education Development Company':
                            $number_of_emails_comp = 7;
                            break;
                        case 'WILMAR INTERNATIONAL LLC':
                            $number_of_emails_comp = 4;
                            break;
                        case 'AL ZUBAIR TRADING ESTABLISHMENT':
                            $number_of_emails_comp = 6;
                            break;
                        case 'ARA Petroleum LLC':
                            $number_of_emails_comp = 3;
                            break;
                        case '4010-Federal Power Transformers LLC':
                            $number_of_emails_comp = 83;
                            break;
                        default:
                            $number_of_emails_comp = count($emails_comp);;
                            break;
                    }
                    $company_details = [
                        'company_name' => App()->getLocale() == 'en' ? $company->company_name_en : $company->company_name_ar,
                        'company_id' => $company->id,
                        //get all emails with comp_id = $company->id
                        'company_emails' => count($emails_comp),
                        'company_answers' => $this->number_response,
                        //sector name
                        'sector_name' => App()->getLocale() == 'en' ? $company->sectors->sector_name_en : $company->sectors->sector_name_ar,
                        //response rate
                        'response_rate' => round(($this->number_response / count($emails_comp)), 2) * 100,

                    ];
                    array_push($companies_details, $company_details);
                } else {
                    // Log::info("I am not inside");
                }
            }
        }
        //company-wise statistics

        $data = [
            'number_all_respondent' => $number_all_respondent,
            'number_all_respondent_answers' => $number_all_respondent_answers,
            'sectors' => $sectors->count(),
            'companies' => count($companies_list),
            'departments' => count($dep_list),
            'id' => $id,
            'sectors_details' => $sectors_details,
            'company_details' => $companies_details,
        ];
        return view('SurveyAnswers.statistics')->with($data);
    }
    function alzubair_result($id, $type, $type_id)
    {
        $data = [];
        if ($type == 'comp') {
            $data = $this->getCompanyResult($id, $type, $type_id);
        }
        return view('SurveyAnswers.result-new')->with($data);
    }
    function getCompanyResult($id, $type, $type_id)
    {
        // ==============================================================
        // Declerations
        $scaleSize = 5;
        $leaders_email = array();
        $hr_teames_email = array();
        $employees_email = array();
        $overall_Practices = array();
        $leaders_practices = array();
        $hr_practices = array();
        $emp_practices = array();
        $planID = Surveys::where('id', $id)->first()->PlanId;
        $functions = Functions::where('PlanId', $planID)->select(['id', 'FunctionTitleAr', 'FunctionTitle'])->get();
        // End Declerations
        // ==============================================================
        $surveyEmails = Emails::where([['SurveyId', $id], ['comp_id', $type_id]])->select(['id', 'EmployeeType'])->get();
        //get ID's from $surveyEmails
        $surveyEmails_ids = $surveyEmails->pluck('id')->all();
        $SurveyResult = SurveyAnswers::where('SurveyId', '=', $id)->whereIn('AnsweredBy', $surveyEmails_ids)->select(['AnswerValue', 'QuestionId', 'AnsweredBy'])->get();
        $SurveyResult = $SurveyResult->map(function ($item, $key) {
            $item['AnswerValue'] = $item['AnswerValue'] - 1;
            return $item;
        });
        list($leaders_email, $hr_teames_email, $employees_email) = $this->newFunc($surveyEmails, $leaders_email, $hr_teames_email, $employees_email);
        $Answers_by_leaders = $SurveyResult->whereIn('AnsweredBy', $leaders_email)->unique('AnsweredBy')->count();
        $Answers_by_hr = $SurveyResult->whereIn('AnsweredBy', $hr_teames_email)->unique('AnsweredBy')->count();
        $Answers_by_emp = $SurveyResult->whereIn('AnsweredBy', $employees_email)->unique('AnsweredBy')->count();
        foreach ($functions as $function) {
            $counter = 0;
            $HRcounter = 0;
            $leaders_total = 0;
            $emp_total = 0;
            $Empcounter = 0;
            $hr_total = 0;
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
                if ($answers) {
                    $counter++;
                }
                $leaders_practice = [
                    'name' => $practiceName,
                    'id' => $functionPractice->id,
                    'weight' => round($leaders_Pract_w, 2),
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
                $hr_total += $hr_practice_weight;
                if ($hr_practice_ans) {
                    $HRcounter++;
                }
                $hr_practice = [
                    'name' => $practiceName,
                    'id' => $functionPractice->id,
                    'weight' => $hr_practice_weight,
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
                $emp_practice_weight =  round((($allans) / $scaleSize), 2);
                if ($emp_practice_ans) {
                    $Empcounter++;
                }
                $emp_total += $emp_practice_weight;
                $emp_practice = [
                    'name' => $practiceName,
                    'id' => $functionPractice->id,
                    'weight' => $emp_practice_weight,
                    'function_id' => $function->id,
                ];
                array_push($emp_practices, $emp_practice);
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
                if ($avg_factor <= 0)
                    return ['data_size' => 0];
                $OverAllAv = ($the_three_avg) / $avg_factor;
                $practiceWeight =  round((($OverAllAv) / $scaleSize), 2);
                $overall_Practice = [
                    'name' => $practiceName,
                    'id' => $functionPractice->id,
                    'weight' => $practiceWeight,
                    'function_id' => $function->id,
                ];
                array_push($overall_Practices, $overall_Practice);
            }
        }
        $data = [
            'Resp_overAll_res' => count($surveyEmails),
            'overAll_res' => $Answers_by_leaders + $Answers_by_hr + $Answers_by_emp,
            'prop_leadersResp' => count($leaders_email),
            'prop_hrResp' => count($hr_teames_email),
            'prop_empResp' => count($employees_email),
            'leaders_res' => $Answers_by_leaders,
            'hr_res' => $Answers_by_hr,
            'emp_res' => $Answers_by_emp,
            'functions' => $functions,
            'overall_Practices' => $overall_Practices
        ];
        return $data;
    }
}
