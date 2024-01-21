<?php

namespace App\Exports;

use App\Models\Companies;
use App\Models\Emails;
use App\Models\OpenEndedQuestions;
use App\Models\OpenEndedQuestionsAnswers;
use App\Models\PracticeQuestions;
use App\Models\SurveyAnswers;
use App\Models\Surveys;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class SurveyAnswersExport implements FromCollection, WithHeadings, WithChunkReading
{
    /**
     * @return \Illuminate\Support\Collection
     */
    private $id;
    private $type;
    private $type_id;
    private $ans;
    public function __construct($id, $type, $type_id)
    {
        $this->id = $id;
        $this->type = $type;
        $this->type_id = $type_id;
    }
    public function collection()
    {
        $exportData = [];
        $surveyAnswers = null;
        $Emails = null;
        if ($this->type == 'all') {
            $Emails = DB::table('emails')
                ->distinct()
                ->join('survey_answers', 'emails.id', '=', 'survey_answers.AnsweredBy')
                ->where('emails.SurveyId', '=', $this->id)
                ->select(['emails.id', 'emails.comp_id', 'emails.sector_id', 'emails.EmployeeType'])
                ->get();
            // $Emails = Emails::select(['id', 'comp_id','sector_id','age_generation','gender'])->where('SurveyId', $this->id)->get();


        } else if ($this->type == 'comp') {
            //get emails where $emails exist in SurveyAnswers
            // $Emails = Emails::select(['id', 'comp_id','sector_id','age_generation','gender'])->where('SurveyId', $this->id)->where('comp_id', $this->type_id)->get();
            $Emails = DB::table('emails')
                ->distinct()
                ->join('survey_answers', 'emails.id', '=', 'survey_answers.AnsweredBy')
                ->where([['emails.SurveyId', '=', $this->id], ['comp_id', $this->type_id]])
                ->select(['emails.id', 'emails.comp_id', 'emails.sector_id', 'emails.EmployeeType'])
                ->get();
        }
        //sector
        else if ($this->type == 'sec') {
            //get emails where $emails exist in SurveyAnswers
            // $Emails = Emails::select(['id', 'comp_id','sector_id','age_generation','gender'])->where('SurveyId', $this->id)->where('sector_id', $this->type_id)->get();
            $Emails = DB::table('emails')
                ->distinct()
                ->join('survey_answers', 'emails.id', '=', 'survey_answers.AnsweredBy')
                ->where([['emails.SurveyId', '=', $this->id], ['sector_id', $this->type_id]])
                ->select(['emails.id', 'emails.comp_id', 'emails.sector_id', 'emails.EmployeeType'])
                ->get();
        }

        $count = 0;
        $index = 0;
        $temp_AnsweredBy = null;
        //chunck the Emails collection
        // $EmailsColl=$Emails->chunck(150);
        // dd($Emails);
        $EmailsChunk = $Emails->chunk(100);
        foreach ($Emails as $email) {
            $count = 0;
            $emp_type = '';
            if ($email->EmployeeType == 1)
                $emp_type = 'Manager';
            //if 2 HR Team
            else if ($email->EmployeeType == 2)
                $emp_type = 'HR Team';
            //if 3 Employee
            else if ($email->EmployeeType == 3)
                $emp_type = 'Employee';
            $surveyAnswers = SurveyAnswers::where('SurveyId', $this->id)->where('AnsweredBy', $email->id)->get();
            if ($surveyAnswers->count() > 0) {

                $company = Companies::find($email->comp_id);
                $exportData[] = [
                    'Survey Id' => $this->id,
                    'Answered By' => $email->id,
                    'Company' => $company != null ? $company->company_name_en : "",
                    'Respondent Type' => $emp_type
                ];
                // foreach ($surveyAnswers as $surveyAnswer) {
                $count = 4;
                $indx = 4;
                $functions = Surveys::find($this->id)->plan->functions;

                foreach ($functions as $function) {

                    foreach ($function->functionPractices as $practce) {

                        $questionTitle  =  $function->FunctionTitle;

                        //get answervalue
                        $answerValue = SurveyAnswers::where('SurveyId', $this->id)->where('QuestionId', $practce->practiceQuestions->id)->where('AnsweredBy', $email->id)->first();
                        //push answerValue into eFunctionTitlexportData
                        if ($answerValue != null) {

                            //add this value to next column

                            $exportData[$index][$questionTitle . '-Q-' . $indx++] = ($answerValue->AnswerValue - 1) == 0 ? "0" : ($answerValue->AnswerValue - 1);
                            $count++;
                        } else {
                            $exportData[$index][$questionTitle . '-Q-' . $indx++] = '-';
                            $count++;
                        }
                    }
                }
                // }

                $index++;
            }
        }

        return collect($exportData);
        // return SurveyAnswers::select('SurveyId', 'QuestionId', 'AnswerValue',   'AnsweredBy')->where('SurveyId',$this->id)->get();
    }
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function headings(): array
    {
        // $heading = ['Survey Id', 'Function ID', 'Function Title', 'Is Driver Function', 'Question Id', 'Question Title', 'Question is eNPS', 'Answer Value',   'Answered By'];
        $heading = [
            'Survey Id', 'Answered By',
            'Company', 'Respondent Type'
        ];
        $functions = Surveys::find($this->id)->plan->functions;
        //get functions
        $index = 1;
        foreach ($functions as $function) {
            $questionTitle = "";
            foreach ($function->functionPractices as $practce) {

                $questionTitle  =  $function->FunctionTitle;
                //get loop iteration

                $heading[] = $questionTitle . '-Q-' . $index;
                $index++;
            }
        }
        //add new Item to heading
        // $open_end_questions = OpenEndedQuestions::select('question')->where('survey_id', $this->id)->get();
        // foreach ($open_end_questions as $open_end_question) {
        //     $heading[] = $open_end_question->question;
        // }
        return $heading;
    }
    public function registerEvents(): array
    {

        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->setCellValue('A' . (count($this->ans) + 3), 'Average:');
                $event->sheet->setCellValue('B' . (count($this->ans) + 3), 'Percentage:');
            }
        ];
    }
    public function chunkSize(): int
    {
        return 80; // Set the desired chunk size
    }
}
