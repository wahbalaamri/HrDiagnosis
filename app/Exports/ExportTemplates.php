<?php

namespace App\Exports;

use App\Models\Sectors;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportTemplates implements FromCollection, WithHeadings
{
    private $client_id;
    function __construct($client_id)
    {
        $this->client_id = $client_id;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        //
        foreach (Sectors::where('client_id', $this->client_id)->get() as $sector) {
            foreach ($sector->companies as $company) {

                $data[] = [
                    'sector_name_en' => $sector->sector_name_en,
                    'company_name_en' => $company->company_name_en,
                    'Employee_Name' => "Here should be the employee's name",
                    'Employee_Email' => "Here should be the employee's email",
                    'Employee_Mobile' => "Here should be the employee's mobile number",
                    'Employee_Type' => "Here should be the employees type [1 is for Managers , 2 is for HR team, 3 for Normal Employees]",
                    'Comment' => 'You can repeat this row every employee in every company in different sector'
                ];
            }
        }
        return collect($data);
    }
    public function headings(): array
    {
        return [
            'Sector Name',
            'Company Name',
            'Employee Name',
            'Employee Email',
            'Employee Mobile',
            'Employee Type',
            'Comment'
        ];
    }
}
