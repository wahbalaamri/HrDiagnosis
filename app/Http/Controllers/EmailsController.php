<?php

namespace App\Http\Controllers;

use App\Exports\RespondentsExport;
use App\Http\Requests\EmailByUploadStoreRequest;
use App\Http\Requests\EmailStoreRequest;
use App\Http\Requests\EmailUpdateRequest;
use App\Mail\SendSurvey;
use App\Models\Clients;
use App\Models\Companies;
use App\Models\Departments;
use App\Models\EmailContent;
use App\Models\Emails;
use App\Models\Sectors;
use App\Models\SurveyAnswers;
use App\Models\Surveys;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Exceptions\NoTypeDetectedException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use ImportUser;
use Symfony\Component\Console\Input\Input as InputInput;
use Yajra\DataTables\Facades\DataTables;

class EmailsController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $emails = Emails::all();
        $data = [
            'emails' => array(),
            'clients' => Clients::all(),
            'surveys' => Surveys::all(),
        ];
        return view('Emails.index')->with($data);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data = [
            'clients' => Clients::all(),
            'surveys' => Surveys::all(),
        ];
        return view('Emails.create')->with($data);
    }

    /**
     * @param \App\Http\Requests\EmailsStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(EmailStoreRequest $request)
    {
        // dd( $request->get('ClientId'));
        if ($request->Email != null || $request->Mobile != null) {

            $byEmail = $request->Email != null ? Emails::where([['SurveyId', $request->get('SurveyId')], ['Email', $request->Email]])->count() : 0;
            $byMobile = $request->Mobile != null ? Emails::where([['SurveyId', $request->get('SurveyId')], ['Mobile', $request->Mobile]])->count() : 0;
        } else {

            return redirect()->back()->withErrors(['You must provide Either Employee Email or Mobile']);
        }
        if ($byEmail > 0 || $byMobile > 0) {
            //return back with error
            return redirect()->back()->withErrors(['This Email or Mobile already Exists']);
        }
        //save new email
        $email = new Emails();
        $email->ClientId = $request->get('ClientId');
        $email->SurveyId = $request->get('SurveyId');
        $email->Email = $request->get('Email');
        $email->Mobile = $request->get('Mobile');
        $email->sector_id = $request->SectorId;
        $email->comp_id = $request->CompanyId;
        $email->EmployeeType = $request->EmployeeType;
        //department id
        // $email->dep_id = $request->get('DepartmentId');
        $email->dep_id = 0;
        $email->AddedBy = Auth()->user()->id;
        $email->save();
        return redirect()->route('clients.show', $request->get('ClientId'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param Emails $email
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Emails $email)
    {
        return view('Emails.show', compact('email'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param Emails $email
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Emails $email)
    {
        $data = [
            'email' => $email,
            'clients' => Clients::all(),
            'surveys' => Surveys::all(),
        ];
        return view('Emails.edit')->with($data);
    }

    /**
     * @param \App\Http\Requests\EmailsUpdateRequest $request
     * @param Emails $email
     * @return \Illuminate\Http\Response
     */
    public function update(EmailUpdateRequest $request, Emails $email)
    {
        $request->validated();
        //save updated email

        $email->ClientId = $request->get('ClientId');
        $email->SurveyId = $request->get('SurveyId');
        $email->Email = $request->get('Email');
        //department id
        $email->dep_id = $request->get('dep_id');
        return redirect()->route('clients.show', $email->ClientId);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param Emails $email
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Emails $email)
    {
        $id = $email->ClientId;
        $email->delete();

        return redirect()->route('clients.show', $id);
    }
    public function search(Request $request)
    {
        $ClientID = $request->get('ClientID');
        $SurveyID = $request->get('SurveyID');
        $emails = Emails::where([['ClientId', '=', $ClientID], ['SurveyId', '=', $SurveyID]])->get();
        $data = [
            'emails' => $emails,
            'clients' => Clients::all(),
            'surveys' => Surveys::all(),
        ];
        return view('Emails.index')->with($data);
    }
    public function saveUpload(EmailByUploadStoreRequest $request)
    {

        $request->validated();

        if ($request->hasFile('EmailFile')) {
            $file = $request->file('EmailFile')->getRealPath();

            // Excel::toArray([],$filePath);
            $array = array();
            // $tss = Excel::toArray($array, $request->file('EmailFile'));
            $emails = Excel::toArray([], $request->file('EmailFile'));
            foreach ($emails[0] as $key => $value) {

                if (str_contains($value[0], '@')) {
                    $email = Emails::create([
                        'ClientId' => $request->get('ClientIdU'),
                        'SurveyId' => $request->get('SurveyIdU'),
                        'Email' => $value[0],
                        'EmployeeType' => $value[1],
                        'AddedBy' => $request->AddedBy,
                    ]);
                } else {
                    continue;
                }
            }
            return redirect()->route('clients.show', $request->ClientIdU);
        } else {
            return view('Emails.create')->with('error', 'Please Upload File');
        }
    }
    public function saveUploadZ(Request $request)
    {
        if ($request->hasFile('EmailFile')) {
            // $file = $request->file('EmailFile')->getRealPath();
            // Excel::toArray([],$filePath);
            $array = array();
            // $tss = Excel::toArray($array, $request->file('EmailFile'));
            $emails = Excel::toArray([], $request->file('EmailFile'));
            foreach ($emails[0] as $key => $value) {
                //ignor table head title
                $usr_email = null;
                $emp_type = 0;
                if ($key > 0) {
                    // Log::info($value[3]);
                    if (str_contains($value[3], '@')) {
                        $usr_email = $value[3];
                    }

                    //lower case str_contains($value[5], 'employee') employee type 3
                    if (str_contains(strtolower($value[5]), 'employee')) {
                        $emp_type = 3;
                    }
                    //lower case str_contains($value[5], 'hr') employee type 2
                    elseif (str_contains(strtolower($value[5]), 'hr')) {
                        $emp_type = 2;
                    } else {
                        $emp_type = 1;
                    }
                    $company_id = null;
                    $sector_ = null;
                    $old_email = Emails::where([['Email', $usr_email], ['Mobile', $value[4]], ['ClientId', $request->get('ClientIdU')], ['SurveyId', $request->get('SurveyIdU')]])->first();
                    $sector_id = $this->AddMissingDeps(trim($value[0]), 'sec', $request->get('ClientIdU'));
                    $company_id = $this->AddMissingDeps(trim($value[1]), 'comp', $request->get('ClientIdU'), $sector_id);
                    $client = Clients::select('use_sections')->where('id', $request->ClientIdU)->first();
                    if ($client->use_sections) {
                        $dep_id = $this->AddMissingDeps(trim($value[2]), 'dep', $request->get('ClientIdU'), $sector_id, $company_id);
                        if ($sector_id != null && $company_id != null && $dep_id != null) {
                            if ($usr_email != null && $old_email != null) {
                                $old_email->ClientId = $request->get('ClientIdU');
                                $old_email->SurveyId = $request->get('SurveyIdU');
                                $old_email->Email = $usr_email;
                                $old_email->Mobile = $value[4];
                                $old_email->sector_id = $sector_id;
                                $old_email->comp_id = $company_id;
                                $old_email->dep_id = $dep_id;
                                $old_email->EmployeeType = $emp_type;
                                $old_email->save();
                            } else {

                                $email = new Emails();
                                $email->ClientId = $request->get('ClientIdU');
                                $email->SurveyId = $request->get('SurveyIdU');
                                $email->Email = $usr_email;
                                $email->Mobile = $value[4];
                                $email->sector_id = $sector_id;
                                $email->comp_id = $company_id;
                                $email->dep_id = $dep_id;
                                $email->EmployeeType = $emp_type;
                                $email->AddedBy = Auth()->user()->id;
                                $email->save();
                            }
                        }
                    } else {
                        if ($sector_ != null && $company_id != null) {
                            if ($usr_email != null && $old_email != null) {
                                $old_email->ClientId = $request->get('ClientIdU');
                                $old_email->SurveyId = $request->get('SurveyIdU');
                                $old_email->Email = $usr_email;
                                $old_email->Mobile = $value[4];
                                $old_email->sector_id = $sector_id;
                                $old_email->comp_id = $company_id;
                                $old_email->dep_id = 0;
                                $old_email->EmployeeType = $emp_type;
                                $old_email->save();
                            } else {

                                $email = new Emails();
                                $email->ClientId = $request->get('ClientIdU');
                                $email->SurveyId = $request->get('SurveyIdU');
                                $email->Email = $usr_email;
                                $email->Mobile = $value[4];
                                $email->sector_id = $sector_id;
                                $email->comp_id = $company_id;
                                $email->dep_id = 0;
                                $email->EmployeeType = $emp_type;
                                $email->AddedBy = Auth()->user()->id;
                                $email->save();
                            }
                        }
                    }
                }
            }
            return redirect()->route('clients.show', $request->ClientIdU);
        } else {
            return view('Emails.create')->with('error', 'Please Upload File');
        }
    }
    public function copy(Request $request)
    {
        $ClientID = $request->get('ClientIdC');
        $SurveyID = $request->get('SurveyIdC');
        $newSurveyID = $request->get('NewSurveyIdC');
        // dd($request->all());
        // dd('newSurveyID: '.$newSurveyID .' SurveyID: '.$SurveyID.' ClientID: '.$ClientID );
        $emails = Emails::where([['ClientId', '=', $ClientID], ['SurveyId', '=', $SurveyID]])->get();
        if ($SurveyID == $newSurveyID) {
            return redirect()->route('emails.create')->withErrors('Please Select Different Survey');
        } else {
            foreach ($emails as $key => $value) {
                $email = Emails::create([
                    'ClientId' => $ClientID,
                    'SurveyId' => $newSurveyID,
                    'Email' => $value->Email,
                    'EmployeeType' => $value->EmployeeType,
                    'AddedBy' => Auth::user()->user_type == 'superadmin' ? 0 : Auth::user()->company_id,
                ]);
            }
            return redirect()->route('clients.show', $ClientID);
        }
    }
    public function manage()
    {
        $data = [
            'emails' => EmailContent::all(),
            'clients' => Clients::all(),
            'surveys' => Surveys::all(),
        ];
        return view('Emails.manage')->with($data);
    }
    public function CreateContent(Request $request)
    {
        $data = [
            'clients' => Clients::all(),
            'surveys' => Surveys::all(),
        ];
        return view('Emails.createcontent')->with($data);
    }
    public function StoreContent(Request $request)
    {
        $request->validate([
            'client_id' => 'required',
            'survey_id' => 'required',
            'subject' => 'required',
            'body_header' => 'required',
            'body_footer' => 'required',
        ]);
        $email = new EmailContent();
        $email->client_id = $request->client_id;
        $email->survey_id = $request->survey_id;
        $email->subject = $request->subject;
        $email->body_header = $request->body_header;
        $email->body_footer = $request->body_footer;
        $email->subject_ar = $request->subject_ar;
        $email->body_header_ar = $request->body_header_ar;
        $email->body_footer_ar = $request->body_footer_ar;
        $email->save();
        return redirect()->route('emails.manage');
    }
    public function ViewContent(Request $request, $id)
    {
        $email = EmailContent::find($id);
        $data = [
            'email' => $email,
            'clients' => Clients::all(),
            'surveys' => Surveys::all(),
        ];
        return view('Emails.viewcontent')->with($data);
    }
    public function SendSurvey($id)
    {
        $emailContent = EmailContent::find($id);
        $emails = Emails::where([['ClientId', '=', $emailContent->client_id], ['SurveyId', '=', $emailContent->survey_id]])->get();
        foreach ($emails as $key => $value) {
            $data = [
                'email' => $value->Email,
                'id' => $value->id,
                'subject' => $emailContent->subject,
                'body_header' => $emailContent->body_header,
                'body_footer' => $emailContent->body_footer,
                'subject_ar' => $emailContent->subject_ar,
                'body_header_ar' => $emailContent->body_header_ar,
                'body_footer_ar' => $emailContent->body_footer_ar,
            ];
            Mail::to($value->Email)->send(new SendSurvey($data));
            sleep(2);
        }
        return redirect()->route('emails.manage');
    }
    public function sendSurveyw(Request $request, $SurveyID, $ClientID)
    {
        $data = [
            'clients' => Clients::all(),
            'surveys' => Surveys::all(),
            'sectors' => Sectors::where('client_id', $ClientID)->get(),
            'surveyId' => $SurveyID,
            'clientId' => $ClientID,
            'reminder' => 0
        ];
        return view('Emails.CreateEmail')->with($data);
    }
    public function sendTheSurvey(Request $request)
    {
        $emails = [];
        if ($request->SectorId != null || $request->SectorId != '') {
            if ($request->reminder == 2) {
                $emails = Emails::where('id', $request->respondentID)->get();
            } elseif ($request->reminder == 0)
                $emails = Emails::where([['ClientId', '=', $request->client_id], ['SurveyId', '=', $request->survey_id], ['comp_id', $request->CompanyId]])->whereNotNull('Email')->get();
            else {
                $emails = Emails::where([['ClientId', '=', $request->client_id], ['SurveyId', '=', $request->survey_id], ['comp_id', $request->CompanyId]])->whereNotNull('Email')
                    ->whereNotIn('id', SurveyAnswers::where('SurveyId', $request->survey_id)->distinct()->pluck('AnsweredBy')->ToArray())
                    ->get();
            }
        } else {
            if ($request->reminder == 2) {
                $emails = Emails::where('id', $request->respondentID)->get();
            } elseif ($request->reminder == 0)
                $emails = Emails::where([['ClientId', '=', $request->client_id], ['SurveyId', '=', $request->survey_id]])->whereNotNull('Email')->get();
            else {
                $emails = Emails::where([['ClientId', '=', $request->client_id], ['SurveyId', '=', $request->survey_id]])->whereNotNull('Email')
                    ->whereNotIn('id', SurveyAnswers::where('SurveyId', $request->survey_id)->distinct()->pluck('AnsweredBy')->ToArray())
                    ->get();
            }
        }
        // foreach ($emails as $key => $value) {
        $data = [
            //         'email' => $value->Email,
            //         'id' => $value->id,
            'subject' => $request->subject,
            'body_header' => $request->body_header,
            'body_footer' => $request->body_footer,
        ];
        $job = (new \App\Jobs\SendQueueEmail($data, $emails))
            ->delay(now()->addSeconds(2));

        dispatch($job);
        // sleep(2);
        // }
        return redirect()->route('clients.show', $request->client_id);
    }
    public function sendIndividual(Request $request, $ID)
    {
        $email = Emails::find($ID);
        $data = [
            'clients' => Clients::all(),
            'surveys' => Surveys::all(),
            'sectors' => Sectors::where('client_id', $email->ClientId)->get(),
            'surveyId' => $email->SurveyId,
            'clientId' => $email->ClientId,
            'reminder' => 2,
            'respondentID' => $email->id
        ];
        return view('Emails.CreateEmail')->with($data);
    }
    public function sendReminder(Request $request, $SurveyID, $ClientID)
    {
        $data = [
            'clients' => Clients::all(),
            'surveys' => Surveys::all(),
            'sectors' => Sectors::where('client_id', $ClientID)->get(),
            'surveyId' => $SurveyID,
            'clientId' => $ClientID,
            'reminder' => 1
        ];
        return view('Emails.CreateEmail')->with($data);
    }

    public function getEmails($ClientID, $SurveyID)
    {
        $emails = Emails::where([['ClientId', '=', $ClientID], ['SurveyId', '=', $SurveyID]])->whereNotNull('Email')->get();
        //datatable
        return DataTables::of($emails)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '<a href="' . route('emails.edit', $row->id) . '" class="btn btn-sm m-1 btn-primary"><i class="fa fa-edit"></i></a>';
                $btn .= '<form action="' . route('emails.destroy', $row->id) . '" method="POST" class="delete_form" style="display:inline">';
                $btn .= '<input type="hidden" name="_method" value="DELETE">';
                $btn .= csrf_field();
                $btn .= '<button type="submit" class="btn btn-danger btn-sm m-1"><i class="fa fa-trash"></i></button>';
                $btn .= '</form>';
                return $btn;
            })
            ->addColumn('SendSurvey', function ($row) {

                $btn = '<a href="' . route('emails.sendIndividual', $row->id) . '" class="btn btn-sm m-1 btn-primary">Send Individually</a>';

                return $btn;
            })
            ->editColumn('EmployeeType', function ($row) {
                switch ($row->EmployeeType) {
                    case 3:
                        return 'Employee';
                    case 1:
                        return 'Manager';
                    case 2:
                        return 'HR Team';
                    default:
                        return 'Others';
                }
            })
            ->rawColumns(['SendSurvey', 'action'])
            ->make(true);
    }
    public function CreateNewEmails($ClientID, $SurveyID)
    {
        $data = [
            'sectors' => Sectors::where('client_id', $ClientID)->get(),
            'clients' => Clients::all(),
            'surveys' => Surveys::where('ClientId', '=', $ClientID)->get(),
            'surveyId' => $SurveyID,
            'clientId' => $ClientID,
        ];
        return view('Emails.create')->with($data);
    }
    function ExportEmails($client_id, $survey_id)
    {
        return Excel::download(new RespondentsExport($client_id, $survey_id), 'Respondents.xlsx');
    }
    private function AddMissingDeps($name, $type, $client, $sec = null, $comp = null)
    {
        $return_id = 0;
        switch ($type) {
            case 'sec': {
                    //find sector by sector_name_en
                    $sector = Sectors::where('sector_name_en', "LIKE", "%" . $name . "%")->where('client_id', $client)->first();
                    if ($sector) {
                        $return_id = $sector->id;
                    } else {
                        $newSector = new Sectors();
                        $newSector->client_id = $client;
                        $newSector->sector_name_en = $name;
                        $newSector->sector_name_ar = $name;
                        $newSector->save();
                        $return_id = $newSector->id;
                    }
                }
                break;
            case 'comp': {
                    $company = Companies::where('sector_id', $sec)->where('company_name_en', 'like', '%' . $name . '%')->first();
                    if ($company) {
                        $return_id = $company->id;
                    } else {
                        $newCompany = new Companies();
                        $newCompany->sector_id = $sec;
                        $newCompany->company_name_en = $name;
                        $newCompany->company_name_ar = $name;
                        $newCompany->save();
                        $return_id = $newCompany->id;
                    }
                }
                break;
            case 'dep': {
                    $department = Departments::where('company_id', $comp)->where("dep_name_en", "LIKE", "%" . $name . "%")->first();
                    if ($department) {
                        $return_id = $department->id;
                    } else {
                        $newDepartment = new Departments();
                        $newDepartment->dep_name_en = $name;
                        $newDepartment->dep_name_ar = $name;
                        $newDepartment->company_id = $comp;
                        $newDepartment->save();
                        $return_id = $newDepartment->id;
                    }
                }
                break;
        }
        return $return_id;
    }
}
