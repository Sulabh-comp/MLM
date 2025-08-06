<?php

namespace App\Exports;

use App\Models\FamilyMember;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FamilyMemberExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $customerId;
    protected $filters;

    public function __construct($customerId = null, $filters = [])
    {
        $this->customerId = $customerId;
        $this->filters = $filters;
    }

    public function collection()
    {
        if ($this->customerId) {
            // Export for specific customer
            return FamilyMember::with('customer')->where('customer_id', $this->customerId)->get();
        } else {
            // Export for filtered customers
            $query = FamilyMember::with('customer.agency')
                ->whereHas('customer', function ($q) {
                    if (!empty($this->filters['agency_id'])) {
                        $q->where('agency_id', $this->filters['agency_id']);
                    }
                    if (!empty($this->filters['status'])) {
                        $q->where('status', $this->filters['status']);
                    }
                    if (!empty($this->filters['gender'])) {
                        $q->where('gender', $this->filters['gender']);
                    }
                    if (!empty($this->filters['state'])) {
                        $q->where('state', $this->filters['state']);
                    }
                    if (!empty($this->filters['city'])) {
                        $q->where('city', $this->filters['city']);
                    }
                    if (!empty($this->filters['religion'])) {
                        $q->where('religion', $this->filters['religion']);
                    }
                    if (!empty($this->filters['created_from'])) {
                        $q->whereDate('created_at', '>=', $this->filters['created_from']);
                    }
                    if (!empty($this->filters['created_to'])) {
                        $q->whereDate('created_at', '<=', $this->filters['created_to']);
                    }
                    if (!empty($this->filters['search'])) {
                        $search = $this->filters['search'];
                        $q->where(function($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%")
                                  ->orWhere('email', 'like', "%{$search}%")
                                  ->orWhere('mobile', 'like', "%{$search}%");
                        });
                    }
                });
            
            return $query->get();
        }
    }

    public function headings(): array
    {
        return [
            'ID',
            'Family Member Code',
            'Customer Code',
            'Customer Name',
            'Customer Email',
            'Member Name',
            'Position/Relationship',
            'Age',
            'Gender',
            'Occupation',
            'Contact Number',
            'Monthly Income',
            'Health Status',
            'Disease Name',
            'Medicine Expenses',
            'Medicine Name',
            'Doctor Name',
            'Has Skills',
            'Skill Name',
            'Institute Certified',
            'Year of Passing',
            'Degree/Course',
            'Professional Courses',
            'Course Name',
            'Institute Name',
            'Work City',
            'Looking for Opportunity',
            'Interested in MLM',
            'Sales & Marketing',
            'Partner Commission Work',
            'Manufacturing Work',
            'Commission Work',
            'Created At',
            'Updated At'
        ];
    }

    public function map($familyMember): array
    {
        return [
            $familyMember->id,
            $familyMember->code ?? 'N/A',
            $familyMember->customer->code ?? 'N/A',
            $familyMember->customer->first_name . ' ' . $familyMember->customer->last_name,
            $familyMember->customer->email,
            $familyMember->name ?? 'N/A',
            $familyMember->position ?? 'N/A',
            $familyMember->age ?? 'N/A',
            $familyMember->gender ?? 'N/A',
            $familyMember->occupation ?? 'N/A',
            $familyMember->contact_number ?? 'N/A',
            $familyMember->monthly_income ?? 'N/A',
            $this->formatHealthStatus($familyMember->health_status),
            $familyMember->disease_name ?? 'N/A',
            $familyMember->medicine_expenses ?? 'N/A',
            $familyMember->medicine_name ?? 'N/A',
            $familyMember->doctor_name ?? 'N/A',
            $this->formatYesNo($familyMember->skill_knowledge),
            $familyMember->skill_name ?? 'N/A',
            $familyMember->institute_certified ?? 'N/A',
            $familyMember->year_of_passing ?? 'N/A',
            $familyMember->degree_course ?? 'N/A',
            $familyMember->professional_courses ?? 'N/A',
            $familyMember->course_name ?? 'N/A',
            $familyMember->institute_name ?? 'N/A',
            $familyMember->work_city ?? 'N/A',
            $this->formatYesNo($familyMember->looking_for_opportunity),
            $this->formatYesNo($familyMember->mlm),
            $this->formatYesNo($familyMember->sales_marketing),
            $this->formatYesNo($familyMember->partner_commission_work),
            $this->formatYesNo($familyMember->manufacturing_work),
            $this->formatYesNo($familyMember->commission_work),
            $familyMember->created_at->format('Y-m-d H:i:s'),
            $familyMember->updated_at->format('Y-m-d H:i:s')
        ];
    }

    private function formatYesNo($value)
    {
        if ($value === null) return 'N/A';
        return $value ? 'Yes' : 'No';
    }

    private function formatHealthStatus($status)
    {
        if ($status === null) return 'N/A';
        
        switch ($status) {
            case 1:
                return 'Good';
            case 2:
                return 'Fair';
            case 3:
                return 'Poor';
            default:
                return 'N/A';
        }
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }
}
