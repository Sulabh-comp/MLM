<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerExport implements FromCollection, WithHeadings, WithMapping
{
    public $model;

    public function __construct($model = 'admin')
    {
        $this->model = $model;
    }

    public function collection()
    {
        if($this->model == 'employee') {
            return Customer::with('employee')->whereHas('agency', function($query) {
                $query->where('employee_id', auth('employee')->id());
            })->get();
        } elseif($this->model == 'agency') {
            return Customer::with('employee')->where('agency_id', auth('agency')->id())->get();
        }
        return Customer::with('employee')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Customer Name',
            'Email',
            'Phone',
            'Address',
            'Agency',
            'Total Family Members',
            'Created At',
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
            $agency->agency->name ?? 'N/A', // Handle null relationship
            $agency->family_member->count(),
            $agency->created_at->format('Y-m-d H:i:s')
        ];
    }
}