<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Region extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'description', 'states', 'status'
    ];

    protected $casts = [
        'states' => 'array',
        'status' => 'boolean'
    ];

    public function managers()
    {
        return $this->hasMany(Manager::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    // Get all agencies in this region through employees
    public function agencies()
    {
        return Agency::whereHas('employee', function($query) {
            $query->where('region_id', $this->id);
        });
    }

    // Get all customers in this region through agencies
    public function customers()
    {
        return Customer::whereHas('agency.employee', function($query) {
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
