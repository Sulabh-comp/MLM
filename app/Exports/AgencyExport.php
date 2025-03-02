<?php

namespace App\Exports;

use App\Models\Agency;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AgencyExport implements FromCollection, WithHeadings, WithMapping
{
    public $model;

    public function __construct($model = 'admin')
    {
        $this->model = $model;
    }

    public function collection()
    {
        if($this->model == 'employee') {
            return Agency::with('employee')->where('employee_id', auth('employee')->id())->get();
        }
        return Agency::with('employee')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Agency Name',
            'Email',
            'Phone',
            'Address',
            'Employee',
            'Created At'
        ];
    }

    public function map($agency): array
    {
        return [
            $agency->id,
            $agency->name,
            $agency->email,
            $agency->phone,
            $agency->address,
            $agency->employee->name ?? 'N/A', // Handle null relationship
            $agency->created_at->format('Y-m-d H:i:s')
        ];
    }
}