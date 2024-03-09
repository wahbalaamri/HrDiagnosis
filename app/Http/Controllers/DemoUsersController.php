<?php

namespace App\Http\Controllers;

use App\Models\DemoPrioritiesAnswers;
use App\Models\DemoSurveyAnswers;
use App\Models\DemoUsers;
use App\Models\Functions;
use App\Models\PracticeQuestions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DemoUsersController extends Controller
{
    //
    //index function to show registration form for new demo users
    public function index()
    {
        return view('demoUsers.index');
    }
    function store(Request $request)
    {
        //validate the request
        $request->validate([
            'email' => 'required|email|unique:demo_users,email',
            'client_mobile' => 'unique:demo_users,mobile',
        ]);
        //messages
        $messages = [
            'email.required' => 'Please enter your email address',
            'email.email' => 'Please enter a valid email address',
            'email.unique' => 'This email address is already registered',
            'client_mobile.unique' => 'This mobile number is already registered',

        ];
        //attributes
        $attributes = [
            'email' => 'Email Address',
            //client_mobile
            'client_mobile' => 'Mobile Number',
        ];
        //create a new demo user
        $demoUser = DemoUsers::create([
            //id auto uuid

            'email' => $request->email,
            //name
            'name' => $request->client_name,
            //focal point
            'focal_point_name' => $request->focal_point_name,
            //client_mobile
            'mobile' => $request->client_mobile,
            //country
            'country' => $request->country,
        ]);
        return redirect()->route('demo.CP', ['id' => $demoUser->id]);
    }
    function CP($id)
    {
        return view('demoUsers.CP', ['id' => $id]);
    }
    //function to show the demo user dashboard
    function survey($id, $type)
    {
        $emailDetails = DemoUsers::find($id);
        Log::info($id);
        if ($emailDetails == null) {
            return view('errors.404');
        }
        $answerBythisEmail = DemoSurveyAnswers::where([['AnsweredBy', $id], ['type', $type]])->count();
        if ($answerBythisEmail > 0) {
            return view('errors.completed');
        }

        $functions = Functions::where([['Status', '=', 1], ['PlanId', '=', 4]])->get();
        $can_ansewer_to_priorities = false;
        foreach ($functions as $function) {
            if ($type == 3) {
                if ($function->Respondent == 2 || $function->Respondent == 4 || $function->Respondent == 5 || $function->Respondent == 7 || $function->Respondent == 8) {
                    $can_ansewer_to_priorities = true;
                }
            }
            if ($type == 2) {
                if ($function->Respondent == 1 || $function->Respondent == 4 || $function->Respondent == 6 || $function->Respondent == 7 || $function->Respondent == 8) {
                    $can_ansewer_to_priorities = true;
                }
            }
            if ($type == 1) {
                if ($function->Respondent == 3 || $function->Respondent == 5 || $function->Respondent == 6 || $function->Respondent == 7 || $function->Respondent == 8) {
                    $can_ansewer_to_priorities = true;
                }
            }
        }
        //setLocale ar
        $data = [
            'functions' => $functions,
            'user_type' => $type,
            'can_ansewer_to_priorities' => $can_ansewer_to_priorities,
            'SurveyId' => null,
            'email_id' => $id,
            'plan_id' => null,
            'open_end_q' => null,
            'type' => $type,
        ];
        return view('demoUsers.survey')->with($data);
    }
    //function to store the survey answers
    function saveAnswer(Request $request)
    {
        $reply = ($request->reply);
        $QuestionAnswers = $reply[0]['answers'];
        $SurveyId = $reply[0]['survey_id'];
        $PlanId = $reply[0]['PlanID'];
        $EmailId = $reply[0]['EmailId'];
        $priorities = $reply[0]['priorities'];
        $gender = $reply[0]['gender'];
        $agegroup = $reply[0]['agegroup'];
        $type = $reply[0]['type'];
        $ansAva = DemoSurveyAnswers::where([['AnsweredBy', $EmailId], ['type', $type]])->get();
        if (count($ansAva) == 0) {
            foreach ($QuestionAnswers as $key => $value) {
                $survey_answer = new DemoSurveyAnswers();
                $survey_answer->type = $type;
                $survey_answer->AnsweredBy = $EmailId;
                $survey_answer->QuestionId = $value['question_id'];
                $survey_answer->AnswerValue = $value['answer'];
                $survey_answer->save();
            }
            if ($priorities != null) {
                foreach ($priorities as $key => $value) {
                    $Priority_answer = new DemoPrioritiesAnswers();
                    $Priority_answer->AnsweredBy = $EmailId;
                    $Priority_answer->QuestionId = $value['functionId'];
                    $Priority_answer->AnswerValue = $value['priority'];
                    $Priority_answer->save();
                }
            }
        }
        $data = [
            'msg' => 'success',
            'message' => 'Your answers has been saved successfully',
            'url' => '',
        ];
        return response()->json($data);
    }
    function sendMailToUser(Request $request)
    {
        $email = $request->email;
        //Find the Demo user by email
        $demoUser = DemoUsers::where('email', $email)->first();
        if ($demoUser == null) {
            //return with back with error message
            return back()->with('error', 'This email address is not registered');
        }
        //send the email to the user
        $data = [
            'id' => $demoUser->id,
        ];

        $job = (new \App\Jobs\SendDemoLinkEmail($data, $email))
            ->delay(now()->addSeconds(2));

        dispatch($job);
        //return home with success message
        return redirect()->route('home.index')->with('success', 'The email has been sent successfully');
        // sleep(2);
        // }il to the user

    }
    //CPForm
    function CPForm()
    {
        return view('demoUsers.sendCPForm');
    }

    //result
    function result($id)
    {
        $scaleSize = 5;
        $emailDetails = DemoUsers::find($id);
        if ($emailDetails == null) {
            return view('errors.404');
        }
        $SurveyResult = DemoSurveyAnswers::where('AnsweredBy', $id)->select(['AnswerValue', 'QuestionId', 'AnsweredBy','type'])->get();
        if ($SurveyResult->count() == 0) {
            $data = [
                'leaders' => 1,
                'hr' => 1,
                'emp' => 1,
                'leaders_answers' => 0,
                'hr_answers' => 0,
                'emp_answers' => 0,
                'total' => 3,
                'total_answers' => 0 + 0 + 0,
            ];
            return view('SurveyAnswers.notComplet')->with($data);
        }
        $SurveyResult = $SurveyResult->map(function ($item, $key) {
            $item['AnswerValue'] = $item['AnswerValue'] - 1;
            return $item;
        });
        $HR_score = $SurveyResult->where('type', 2)->avg('AnswerValue');
        $Emp_score = $SurveyResult->where('type', 3)->avg('AnswerValue');
        $Leaders_score = $SurveyResult->where('type', 1)->avg('AnswerValue');
        $_all_score = ($HR_score + $Emp_score + $Leaders_score) / 3;
        if ($Leaders_score == null /* || $HR_score == null || $Emp_score == null */) {
            $data = [
                'leaders' => 1,
                'hr' => 1,
                'emp' => 1,
                'leaders_answers' => 0,
                'hr_answers' => 0,
                'emp_answers' => 0,
                'total' => 3,
                'total_answers' => 0 + 0 + 0,
            ];
            return view('SurveyAnswers.notComplet')->with($data);
        }
        $functions = Functions::where([['Status', '=', 1], ['PlanId', '=', 4]])->get();
        $prioritiesRes = DemoPrioritiesAnswers::where('AnsweredBy', $id)->select(['AnswerValue', 'QuestionId', 'AnsweredBy'])->get();
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

            foreach ($function->functionPractices as $functionPractice) {
                //genral data
                $practiceName = App()->getLocale() == 'ar' ? $functionPractice->PracticeTitleAr : $functionPractice->PracticeTitle;

                //leaders Caluclations
                $allans = $SurveyResult->where('QuestionId', '=', $functionPractice->practiceQuestions->id)->where('type', 1)->avg('AnswerValue');
                // leaders answers avg
                $leaders_ans_avg = $allans;
                //check if $allans has a value or just empty
                // if (!$leaders_had_answers)
                $leaders_had_answers = isset($allans) ? true : false;
                $answers = $SurveyResult->where('QuestionId', '=', $functionPractice->practiceQuestions->id)->where('type', 1);/* ->sum('AnswerValue') ;*/
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
                    ->where('type', 2)->avg('AnswerValue');
                // HrTeam answers avg
                $hr_ans_avg = $allans;
                // if (!$hr_had_answers)
                $hr_had_answers = isset($allans) ? true : false;
                $hr_practice_ans = $SurveyResult->where('QuestionId', '=', $functionPractice->practiceQuestions->id)
                    ->where('type', 2);
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
                $allans = $SurveyResult->where('QuestionId', '=', $functionPractice->practiceQuestions->id)->where('type', 3)->avg('AnswerValue');
                // employees answers avg
                $emp_ans_avg = $allans;
                //check if employee group answer avg has value and assign flag
                // if (!$emp_had_answers)
                $emp_had_answers = isset($allans) ? true : false;
                $emp_practice_ans = $SurveyResult->where('QuestionId', '=', $functionPractice->practiceQuestions->id)->where('type', 3);
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
                    if (!$Leader_function_flag) {
                        $p_count_++;
                        $Leader_function_flag = true;
                    }
                }
                if ($hr_had_answers) {
                    $the_three_avg += $hr_ans_avg;
                    $avg_factor++;
                    if (!$hr_function_flag) {
                        $p_count_++;
                        $hr_function_flag = true;
                    }
                }
                if ($emp_had_answers) {
                    $the_three_avg += $emp_ans_avg;
                    $avg_factor++;
                    if (!$emp_function_flag) {
                        $p_count_++;
                        $emp_function_flag = true;
                    }
                }

                // $OverAllAv = $SurveyResult->where('QuestionId', '=', $functionPractice->practiceQuestions->id)
                //     ->avg('AnswerValue');
                if ($avg_factor <= 0)
                    return ['data_size' => 0];
                $OverAllAv = ($the_three_avg) / $avg_factor;
                $practiceWeight =  round((($OverAllAv) / $scaleSize), 2);
                // $function_w += $practiceWeight;
                // $p_count_++;
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
            //get bluck of question id through function
            $practicesIDs = $function->functionPractices->pluck('id')->toArray();
            $questionsIDs = PracticeQuestions::whereIn('PracticeId', $practicesIDs)->pluck('id')->toArray();
            //get sum of this function overalll
            //get avg of this function
            $avg = $SurveyResult->whereIn('QuestionId', $questionsIDs)->avg('AnswerValue');
            $avg = round(($avg / $scaleSize), 2);
            //get sum of this function leaders
            //get avg of this function leaders
            $avgl = $SurveyResult->whereIn('QuestionId', $questionsIDs)->where('type',1)->avg('AnswerValue');
            $avgl = round(($avgl / $scaleSize), 2);
            //get sum of this function hr
            //get avg of this function hr
            $avgh = $SurveyResult->whereIn('QuestionId', $questionsIDs)->where('type', 2)->avg('AnswerValue');
            $avgh = round(($avgh / $scaleSize), 2);
            //get sum of this function employees
            //get avg of this function employees
            $avge = $SurveyResult->whereIn('QuestionId', $questionsIDs)->where('type', 3)->avg('AnswerValue');
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
            $total_answers = $prioritiesRes->where('QuestionId', $function->id)->where('AnsweredBy', $id)->sum('AnswerValue');
            $count_answers = $prioritiesRes->where('QuestionId', $function->id)->where('AnsweredBy', $id)->count();
            $priorityVal = $count_answers > 0 ? round((($total_answers / $count_answers) / 3), 2) : 0;
            $priority = ["priority" => number_format($priorityVal * 100), "function" => App()->getLocale() == 'ar' ? $function->FunctionTitleAr : $function->FunctionTitle, "function_id" => $function->id, "performance" => number_format($avgl * 100), "performancez" => number_format($avgl * 100)];
            array_push($priorities, $priority);
            $performence_ = [
                "function" => App()->getLocale() == 'ar' ? $function->FunctionTitleAr : $function->FunctionTitle,
                "function_id" => $function->id,
                "performance" => number_format((($avge + $avgh + $avgl) / $p_count_) * 100),
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
            'data_size' => 3,
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
            'Resp_overAll_res' => 3,
            'overAll_res' => 3,
            'prop_leadersResp' => 1,
            'prop_hrResp' => 1,
            'prop_empResp' => 1,
            'leaders_res' => 1,
            'hr_res' => 1,
            'emp_res' => 1,
            'leader_performences' => $leader_performences_,
            'hr_performences' => $hr_performences_,
            'type' => "Demo",
            'type_id' => 1,
            'entities' => null,
            'entity' => "Demo Test" . ' Result Company-wise'
        ];
        return view('demoUsers.result')->with($data);
    }
}
