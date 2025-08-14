<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Region extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'description', 'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function managers()
    {
        return $this->hasMany(Manager::class);
    }

    // Get all employees in this region through managers
    public function employees()
    {
        return Employee::whereHas('manager', function($query) {
            $query->where('region_id', $this->id);
        });
    }

    // Get all agencies in this region through managers and their employees
    public function agencies()
    {
        return Agency::whereHas('employee.manager', function($query) {
            $query->where('region_id', $this->id);
        });
    }

    // Get all customers in this region through agencies
    public function customers()
    {
        return Customer::whereHas('agency.employee.manager', function($query) {
            $query->where('region_id', $this->id);
        });
    }

    protected static function booted()
    {
        static::addGlobalScope('search', function ($builder) {
            if ($search = request()->query('q')) {
                $builder->where('name', 'like', '%' . $search . '%')
                        ->orWhere('code', 'like', '%' . $search . '%');
            }
        });
    }
}
