<?php

namespace App\Exports;

use App\Models\Emails;
use App\Models\PrioritiesAnswers;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PrioritiesAnswersExport implements FromCollection , WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $id;
    private $type;
    private $type_id;
    public function __construct($id,$type,$type_id)
    {
        $this->id = $id;
        $this->type = $type;
        $this->type_id = $type_id;
    }
    public function collection()
    {
        if ($this->type == 'all') {
          //bluck id from emails
          $ids=Emails::where('SurveyId',$this->id)->pluck('id')->all();
        } else if ($this->type == 'comp') {
            //get emails where $emails exist in SurveyAnswers
            $ids=Emails::where('SurveyId',$this->id)->where('comp_id',$this->type_id)->pluck('id')->all();
        }
        //sector
        else if ($this->type == 'sec') {
            $ids=Emails::where('SurveyId',$this->id)->where('sector_id',$this->type_id)->pluck('id')->all();
        }

        return PrioritiesAnswers::select("SurveyId" , "QuestionId" ,"AnswerValue" ,  "AnsweredBy" )->where('SurveyId',$this->id)->whereIn("AnsweredBy",$ids)->get();
    }
      /**
     * Write code on Method
     *
     * @return response()
     */
    public function headings(): array
    {
        return ['Survey Id', 'Question Id', 'Answer Value',   'Answered By'];
    }
}
