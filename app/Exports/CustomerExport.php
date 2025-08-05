<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomerExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Customer::with(['agency', 'familyMembers']);

        // Apply the same filters as in the controller
        if (!empty($this->filters['agency_id'])) {
            $query->where('agency_id', $this->filters['agency_id']);
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['state'])) {
            $query->where('state', $this->filters['state']);
        }

        if (!empty($this->filters['city'])) {
            $query->where('city', $this->filters['city']);
        }

        if (!empty($this->filters['gender'])) {
            $query->where('gender', $this->filters['gender']);
        }

        if (!empty($this->filters['religion'])) {
            $query->where('religion', $this->filters['religion']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        if (!empty($this->filters['q'])) {
            $query->where(function($q) {
                $q->where('first_name', 'like', '%' . $this->filters['q'] . '%')
                  ->orWhere('last_name', 'like', '%' . $this->filters['q'] . '%')
                  ->orWhere('email', 'like', '%' . $this->filters['q'] . '%')
                  ->orWhere('phone', 'like', '%' . $this->filters['q'] . '%');
            });
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Sponsor Code',
            'First Name',
            'Last Name',
            'Full Name',
            'Email',
            'Phone',
            'Mobile',
            'Address 1',
            'Address 2',
            'City',
            'State',
            'PIN Code',
            'Country',
            'Religion',
            'Date of Birth',
            'Gender',
            'Aadhar Number',
            'Agency Name',
            'Agency Email',
            'Family Members Count',
            'Status',
            'Created At',
            'Updated At'
        ];
    }

    public function map($customer): array
    {
        return [
            $customer->id,
            $customer->sponcer_code ?? 'N/A',
            $customer->first_name,
            $customer->last_name,
            $customer->first_name . ' ' . $customer->last_name,
            $customer->email,
            $customer->phone,
            $customer->mobile ?? 'N/A',
            $customer->address_1,
            $customer->address_2 ?? 'N/A',
            $customer->city,
            $customer->state,
            $customer->pin,
            $customer->country,
            $customer->religion ?? 'N/A',
            $customer->dob ? $customer->dob : 'N/A',
            $customer->gender ?? 'N/A',
            $customer->adhar_number ?? 'N/A',
            $customer->agency->name ?? 'N/A',
            $customer->agency->email ?? 'N/A',
            $customer->familyMembers->count(),
            $customer->status ? 'Active' : 'Inactive',
            $customer->created_at->format('Y-m-d H:i:s'),
            $customer->updated_at->format('Y-m-d H:i:s')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }
}