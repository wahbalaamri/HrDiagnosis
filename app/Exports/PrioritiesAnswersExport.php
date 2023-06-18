<?php

namespace App\Exports;

use App\Models\Emails;
use App\Models\Functions;
use App\Models\PrioritiesAnswers;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PrioritiesAnswersExport implements FromCollection , WithHeadings
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

        $result= PrioritiesAnswers::select("SurveyId" , "QuestionId" ,"AnswerValue" ,  "AnsweredBy" )->where('SurveyId',$this->id)->get();
        $bigData=array();

        foreach($result as $res)
        {
            $data=[
                'SurveyId'=>$res->SurveyId,
                'QuestionId'=>Functions::find($res->QuestionId)->FunctionTitle,
                'AnswerValue'=>$res->AnswerValue,
                'AnsweredBy'=>Emails::find($res->AnsweredBy)->Email,
            ];
            array_push($bigData,$data);
        }
        return collect($bigData);
    }
      /**
     * Write code on Method
     *
     * @return response()
     */
    public function headings(): array
    {
        return ['Survey Id', 'Question', 'Answer Value',   'Answered By'];
    }
}
