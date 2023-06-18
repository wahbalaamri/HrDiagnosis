<?php

namespace App\Exports;

use App\Models\SurveyAnswers;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SurveyAnswersExport implements FromCollection,WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    private $id;
    public function __construct($id)
    {
        $this->id = $id;
    }
    public function collection()
    {
        $results=SurveyAnswers::where('SurveyId',$this->id)->get();
        $bigData=array();
        foreach($results as $result)
        {
            $respondent=$result->email;
            $Emplpyeetype='';
            if($respondent->EmployeeType==1)
            {
                $Emplpyeetype="Manager";
            }
            elseif($respondent->EmployeeType==2)
            {
                $Emplpyeetype="HR Team";
            }
            elseif($respondent->EmployeeType==3)
            {
                $Emplpyeetype="Employee";
            }
            else{
                $Emplpyeetype="Other";
            }
            $data=[
                'Survey'=>$result->surveys->SurveyTitle,
                'RespondentEmail'=>$respondent->Email,
                'RespondentType'=>$Emplpyeetype,
                'Function'=>$result->questions->functionPractice->functions->FunctionTitle,
                'Practice'=>$result->questions->functionPractice->PracticeTitle,
                'Question'=>$result->questions->Question,
                'AnswerValue'=>($result->AnswerValue-1)<=0?"0":($result->AnswerValue-1),
            ];
            // Log::alert($data);
            array_push($bigData,$data);

        }
        // Log::alert($bigData);
        Log::alert(collect($bigData));
        return collect($bigData);

        // return SurveyAnswers::select('SurveyId', 'QuestionId', 'AnswerValue',   'AnsweredBy')->where('SurveyId',$this->id)->get();
    }
     /**
     * Write code on Method
     *
     * @return response()
     */
    public function headings(): array
    {
        return ['Survey', 'Respondent Email','RespondentType','Function', 'Practice','Question', 'Answer Value'];
    }
}
